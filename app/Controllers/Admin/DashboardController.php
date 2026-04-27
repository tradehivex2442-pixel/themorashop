<?php
// THEMORA SHOP — Admin DashboardController (CORRECTED)
// Fixes: flash_error -> native flash methods, tickets table, activity_logs table
namespace App\Controllers\Admin;

use App\Core\{Controller, Database, Session, Request};

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireAdmin();

        $revenue  = (float)(Database::fetchOne('SELECT COALESCE(SUM(total),0) AS r FROM orders WHERE status="paid"')['r'] ?? 0);
        $orders   = (int)(Database::fetchOne('SELECT COUNT(*) AS c FROM orders WHERE status="paid"')['c'] ?? 0);
        $users    = (int)(Database::fetchOne('SELECT COUNT(*) AS c FROM users WHERE role="user"')['c'] ?? 0);
        $products = (int)(Database::fetchOne('SELECT COUNT(*) AS c FROM products WHERE status="active"')['c'] ?? 0);

        $revenueChart = Database::fetchAll(
            'SELECT DATE(created_at) AS date, COALESCE(SUM(total),0) AS revenue, COUNT(*) AS orders
             FROM orders WHERE status="paid" AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
             GROUP BY DATE(created_at) ORDER BY date ASC'
        );

        $recentOrders = Database::fetchAll(
            'SELECT o.*, u.name AS user_name, u.email AS user_email
             FROM orders o LEFT JOIN users u ON u.id=o.user_id
             ORDER BY o.created_at DESC LIMIT 10'
        );

        $topProducts = Database::fetchAll(
            'SELECT p.title, p.thumbnail, COALESCE(SUM(oi.price),0) AS revenue, COUNT(oi.id) AS sales
             FROM order_items oi
             JOIN products p ON p.id=oi.product_id
             JOIN orders o ON o.id=oi.order_id WHERE o.status="paid"
             GROUP BY oi.product_id ORDER BY sales DESC LIMIT 5'
        );

        // Correct table name: tickets
        $pendingTickets = (int)(Database::fetchOne('SELECT COUNT(*) AS c FROM tickets WHERE status IN ("open","in-progress")')['c'] ?? 0);
        $pendingPayouts = (int)(Database::fetchOne('SELECT COUNT(*) AS c FROM affiliate_payouts WHERE status="pending"')['c'] ?? 0);

        $this->view('admin/dashboard', compact(
            'revenue','orders','users','products',
            'revenueChart','recentOrders','topProducts',
            'pendingTickets','pendingPayouts'
        ), 'admin', 'Dashboard');
    }

    public function loginForm(): void
    {
        if (is_admin()) $this->redirect(url('admin'));
        $this->view('admin/login', [
            'error' => Session::getFlash('error'),
        ], 'none', 'Admin Login');
    }

    public function login(Request $req): void
    {
        $email    = trim($req->post('email', ''));
        $password = $req->post('password', '');
        $ip       = $req->ip();

        if (rate_limit("admin_login_{$ip}", 5, 60)) {
            flash_error('Too many attempts. Please wait 1 minute.');
            $this->redirect(url('admin/login'));
        }

        $user = Database::fetchOne('SELECT * FROM users WHERE email=?', [$email]);

        if (!$user || !password_verify($password, $user['password'])
            || !in_array($user['role'], ['editor','support','super_admin'])) {
            flash_error('Invalid credentials. Please try again.');
            $this->redirect(url('admin/login'));
        }

        if ($user['is_blocked']) {
            flash_error('Your account has been suspended.');
            $this->redirect(url('admin/login'));
        }

        Session::regenerate();
        Session::set('user', [
            'id'     => $user['id'],
            'name'   => $user['name'],
            'email'  => $user['email'],
            'role'   => $user['role'],
            'avatar' => $user['avatar'],
        ]);

        // Use correct table + columns
        Database::query(
            'INSERT INTO activity_logs (admin_id, action, ip) VALUES (?,?,?)',
            [$user['id'], 'admin_login', $ip]
        );

        $this->redirect(url('admin'));
    }

    public function logout(): void
    {
        $userId = auth()['id'] ?? null;
        if ($userId) {
            Database::query(
                'INSERT INTO activity_logs (admin_id, action, ip) VALUES (?,?,?)',
                [$userId, 'admin_logout', $_SERVER['REMOTE_ADDR'] ?? '']
            );
        }
        Session::destroy();
        $this->redirect(url('admin/login'));
    }

    public function activityLog(): void
    {
        $this->requireAdmin();
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 50;
        $offset  = ($page - 1) * $perPage;

        $where  = '1=1';
        $params = [];
        if (!empty($_GET['q'])) {
            $where   .= ' AND al.action LIKE ?';
            $params[] = '%' . $_GET['q'] . '%';
        }

        $total = (int)(Database::fetchOne("SELECT COUNT(*) AS c FROM activity_logs al WHERE {$where}", $params)['c'] ?? 0);
        $logs  = Database::fetchAll(
            "SELECT al.*, u.name AS admin_name
             FROM activity_logs al
             LEFT JOIN users u ON u.id=al.admin_id
             WHERE {$where}
             ORDER BY al.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        $pagination = $this->paginate($logs, $total, $perPage);

        $this->view('admin/activity-log', compact('logs', 'pagination'), 'admin', 'Activity Log');
    }
}
