<?php
// ============================================================
// THEMORA SHOP — App Bootstrap
// ============================================================

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH',  ROOT_PATH . '/app');
define('PUB_PATH',  ROOT_PATH . '/public');
define('STORAGE',   ROOT_PATH . '/storage');

// ── Detect base URL automatically ──────────────────────────────
(function () {
    $scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host    = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script  = $_SERVER['SCRIPT_NAME'] ?? '/public/index.php';
    // Remove /index.php from the path → get the directory
    $base    = rtrim(dirname($script), '/\\');
    // Ensure slash between host and base, but only if base is not empty
    $baseUrl = $scheme . '://' . $host . ($base ? '/' . ltrim($base, '/\\') : '');
    define('BASE_URL', rtrim($baseUrl, '/'));
    define('API_URL',  env('API_URL', BASE_URL));
})();

// ── Storage directories ────────────────────────────────────────
foreach (['uploads', 'uploads/products', 'uploads/avatars', 'uploads/tickets', 'downloads', 'cache', 'logs'] as $dir) {
    $path = STORAGE . '/' . $dir;
    if (!is_dir($path)) @mkdir($path, 0755, true);
}

// Load environment variables from .env
function loadEnv(string $path): void
{
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        [$name, $value] = explode('=', $line, 2) + [null, null];
        if ($name && $value !== null) {
            $name  = trim($name);
            $value = trim(trim($value), '"\'');
            $_ENV[$name] = $_SERVER[$name] = $value;
        }
    }
}

function env(string $key, string $default = ''): string
{
    return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
}

loadEnv(ROOT_PATH . '/.env');

// Autoloader (PSR-4 style)
spl_autoload_register(function (string $class): void {
    $class    = str_replace('\\', '/', $class);
    $prefixes = [
        'App/'  => APP_PATH . '/',
    ];
    foreach ($prefixes as $prefix => $base) {
        if (str_starts_with($class, $prefix)) {
            $path = $base . substr($class, strlen($prefix)) . '.php';
            if (file_exists($path)) {
                require_once $path;
                return;
            }
        }
    }
});

// Load global helpers
require_once APP_PATH . '/Helpers/functions.php';

// Error handling
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    if (env('APP_DEBUG') === 'true') {
        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
    error_log("[{$errno}] {$errstr} in {$errfile}:{$errline}");
});

set_exception_handler(function (\Throwable $e) {
    if (env('APP_DEBUG') === 'true') {
        echo '<pre style="background:#1e1e2e;color:#cdd6f4;padding:20px;border-radius:8px;">';
        echo "<strong style='color:#f38ba8'>Exception:</strong> " . htmlspecialchars($e->getMessage()) . "\n";
        echo "<strong style='color:#fab387'>File:</strong> " . $e->getFile() . ':' . $e->getLine() . "\n\n";
        echo "<strong style='color:#a6e3a1'>Stack Trace:</strong>\n" . htmlspecialchars($e->getTraceAsString());
        echo '</pre>';
    } else {
        http_response_code(500);
        $file = APP_PATH . '/Views/errors/500.php';
        if (file_exists($file)) require $file;
        else echo 'An internal server error occurred.';
    }
    error_log($e->getMessage() . "\n" . $e->getTraceAsString());
});

// ── App Key (for HMAC signing) ────────────────────────────────
if (!defined('APP_KEY')) {
    define('APP_KEY', env('APP_KEY', 'themora-default-secret-change-me'));
}

// Start session
\App\Core\Session::start();

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}
