<?php
// ============================================================
// THEMORA SHOP — Admin Report Controller
// ============================================================

namespace App\Controllers\Admin;

use App\Core\{Controller, Request, Database};

class ReportController extends Controller
{
    public function index(Request $req): void
    {
        $period = $req->get('period', '30');

        // Revenue over period / Chart Data
        $chartData = Database::fetchAll(
            "SELECT DATE(created_at) as date, SUM(total) as revenue, COUNT(*) as orders
             FROM orders WHERE status='paid' AND created_at >= DATE_SUB(NOW(), INTERVAL {$period} DAY)
             GROUP BY DATE(created_at) ORDER BY date ASC"
        );

        // Gateway breakdown
        $gatewayBreakdown = Database::fetchAll(
            "SELECT payment_gateway as gateway, COUNT(*) as count 
             FROM orders WHERE status='paid' AND created_at >= DATE_SUB(NOW(), INTERVAL {$period} DAY)
             GROUP BY payment_gateway"
        );

        // Top products
        $topProducts = Database::fetchAll(
            "SELECT p.title, p.thumbnail, c.name as category_name, p.avg_rating, COUNT(oi.id) as sales, SUM(oi.price) as revenue
             FROM order_items oi JOIN products p ON p.id=oi.product_id
             LEFT JOIN categories c ON c.id=p.category_id
             JOIN orders o ON o.id=oi.order_id WHERE o.status='paid'
             AND o.created_at >= DATE_SUB(NOW(), INTERVAL {$period} DAY)
             GROUP BY oi.product_id ORDER BY sales DESC LIMIT 10"
        );

        // Summary
        $summaryData = Database::fetchOne(
            "SELECT SUM(total) as revenue, COUNT(*) as orders FROM orders 
             WHERE status='paid' AND created_at >= DATE_SUB(NOW(), INTERVAL {$period} DAY)"
        );
        $newUsers = Database::fetchOne(
            "SELECT COUNT(*) as new_users FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL {$period} DAY)"
        );
        $refunds = Database::fetchOne(
            "SELECT COUNT(*) as refunds FROM orders WHERE status='refunded' AND created_at >= DATE_SUB(NOW(), INTERVAL {$period} DAY)"
        );

        $rev = (float)($summaryData['revenue'] ?? 0);
        $orders = (int)($summaryData['orders'] ?? 0);
        
        $summary = [
            'revenue'   => $rev,
            'orders'    => $orders,
            'avg_order' => $orders > 0 ? $rev / $orders : 0,
            'new_users' => (int)($newUsers['new_users'] ?? 0),
            'refunds'   => (int)($refunds['refunds'] ?? 0)
        ];

        $this->view('admin/reports/index', [
            'title'            => 'Analytics — Admin',
            'period'           => $period,
            'summary'          => $summary,
            'chartData'        => $chartData,
            'topProducts'      => $topProducts,
            'gatewayBreakdown' => $gatewayBreakdown,
        ], 'admin');
    }

    public function export(Request $req): void
    {
        $type = $req->get('type', 'orders');
        if ($type === 'orders') {
            $rows = Database::fetchAll('SELECT o.id, o.total, o.tax, o.discount, o.status, o.payment_gateway, o.transaction_id, o.created_at, COALESCE(u.email, o.guest_email) as email FROM orders o LEFT JOIN users u ON u.id=o.user_id ORDER BY o.created_at DESC');
            $headers = ['ID', 'Total', 'Tax', 'Discount', 'Status', 'Gateway', 'Transaction', 'Date', 'Email'];
        } else {
            $rows = Database::fetchAll('SELECT u.id, u.name, u.email, u.role, u.created_at FROM users ORDER BY u.created_at DESC');
            $headers = ['ID', 'Name', 'Email', 'Role', 'Joined'];
        }

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $type . '_export_' . date('Y-m-d') . '.csv"');

        $out = fopen('php://output', 'w');
        fputcsv($out, $headers);
        foreach ($rows as $row) fputcsv($out, array_values($row));
        fclose($out);
        exit;
    }
}
