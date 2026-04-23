<?php
// ============================================================
// THEMORA SHOP — Base Controller
// ============================================================

namespace App\Core;

class Controller
{
    protected function view(string $view, array $data = [], string $layout = 'user', string $title = ''): void
    {
        if ($title) $data['title'] = $title;
        $renderer = new View();
        $renderer->render($view, $data, $layout);
    }

    protected function requireAuth(): void
    {
        if (!Session::get('user')) {
            Session::flash('info', 'Please log in to continue.');
            Session::set('intended_url', $_SERVER['REQUEST_URI'] ?? '');
            $this->redirect(url('login'));
        }
    }

    protected function requireAdmin(): void
    {
        if (!$this->isAdmin()) {
            http_response_code(403);
            $this->redirect(url('admin/login'));
        }
    }

    protected function input(string $key, mixed $default = null): mixed
    {
        if (isset($_POST[$key])) {
            return is_string($_POST[$key]) ? trim($_POST[$key]) : $_POST[$key];
        }
        if (isset($_GET[$key])) {
            return is_string($_GET[$key]) ? trim($_GET[$key]) : $_GET[$key];
        }
        return $default;
    }

    protected function paginate(array $items, int $total, int $perPage = 15): array
    {
        $perPage = max(1, $perPage);
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $pages   = max(1, (int)ceil($total / $perPage));
        return [
            'items'    => $items,
            'total'    => $total,
            'pages'    => $pages,
            'current'  => $page,
            'per_page' => $perPage,
            'has_prev' => $page > 1,
            'has_next' => $page < $pages,
        ];
    }

    protected function json(mixed $data, int $code = 200): never
    {
        Response::json($data, $code);
    }

    protected function redirect(string $url): never
    {
        Response::redirect($url);
    }

    protected function back(): never
    {
        Response::back();
    }

    protected function success(string $msg, mixed $data = null): never
    {
        Response::success($msg, $data);
    }

    protected function error(string $msg, int $code = 400, mixed $errors = null): never
    {
        Response::error($msg, $code, $errors);
    }

    protected function user(): ?array
    {
        return Session::get('user');
    }

    protected function isAdmin(): bool
    {
        $user = $this->user();
        return $user && in_array($user['role'], ['editor', 'support', 'super_admin']);
    }

    protected function isSuperAdmin(): bool
    {
        $user = $this->user();
        return $user && $user['role'] === 'super_admin';
    }

    protected function flashSuccess(string $msg): void
    {
        Session::flash('success', $msg);
    }

    protected function flashError(string $msg): void
    {
        Session::flash('error', $msg);
    }

    protected function csrf(): string
    {
        if (!Session::has('csrf_token')) {
            Session::set('csrf_token', bin2hex(random_bytes(32)));
        }
        return Session::get('csrf_token');
    }

    protected function verifyCsrf(): void
    {
        $token  = $_POST['_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        $stored = Session::get('csrf_token', '');
        if (empty($stored) || !hash_equals($stored, $token)) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Invalid CSRF token'], 419);
            }
            Session::flash('error', 'Security token expired. Please try again.');
            $this->back();
        }
    }

    protected function isAjax(): bool
    {
        return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
    }

    protected function abort(int $code = 404): never
    {
        http_response_code($code);
        $file = APP_PATH . "/Views/errors/{$code}.php";
        if (file_exists($file)) require $file;
        else echo "Error {$code}";
        exit;
    }
}
