<?php
// ============================================================
// THEMORA SHOP — Global Helper Functions
// URL | Auth | Formatting | Cart | Security | View helpers
// ============================================================

if (!function_exists('url')) {
    /**
     * Generate an absolute URL.
     */
    function url(string $path = ''): string
    {
        $base = rtrim(defined('BASE_URL') ? BASE_URL : (config('app.url') ?? ''), '/');
        $path = ltrim($path, '/');
        return $path ? "{$base}/{$path}" : $base;
    }
}

if (!function_exists('asset')) {
    /**
     * Generate URL to a public asset.
     */
    function asset(string $path = ''): string
    {
        if (str_starts_with($path, 'http')) return $path;
        $base = rtrim(defined('BASE_URL') ? BASE_URL : '', '/');
        $path = ltrim($path, '/');
        
        // If it starts with uploads/, it lives in assets/uploads/
        // Otherwise, it lives in assets/
        return "{$base}/assets/{$path}";
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path): never
    {
        header('Location: ' . url($path));
        exit;
    }
}

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        static $loaded = [];
        [$file, $subKey] = explode('.', $key, 2) + ['', ''];
        if (!isset($loaded[$file])) {
            $f = dirname(__DIR__) . "/config/{$file}.php";
            $loaded[$file] = file_exists($f) ? require $f : [];
        }
        return $loaded[$file][$subKey] ?? $default;
    }
}

if (!function_exists('setting')) {
    /**
     * Read a site setting from DB (cached per request).
     */
    function setting(string $key, mixed $default = null): mixed
    {
        static $cache = null;
        if ($cache === null) {
            try {
                $rows = \App\Core\Database::fetchAll('SELECT `key`, `value` FROM settings');
                $cache = [];
                foreach ($rows as $row)
                    $cache[$row['key']] = $row['value'];
            } catch (\Throwable) {
                $cache = [];
            }
        }
        return $cache[$key] ?? $default;
    }
}

if (!function_exists('e')) {
    /**
     * HTML-escape a value.
     */
    function e(mixed $value): string
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}

if (!function_exists('auth')) {
    /**
     * Return current logged-in user array, or null.
     */
    function auth(): ?array
    {
        return \App\Core\Session::get('user') ?: null;
    }
}

if (!function_exists('logged_in')) {
    function logged_in(): bool
    {
        return auth() !== null;
    }
}

if (!function_exists('is_admin')) {
    function is_admin(): bool
    {
        $u = auth();
        return $u && in_array($u['role'], ['super_admin', 'editor', 'support'], true);
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        $token = \App\Core\Session::get('csrf_token');
        if (!$token) {
            $token = bin2hex(random_bytes(32));
            \App\Core\Session::set('csrf_token', $token);
        }
        return $token;
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('verify_csrf')) {
    function verify_csrf(): bool
    {
        $submitted = $_POST['_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        return hash_equals(csrf_token(), $submitted);
    }
}

if (!function_exists('cart_count')) {
    function cart_count(): int
    {
        return count(\App\Core\Session::get('cart', []));
    }
}

if (!function_exists('currency')) {
    function currency(float $amount): string
    {
        return setting('currency_symbol', '$') . number_format($amount, 2);
    }
}

if (!function_exists('time_ago')) {
    function time_ago(string $datetime): string
    {
        $diff = time() - strtotime($datetime);
        return match (true) {
            $diff < 60 => 'just now',
            $diff < 3600 => floor($diff / 60) . ' min ago',
            $diff < 86400 => floor($diff / 3600) . ' hr ago',
            $diff < 604800 => floor($diff / 86400) . ' day' . (floor($diff / 86400) > 1 ? 's' : '') . ' ago',
            $diff < 2592000 => floor($diff / 604800) . ' wk ago',
            $diff < 31536000 => floor($diff / 2592000) . ' mo ago',
            default => floor($diff / 31536000) . ' yr ago',
        };
    }
}

if (!function_exists('stars')) {
    /**
     * Return filled/half/empty star HTML for a rating.
     */
    function stars(float $rating, int $max = 5): string
    {
        $out = '';
        for ($i = 1; $i <= $max; $i++) {
            if ($rating >= $i)
                $out .= '★';
            elseif ($rating >= $i - .5)
                $out .= '⯨';
            else
                $out .= '☆';
        }
        return $out;
    }
}

if (!function_exists('old')) {
    /**
     * Return old form input value (from session flash or POST).
     */
    function old(string $key, string $default = ''): string
    {
        $old = \App\Core\Session::getFlash('old_input', []);
        $val = $_POST[$key] ?? $old[$key] ?? $default;
        return e($val);
    }
}

if (!function_exists('active')) {
    /**
     * Return 'active' class if current URI starts with given path.
     */
    function active(string $path): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $base = rtrim(defined('BASE_URL') ? parse_url(BASE_URL, PHP_URL_PATH) : '', '/');
        $current = str_replace($base, '', strtok($uri, '?'));
        $target = '/' . ltrim($path, '/');
        return ($path === '/' ? $current === '/' : str_starts_with($current, $target)) ? 'active' : '';
    }
}

if (!function_exists('slugify')) {
    function slugify(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);
        return trim($text, '-');
    }
}

if (!function_exists('truncate')) {
    function truncate(string $str, int $len = 100, string $end = '…'): string
    {
        return mb_strlen($str) > $len ? mb_substr($str, 0, $len) . $end : $str;
    }
}

if (!function_exists('bytes_to_human')) {
    function bytes_to_human(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $pow = floor(log($bytes, 1024));
        return round($bytes / (1024 ** $pow), 2) . ' ' . $units[$pow];
    }
}

if (!function_exists('rate_limit')) {
    /**
     * Simple in-session rate limiting.
     * Returns true if limit exceeded.
     */
    function rate_limit(string $key, int $max = 5, int $window = 60): bool
    {
        $now = time();
        $data = \App\Core\Session::get("rl_{$key}", ['count' => 0, 'reset' => $now + $window]);
        if ($now > $data['reset'])
            $data = ['count' => 0, 'reset' => $now + $window];
        $data['count']++;
        \App\Core\Session::set("rl_{$key}", $data);
        return $data['count'] > $max;
    }
}

if (!function_exists('log_activity')) {
    /**
     * Log admin/user activity to the activity_logs table.
     * Schema: id, admin_id, action, target_type, target_id, meta, ip, created_at
     */
    function log_activity(string $action, string $description = '', ?int $userId = null): void
    {
        try {
            $adminId = $userId ?? (auth()['id'] ?? null);
            if (!$adminId)
                return;
            \App\Core\Database::query(
                "INSERT INTO activity_logs (admin_id, action, target_type, ip) VALUES (?, ?, ?, ?)",
                [$adminId, $action, $description ?: null, $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0']
            );
        } catch (\Throwable) { /* non-fatal */
        }
    }
}

if (!function_exists('flash')) {
    /** Store a flash message in session. */
    function flash(string $type, string $message): void
    {
        \App\Core\Session::flash($type, $message);
    }
}

if (!function_exists('flash_error')) {
    function flash_error(string $message): void
    {
        \App\Core\Session::flash('error', $message);
    }
}

if (!function_exists('flash_success')) {
    function flash_success(string $message): void
    {
        \App\Core\Session::flash('success', $message);
    }
}

if (!function_exists('flash_get')) {
    function flash_get(string $type, mixed $default = null): mixed
    {
        return \App\Core\Session::getFlash($type, $default);
    }
}

if (!function_exists('send_mail')) {
    /**
     * Send email using PHPMailer or native mail().
     */
    function send_mail(string $to, string $subject, string $body, bool $isHtml = true): bool
    {
        if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = setting('smtp_host', 'localhost');
                $mail->SMTPAuth = true;
                $mail->Username = setting('smtp_user');
                $mail->Password = setting('smtp_pass');
                $mail->Port = (int) setting('smtp_port', 587);
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->setFrom(setting('mail_from_email', 'noreply@themorashop.com'), setting('mail_from_name', 'Themora Shop'));
                $mail->addAddress($to);
                $mail->isHTML($isHtml);
                $mail->Subject = $subject;
                $mail->Body = $body;
                return $mail->send();
            } catch (\Throwable) {
                return false;
            }
        }
        $headers = "From: " . setting('mail_from_name') . " <" . setting('mail_from_email') . ">\r\n";
        $headers .= $isHtml ? "Content-Type: text/html; charset=UTF-8\r\n" : '';
        return mail($to, $subject, $body, $headers);
    }
}

if (!function_exists('generate_download_link')) {
    /**
     * Generate a signed time-limited download URL using order_items.id.
     */
    function generate_download_link(int $orderItemId): string
    {
        $hours = (int) setting('download_expiry_hours', 48);
        $expires = time() + ($hours * 3600);
        $key = defined('APP_KEY') ? APP_KEY : env('APP_KEY', 'themora-secret');
        $token = hash_hmac('sha256', "{$orderItemId}|{$expires}", $key);
        return url("download/{$orderItemId}?expires={$expires}&token={$token}");
    }
}

if (!function_exists('verify_download_link')) {
    function verify_download_link(int $orderItemId, int $expires, string $token): bool
    {
        if (time() > $expires)
            return false;
        $key = defined('APP_KEY') ? APP_KEY : env('APP_KEY', 'themora-secret');
        $expected = hash_hmac('sha256', "{$orderItemId}|{$expires}", $key);
        return hash_equals($expected, $token);
    }
}

if (!function_exists('setting_bool')) {
    /** Return a setting value cast to boolean. */
    function setting_bool(string $key, bool $default = false): bool
    {
        return filter_var(setting($key, $default ? '1' : '0'), FILTER_VALIDATE_BOOLEAN);
    }
}

if (!function_exists('format_date')) {
    function format_date(string $datetime, string $format = 'M j, Y'): string
    {
        return date($format, strtotime($datetime));
    }
}
if (!function_exists('generate_token')) {
    function generate_token(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }
}

if (!function_exists('signed_download_url')) {
    function signed_download_url(string $token): string
    {
        return url("download/{$token}");
    }
}

if (!function_exists('stars')) {
    /**
     * Generate HTML for star ratings.
     */
    function stars(float $rating): string
    {
        $full  = floor($rating);
        $half  = ($rating - $full) >= 0.5 ? 1 : 0;
        $empty = 5 - $full - $half;

        $html = str_repeat('<i class="bi bi-star-fill"></i>', $full);
        if ($half)  $html .= '<i class="bi bi-star-half"></i>';
        if ($empty) $html .= str_repeat('<i class="bi bi-star"></i>', $empty);

        return $html;
    }
}
