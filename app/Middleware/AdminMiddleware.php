<?php
// ============================================================
// THEMORA SHOP — Admin Middleware (RBAC)
// ============================================================

namespace App\Middleware;

use App\Core\{Request, Session, Response};

class AdminMiddleware
{
    public function handle(Request $request): void
    {
        $user = Session::get('user');

        if (!$user) {
            Session::flash('error', 'Please log in to access the admin panel.');
            Response::redirect(url('admin/login'));
        }

        $roles = ['editor', 'support', 'super_admin'];
        if (!in_array($user['role'], $roles)) {
            Response::redirect(url('/'));
        }

        // IP whitelist check
        $whitelist = \App\Core\Database::fetchAll('SELECT ip_address FROM ip_whitelist');
        if (!empty($whitelist)) {
            $ips = array_column($whitelist, 'ip_address');
            if (!in_array($request->ip(), $ips)) {
                http_response_code(403);
                die('Access denied: your IP is not whitelisted.');
            }
        }
    }
}
