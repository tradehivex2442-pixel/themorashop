<?php
// ============================================================
// THEMORA SHOP — Admin Order Management Controller
// ============================================================

namespace App\Controllers\Admin;

use App\Core\{Controller, Request, Database};

class OrderManageController extends Controller
{
    public function index(Request $req): void
    {
        $page    = max(1, (int)$req->get('page', 1));
        $status  = $req->get('status', '');
        $search  = $req->get('q', '');
        $gateway = $req->get('gateway', '');
        $from    = $req->get('from', '');
        $to      = $req->get('to', '');

        $where  = ['1=1'];
        $params = [];
        if ($status)  { $where[] = 'o.status=?'; $params[] = $status; }
        if ($gateway) { $where[] = 'o.payment_gateway=?'; $params[] = $gateway; }
        if ($from)    { $where[] = 'o.created_at >= ?'; $params[] = $from . ' 00:00:00'; }
        if ($to)      { $where[] = 'o.created_at <= ?'; $params[] = $to . ' 23:59:59'; }
        if ($search)  { $where[] = '(u.email LIKE ? OR u.name LIKE ? OR o.transaction_id LIKE ? OR o.guest_email LIKE ?)'; $q = "%{$search}%"; array_push($params, $q, $q, $q, $q); }

        $whereSql = implode(' AND ', $where);
        
        $countSql = "SELECT COUNT(*) AS c FROM orders o LEFT JOIN users u ON u.id=o.user_id WHERE {$whereSql}";
        $total = (int)(Database::fetchOne($countSql, $params)['c'] ?? 0);
        
        $perPage = 25;
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT o.*, u.name as user_name, u.email as user_email, 
                (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count 
                FROM orders o LEFT JOIN users u ON u.id=o.user_id 
                WHERE {$whereSql} ORDER BY o.created_at DESC LIMIT {$perPage} OFFSET {$offset}";
        $orders = Database::fetchAll($sql, $params);
        $result = $this->paginate($orders, $total, $perPage);

        // Revenue summary
        $summary = Database::fetchOne('SELECT COALESCE(SUM(total),0) as total_rev, COUNT(*) as total_cnt FROM orders WHERE status="paid"');

        $this->view('admin/orders/index', [
            'title'      => 'Orders — Admin',
            'orders'     => $result['items'],
            'pagination' => $result,
            'summary'    => $summary,
            'filters'    => compact('status', 'search', 'gateway', 'from', 'to'),
        ], 'admin');
    }

    public function show(Request $req): void
    {
        $order = Database::fetchOne(
            'SELECT o.*, u.name as user_name, u.email as user_email, c.code as coupon_code 
             FROM orders o 
             LEFT JOIN users u ON u.id = o.user_id 
             LEFT JOIN coupons c ON c.id = o.coupon_id
             WHERE o.id = ?', 
            [$req->param('id')]
        );
        if (!$order) { flash_error('Order not found.'); $this->redirect(url('admin/orders')); }

        $items = Database::fetchAll('SELECT oi.*, p.title, p.thumbnail FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE oi.order_id=?', [$order['id']]);
        foreach ($items as &$item) { $item['download_url'] = signed_download_url($item['download_token']); }

        $this->view('admin/orders/show', ['title' => "Order #{$order['id']}", 'order' => $order, 'items' => $items], 'admin');
    }

    public function refund(Request $req): void
    {
        $id = $req->param('id');
        Database::execute('UPDATE orders SET status="refunded" WHERE id=?', [$id]);
        Database::execute('INSERT INTO activity_logs (admin_id, action, target_type, target_id) VALUES (?,?,?,?)', [auth()['id'], 'Issued refund', 'order', $id]);
        flash_success('Order marked as refunded.');
        $this->redirect(url("admin/orders/{$id}"));
    }
}
