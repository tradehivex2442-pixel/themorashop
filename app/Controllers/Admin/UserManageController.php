<?php
// THEMORA SHOP — Admin UserManageController (corrected: is_blocked not is_banned)
namespace App\Controllers\Admin;

use App\Core\{Controller, Database, Session, Request};

class UserManageController extends Controller
{
    public function index(): void
    {
        $this->requireAdmin();
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset  = ($page - 1) * $perPage;
        $filters = [];

        $where  = '1=1';
        $params = [];

        if (!empty($_GET['q'])) {
            $where   .= ' AND (u.name LIKE ? OR u.email LIKE ?)';
            $params[] = '%' . $_GET['q'] . '%';
            $params[] = '%' . $_GET['q'] . '%';
            $filters['search'] = $_GET['q'];
        }
        if (!empty($_GET['role'])) {
            $where   .= ' AND u.role = ?';
            $params[] = $_GET['role'];
            $filters['role'] = $_GET['role'];
        }

        $total = (int)(Database::fetchOne("SELECT COUNT(*) AS c FROM users u WHERE {$where}", $params)['c'] ?? 0);

        $users = Database::fetchAll(
            "SELECT u.*,
                COUNT(DISTINCT o.id) AS order_count,
                COALESCE(SUM(o.total),0) AS total_spent
             FROM users u
             LEFT JOIN orders o ON o.user_id = u.id AND o.status='paid'
             WHERE {$where}
             GROUP BY u.id
             ORDER BY u.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        $pagination = $this->paginate($users, $total, $perPage);
        $this->view('admin/users/index', compact('users','filters','pagination'), 'admin', 'Users');
    }

    public function show(Request $req): void
    {
        $id = $req->param('id');
        $this->requireAdmin();
        $user = Database::fetchOne('SELECT * FROM users WHERE id=?', [$id]);
        if (!$user) $this->abort(404);

        $orders = Database::fetchAll(
            "SELECT o.*, COUNT(oi.id) AS item_count
             FROM orders o
             LEFT JOIN order_items oi ON oi.order_id = o.id
             WHERE o.user_id = ?
             GROUP BY o.id
             ORDER BY o.created_at DESC",
            [$id]
        );

        $this->view('admin/users/show', compact('user','orders'), 'admin', 'User: ' . $user['name']);
    }

    public function block(Request $req): void
    {
        $id = $req->param('id');
        $this->requireAdmin();
        $this->verifyCsrf();

        // Role change (super_admin only)
        if (isset($_POST['change_role']) && auth()['role'] === 'super_admin') {
            $role = $_POST['role'] ?? 'user';
            if (in_array($role, ['user','editor','support','super_admin'])) {
                Database::query('UPDATE users SET role=? WHERE id=?', [$role, $id]);
                flash_success('Role updated.');
            }
            $this->redirect("admin/users/{$id}");
        }

        // Block / unblock
        $unblock = isset($_POST['unblock']);
        $blocked = $unblock ? 0 : 1;
        Database::query('UPDATE users SET is_blocked=? WHERE id=?', [$blocked, $id]);
        log_activity($unblock ? 'user_unblocked' : 'user_blocked', "User ID: {$id}");
        flash_success($unblock ? 'User unblocked.' : 'User blocked.');
        $this->redirect("admin/users/{$id}");
    }

    public function impersonate(Request $req): void
    {
        $id = $req->param('id');
        $this->requireAdmin();
        if (auth()['role'] !== 'super_admin') {
            flash_error('Only super admins can impersonate.');
            $this->redirect('admin/users');
        }

        $target = Database::fetchOne('SELECT * FROM users WHERE id=? AND role="user"', [$id]);
        if (!$target) {
            flash_error('User not found or not eligible for impersonation.');
            $this->redirect('admin/users');
        }

        // Save admin session for returning
        Session::set('admin_impersonating', Session::get('user'));
        Session::set('user', [
            'id'     => $target['id'],
            'name'   => $target['name'],
            'email'  => $target['email'],
            'role'   => $target['role'],
            'avatar' => $target['avatar'],
        ]);

        log_activity('user_impersonated', "Impersonating user ID: {$id}");
        $this->redirect(url('dashboard'));
    }
}
