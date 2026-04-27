<?php
// ============================================================
// THEMORA SHOP — Checkout Controller
// ============================================================

namespace App\Controllers\Shop;

use App\Core\{Controller, Request, Database, Session, Response};

class CheckoutController extends Controller
{
    public function index(Request $req): void
    {
        $ids = Session::get('cart', []);
        if (empty($ids)) { $this->redirect(url('cart')); }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $products = Database::fetchAll(
            "SELECT * FROM products WHERE id IN ({$placeholders}) AND status='active'", $ids
        );

        $now = date('Y-m-d H:i:s');
        $subtotal = 0;
        foreach ($products as &$p) {
            $p['effective_price'] = (!empty($p['flash_sale_price']) && !empty($p['flash_sale_ends']) && $p['flash_sale_ends'] > $now)
                ? $p['flash_sale_price'] : $p['price'];
            $subtotal += $p['effective_price'];
        }

        // Bundle discount
        $bundleDiscount = 0;
        if (setting('bundle_discount_enabled') === '1' && count($products) >= (int)setting('bundle_min_items', '2')) {
            $bundleDiscount = round($subtotal * (float)setting('bundle_discount_percent', '10') / 100, 2);
        }

        // Coupon
        $coupon         = Session::get('coupon');
        $couponDiscount = 0;
        if ($coupon) {
            $couponDiscount = $coupon['type'] === 'percent'
                ? round($subtotal * $coupon['value'] / 100, 2)
                : (float)$coupon['value'];
        }

        $discountTotal = $bundleDiscount + $couponDiscount;
        $taxRate       = (float)setting('gst_rate', '18') / 100;
        $taxable       = max(0, $subtotal - $discountTotal);
        $tax           = round($taxable * $taxRate, 2);
        $total         = $taxable + $tax;

        $gateways = [];
        if (setting('razorpay_enabled') === '1') $gateways[] = 'razorpay';
        if (setting('stripe_enabled') === '1')   $gateways[] = 'stripe';
        if (setting('paypal_enabled') === '1')   $gateways[] = 'paypal';
        if (setting('upi_enabled') === '1')      $gateways[] = 'upi';

        $this->view('user/checkout', [
            'title'          => 'Checkout — ' . setting('site_name'),
            'products'       => $products,
            'subtotal'       => $subtotal,
            'bundleDiscount' => $bundleDiscount,
            'couponDiscount' => $couponDiscount,
            'discount'       => $discountTotal,
            'coupon'         => $coupon,
            'tax'            => $tax,
            'taxRate'        => $taxRate * 100,
            'taxLabel'       => setting('tax_label', 'GST'),
            'total'          => $total,
            'gateways'       => $gateways,
            'razorpayKey'    => env('RAZORPAY_KEY_ID'),
            'stripeKey'      => env('STRIPE_PUBLISHABLE_KEY'),
        ]);
    }

    public function applyCoupon(Request $req): void
    {
        $code = strtoupper(trim($req->post('code', '')));
        $coupon = Database::fetchOne(
            'SELECT * FROM coupons WHERE code=? AND is_active=1 AND (expiry IS NULL OR expiry > NOW()) AND (max_uses=0 OR used_count < max_uses)',
            [$code]
        );

        if (!$coupon) {
            Response::error('Invalid or expired coupon code.');
        }

        Session::set('coupon', [
            'id'    => $coupon['id'],
            'code'  => $coupon['code'],
            'type'  => $coupon['type'],
            'value' => $coupon['value'],
        ]);

        Response::success("Coupon <strong>{$code}</strong> applied!", ['coupon' => $coupon]);
    }

    public function process(Request $req): void
    {
        if (!verify_csrf()) {
            flash_error('Security token mismatch.');
            $this->redirect(url('checkout'));
        }

        $ids = Session::get('cart', []);
        if (empty($ids)) { $this->redirect(url('cart')); }

        $gateway  = $req->post('gateway', 'razorpay');
        $fName    = trim($req->post('first_name', ''));
        $lName    = trim($req->post('last_name', ''));
        $name     = trim($fName . ' ' . $lName);
        $email    = trim($req->post('email', ''));
        $txnId    = $req->post('transaction_id', '');

        if (!$fName || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash_error('Please fill in your name and a valid email.');
            $this->redirect(url('checkout'));
        }

        // Recalculate totals
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $products = Database::fetchAll("SELECT * FROM products WHERE id IN ({$placeholders})", $ids);
        $now      = date('Y-m-d H:i:s');
        $subtotal = 0;
        foreach ($products as $p) {
            $subtotal += (!empty($p['flash_sale_price']) && !empty($p['flash_sale_ends']) && $p['flash_sale_ends'] > $now)
                ? $p['flash_sale_price'] : $p['price'];
        }

        $coupon         = Session::get('coupon');
        $couponDiscount = 0;
        $couponId       = null;
        if ($coupon) {
            $couponId       = $coupon['id'];
            $couponDiscount = $coupon['type'] === 'percent'
                ? round($subtotal * $coupon['value'] / 100, 2)
                : (float)$coupon['value'];
        }

        $bundleDiscount = 0;
        if (setting('bundle_discount_enabled') === '1' && count($products) >= (int)setting('bundle_min_items', '2')) {
            $bundleDiscount = round($subtotal * (float)setting('bundle_discount_percent', '10') / 100, 2);
        }

        $discount = $couponDiscount + $bundleDiscount;
        $taxable  = max(0, $subtotal - $discount);
        $tax      = round($taxable * (float)setting('gst_rate', '18') / 100, 2);
        $total    = $taxable + $tax;

        $userId = logged_in() ? auth()['id'] : null;

        Database::beginTransaction();
        try {
            $orderId = Database::insert(
                'INSERT INTO orders (user_id, guest_email, guest_name, subtotal, discount, tax, total, coupon_id, payment_gateway, transaction_id, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "paid")',
                [$userId, $email, $name, $subtotal, $discount, $tax, $total, $couponId, $gateway, $txnId]
            );

            // Create order items with download tokens
            $expiryHours = (int)setting('download_expiry_hours', '48');
            $maxDl       = (int)setting('default_download_limit', '5');
            $expires     = date('Y-m-d H:i:s', strtotime("+{$expiryHours} hours"));

            foreach ($products as $p) {
                $price   = (!empty($p['flash_sale_price']) && !empty($p['flash_sale_ends']) && $p['flash_sale_ends'] > $now) ? $p['flash_sale_price'] : $p['price'];
                $token   = generate_token();
                Database::execute(
                    'INSERT INTO order_items (order_id, product_id, price, download_token, max_downloads, token_expires)
                     VALUES (?, ?, ?, ?, ?, ?)',
                    [$orderId, $p['id'], $price, $token, $maxDl, $expires]
                );
                // Increment product sales
                Database::execute('UPDATE products SET total_sales = total_sales + 1 WHERE id = ?', [$p['id']]);
            }

            // Increment coupon usage
            if ($couponId) {
                Database::execute('UPDATE coupons SET used_count = used_count + 1 WHERE id = ?', [$couponId]);
            }

            // Affiliate commission
            if ($userId) {
                $user = Database::fetchOne('SELECT referred_by FROM users WHERE id=?', [$userId]);
                if ($user['referred_by']) {
                    $aff = Database::fetchOne('SELECT * FROM affiliates WHERE user_id=?', [$user['referred_by']]);
                    if ($aff) {
                        $commission = round($total * $aff['commission_rate'] / 100, 2);
                        Database::execute('UPDATE affiliates SET total_earnings=total_earnings+?, pending_earnings=pending_earnings+?, total_conversions=total_conversions+1 WHERE id=?', [$commission, $commission, $aff['id']]);
                        Database::execute('INSERT INTO affiliate_conversions (affiliate_id, order_id, commission) VALUES (?,?,?)', [$aff['id'], $orderId, $commission]);
                    }
                }
            }

            Database::commit();

            // Clear cart/coupon
            Session::forget('cart');
            Session::forget('coupon');
            Session::set('last_order_id', $orderId);

            $this->redirect(url('checkout/success'));

        } catch (\Throwable $e) {
            Database::rollback();
            error_log($e->getMessage());
            flash_error('Order failed. Please try again.');
            $this->redirect(url('checkout'));
        }
    }

    public function orderSuccess(Request $req): void
    {
        $orderId = Session::get('last_order_id');
        if (!$orderId) { $this->redirect(url('/')); }

        $order = Database::fetchOne('SELECT * FROM orders WHERE id=?', [$orderId]);
        $items = Database::fetchAll(
            'SELECT oi.*, p.title, p.thumbnail FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE oi.order_id=?',
            [$orderId]
        );

        foreach ($items as &$item) {
            $item['download_url'] = signed_download_url($item['download_token']);
        }

        $this->view('user/order-success', [
            'title' => 'Order Confirmed — ' . setting('site_name'),
            'order' => $order,
            'items' => $items,
        ]);
    }
}
