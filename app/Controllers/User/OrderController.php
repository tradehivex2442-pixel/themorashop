<?php
// ============================================================
// THEMORA SHOP — User Order/Dashboard Controller
// ============================================================
namespace App\Controllers\User;
use App\Core\{Controller, Request, Database};

class OrderController extends Controller
{
    public function dashboard(Request $req): void
    {
        $this->requireAuth();
        $user   = auth();
        $orders = Database::fetchAll('SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC LIMIT 5', [$user['id']]);
        $wishlistCount = Database::fetchOne('SELECT COUNT(*) as c FROM wishlist WHERE user_id=?', [$user['id']])['c'] ?? 0;
        $affiliate = Database::fetchOne('SELECT * FROM affiliates WHERE user_id=?', [$user['id']]);
        $tickets   = Database::fetchAll('SELECT * FROM tickets WHERE user_id=? ORDER BY created_at DESC LIMIT 3', [$user['id']]);
        $this->view('user/account/dashboard', ['title' => 'My Dashboard', 'orders' => $orders, 'wishlistCount' => $wishlistCount, 'affiliate' => $affiliate, 'tickets' => $tickets]);
    }

    public function orders(Request $req): void
    {
        $this->requireAuth();
        $user_id = auth()['id'];
        $page    = max(1, (int)$req->get('page', 1));
        $perPage = 10;
        $offset  = ($page - 1) * $perPage;

        $totalSql = 'SELECT COUNT(*) as cnt FROM orders WHERE user_id=?';
        $total    = Database::fetchOne($totalSql, [$user_id])['cnt'] ?? 0;

        $ordersSql = "SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}";
        $orders    = Database::fetchAll($ordersSql, [$user_id]);

        // Fix: Fetch items for each order so they show up in the view
        foreach ($orders as &$o) {
            $o['items'] = Database::fetchAll(
                "SELECT oi.*, p.title, p.thumbnail 
                 FROM order_items oi 
                 JOIN products p ON p.id = oi.product_id 
                 WHERE oi.order_id = ?",
                [$o['id']]
            );
        }

        $result = $this->paginate($orders, (int)$total, $perPage);
        $this->view('user/account/orders', ['title' => 'My Orders', 'orders' => $result['items'], 'pagination' => $result]);
    }

    public function orderDetail(Request $req): void
    {
        $this->requireAuth();
        $order = Database::fetchOne('SELECT * FROM orders WHERE id=? AND user_id=?', [$req->param('id'), auth()['id']]);
        if (!$order) {
            $this->flashError('Order not found.');
            $this->redirect(url('dashboard/orders'));
        }
        $items = Database::fetchAll('SELECT oi.*, p.title, p.thumbnail FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE oi.order_id=?', [$order['id']]);
        foreach ($items as &$item) {
            $item['download_url'] = signed_download_url($item['download_token']);
        }
        $this->view('user/account/order-detail', ['title' => "Order #{$order['id']}", 'order' => $order, 'items' => $items]);
    }
}
