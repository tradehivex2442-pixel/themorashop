<?php
// ============================================================
// THEMORA SHOP — Cart Controller
// ============================================================

namespace App\Controllers\Shop;

use App\Core\{Controller, Request, Database, Response, Session};

class CartController extends Controller
{
    public function index(Request $req): void
    {
        $cartItems = $this->getCartProducts();
        $subtotal  = array_sum(array_column($cartItems, 'effective_price'));

        $discount = 0;
        $coupon = Session::get('coupon');
        if ($coupon) {
            if ($coupon['type'] === 'percent') {
                $discount = $subtotal * ((float)$coupon['value'] / 100);
            } else {
                $discount = (float)$coupon['value'];
                // Ensure discount doesn't exceed subtotal
                if ($discount > $subtotal) $discount = $subtotal;
            }
        }

        $afterDiscount = max(0, $subtotal - $discount);
        $taxRate = (float)setting('tax_rate', '0');
        $tax = $afterDiscount * ($taxRate / 100);
        $total = $afterDiscount + $tax;

        $this->view('user/cart', [
            'title'         => 'Cart — ' . setting('site_name'),
            'cartItems'     => $cartItems,
            'subtotal'      => $subtotal,
            'discount'      => $discount,
            'tax'           => $tax,
            'total'         => $total,
            'appliedCoupon' => $coupon,
        ]);
    }

    public function add(Request $req): void
    {
        $productId = (int)$req->post('product_id');
        $product   = Database::fetchOne('SELECT id FROM products WHERE id=? AND status="active"', [$productId]);

        if (!$product) {
            $req->isAjax()
                ? Response::error('Product not found', 404)
                : (flash_error('Product not found.') | $this->back());
        }

        $cart   = Session::get('cart', []);
        $exists = in_array($productId, $cart);

        if (!$exists) {
            $cart[] = $productId;
            Session::set('cart', $cart);
        }

        if ($req->isAjax()) {
            Response::success($exists ? 'Already in cart' : 'Added to cart', ['count' => count($cart)]);
        }

        flash_success('Added to cart!');
        $this->redirect(url('cart'));
    }

    public function buyNow(Request $req): void
    {
        $productId = (int)$req->post('product_id');
        $product   = Database::fetchOne('SELECT id FROM products WHERE id=? AND status="active"', [$productId]);

        if (!$product) {
            flash_error('Product not found.');
            $this->redirect(url('/'));
        }

        // Set cart to ONLY this product for "Buy Now"
        Session::set('cart', [$productId]);
        $this->redirect(url('checkout'));
    }

    public function remove(Request $req): void
    {
        $productId = (int)$req->post('product_id');
        $cart      = Session::get('cart', []);
        $cart      = array_values(array_filter($cart, fn($id) => $id !== $productId));
        Session::set('cart', $cart);

        if ($req->isAjax()) {
            Response::success('Removed', ['count' => count($cart)]);
        }

        flash_success('Item removed.');
        $this->redirect(url('cart'));
    }

    public function clear(Request $req): void
    {
        Session::forget('cart');
        Session::forget('coupon');
        if ($req->isAjax()) Response::success('Cart cleared');
        $this->redirect(url('cart'));
    }

    private function getCartProducts(): array
    {
        $ids = Session::get('cart', []);
        if (empty($ids)) return [];

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $products = Database::fetchAll(
            "SELECT p.*, c.name as category_name FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.id IN ({$placeholders})",
            $ids
        );

        // Apply flash sale prices
        $now = date('Y-m-d H:i:s');
        foreach ($products as &$p) {
            $p['effective_price'] = (!empty($p['flash_sale_price']) && !empty($p['flash_sale_ends']) && $p['flash_sale_ends'] > $now)
                ? $p['flash_sale_price']
                : $p['price'];
        }

        return $products;
    }
}
