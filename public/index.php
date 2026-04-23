<?php
// ============================================================
// THEMORA SHOP — Front Controller
// ============================================================

require_once dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\{Router, Request};

// Maintenance mode check
if (setting('maintenance_mode') === '1' && !is_admin()) {
    http_response_code(503);
    $msg = setting('maintenance_message', 'We are performing scheduled maintenance. Back soon!');
    include APP_PATH . '/Views/errors/maintenance.php';
    exit;
}

$router = new Router();
$request = new Request();

// ── PUBLIC ROUTES ─────────────────────────────────────────────
$router->get('/', [\App\Controllers\Shop\HomeController::class, 'index']);
$router->get('/products', [\App\Controllers\Shop\ProductController::class, 'index']);
$router->get('/products/:slug', [\App\Controllers\Shop\ProductController::class, 'show']);
$router->get('/cart', [\App\Controllers\Shop\CartController::class, 'index']);
$router->post('/cart/add', [\App\Controllers\Shop\CartController::class, 'add']);
$router->post('/cart/buy-now', [\App\Controllers\Shop\CartController::class, 'buyNow']);
$router->post('/cart/remove', [\App\Controllers\Shop\CartController::class, 'remove']);
$router->post('/cart/clear', [\App\Controllers\Shop\CartController::class, 'clear']);
$router->get('/checkout', [\App\Controllers\Shop\CheckoutController::class, 'index']);
$router->post('/checkout/process', [\App\Controllers\Shop\CheckoutController::class, 'process']);
$router->get('/checkout/success', [\App\Controllers\Shop\CheckoutController::class, 'orderSuccess']);
$router->post('/coupon/apply', [\App\Controllers\Shop\CheckoutController::class, 'applyCoupon']);
$router->get('/faq', [\App\Controllers\Support\FaqController::class, 'index']);
$router->get('/contact', [\App\Controllers\Support\FaqController::class, 'contact']);
$router->post('/contact', [\App\Controllers\Support\FaqController::class, 'sendContact']);
$router->get('/download/:tokenOrId', [\App\Controllers\Shop\ProductController::class, 'universalDownload']);

// ── LEGAL ROUTES ──────────────────────────────────────────────
$router->get('/privacy-policy', [\App\Controllers\Support\PageController::class, 'privacy']);
$router->get('/terms-of-service', [\App\Controllers\Support\PageController::class, 'terms']);
$router->get('/refund-policy', [\App\Controllers\Support\PageController::class, 'refund']);

// ── AUTH ROUTES ───────────────────────────────────────────────
$router->get('/login', [\App\Controllers\User\AuthController::class, 'loginForm']);
$router->post('/login', [\App\Controllers\User\AuthController::class, 'login']);
$router->get('/signup', [\App\Controllers\User\AuthController::class, 'signupForm']);
$router->post('/signup', [\App\Controllers\User\AuthController::class, 'signup']);
$router->get('/logout', [\App\Controllers\User\AuthController::class, 'logout']);
$router->get('/forgot-password', [\App\Controllers\User\AuthController::class, 'forgotForm']);
$router->post('/forgot-password', [\App\Controllers\User\AuthController::class, 'forgot']);
$router->get('/reset-password/:token', [\App\Controllers\User\AuthController::class, 'resetForm']);
$router->post('/reset-password', [\App\Controllers\User\AuthController::class, 'reset']);
$router->get('/auth/google', [\App\Controllers\User\AuthController::class, 'googleRedirect']);
$router->get('/auth/google/callback', [\App\Controllers\User\AuthController::class, 'googleCallback']);
$router->get('/auth/github', [\App\Controllers\User\AuthController::class, 'githubRedirect']);
$router->get('/auth/github/callback', [\App\Controllers\User\AuthController::class, 'githubCallback']);
$router->get('/2fa', [\App\Controllers\User\AuthController::class, 'twoFaForm']);
$router->post('/2fa', [\App\Controllers\User\AuthController::class, 'twoFaVerify']);

// ── USER DASHBOARD ROUTES ─────────────────────────────────────
$router->get('/dashboard', [\App\Controllers\User\OrderController::class, 'dashboard']);
$router->get('/dashboard/orders', [\App\Controllers\User\OrderController::class, 'orders']);
$router->get('/dashboard/order/:id', [\App\Controllers\User\OrderController::class, 'orderDetail']);
$router->get('/dashboard/profile', [\App\Controllers\User\ProfileController::class, 'index']);
$router->post('/dashboard/profile', [\App\Controllers\User\ProfileController::class, 'update']);
$router->get('/dashboard/profile/2fa-setup', [\App\Controllers\User\ProfileController::class, 'twoFaSetup']);
$router->post('/dashboard/profile/2fa-enable', [\App\Controllers\User\ProfileController::class, 'twoFaEnable']);
$router->post('/dashboard/profile/2fa-disable', [\App\Controllers\User\ProfileController::class, 'twoFaDisable']);
$router->post('/dashboard/profile/password', [\App\Controllers\User\ProfileController::class, 'updatePassword']);
$router->post('/dashboard/profile/notifications', [\App\Controllers\User\ProfileController::class, 'updateNotifications']);
$router->get('/dashboard/wishlist', [\App\Controllers\User\ProfileController::class, 'wishlist']);
$router->post('/wishlist/toggle', [\App\Controllers\User\ProfileController::class, 'toggleWishlist']);
$router->get('/dashboard/affiliate', [\App\Controllers\User\AffiliateController::class, 'index']);
$router->post('/affiliate/payout', [\App\Controllers\User\AffiliateController::class, 'requestPayout']);
$router->get('/dashboard/tickets', [\App\Controllers\Support\TicketController::class, 'index']);
$router->get('/dashboard/tickets/new', [\App\Controllers\Support\TicketController::class, 'createForm']);
$router->post('/dashboard/tickets', [\App\Controllers\Support\TicketController::class, 'create']);
$router->get('/dashboard/tickets/:id', [\App\Controllers\Support\TicketController::class, 'show']);
$router->post('/dashboard/tickets/:id/reply', [\App\Controllers\Support\TicketController::class, 'reply']);

// ── PAYMENT WEBHOOK ROUTES ────────────────────────────────────
$router->post('/webhook/razorpay', [\App\Controllers\Shop\PaymentCallbackController::class, 'razorpay']);
$router->post('/webhook/stripe', [\App\Controllers\Shop\PaymentCallbackController::class, 'stripe']);
$router->post('/webhook/paypal', [\App\Controllers\Shop\PaymentCallbackController::class, 'paypal']);

// ── API ROUTES ────────────────────────────────────────────────
$router->get('/api/search', [\App\Controllers\Api\V1\ProductApi::class, 'search']);
$router->post('/api/newsletter', [\App\Controllers\Api\V1\ProductApi::class, 'newsletter']);
$router->get('/api/recommendations', [\App\Controllers\Api\V1\ProductApi::class, 'recommendations']);

// ── ADMIN ROUTES ──────────────────────────────────────────────
$router->get('/admin', [\App\Controllers\Admin\DashboardController::class, 'index'], [\App\Middleware\AdminMiddleware::class]);

// Categories
$router->get('/admin/categories', [\App\Controllers\Admin\CategoryManageController::class, 'index'], [\App\Middleware\AdminMiddleware::class]);
$router->get('/admin/categories/create', [\App\Controllers\Admin\CategoryManageController::class, 'create'], [\App\Middleware\AdminMiddleware::class]);
$router->post('/admin/categories/store', [\App\Controllers\Admin\CategoryManageController::class, 'store'], [\App\Middleware\AdminMiddleware::class]);
$router->get('/admin/categories/:id/edit', [\App\Controllers\Admin\CategoryManageController::class, 'edit'], [\App\Middleware\AdminMiddleware::class]);
$router->post('/admin/categories/:id', [\App\Controllers\Admin\CategoryManageController::class, 'update'], [\App\Middleware\AdminMiddleware::class]);
$router->post('/admin/categories/:id/delete', [\App\Controllers\Admin\CategoryManageController::class, 'delete'], [\App\Middleware\AdminMiddleware::class]);

$router->get('/admin/products', [\App\Controllers\Admin\ProductManageController::class, 'index'], [\App\Middleware\AdminMiddleware::class]);
$router->get('/admin/products/create', [\App\Controllers\Admin\ProductManageController::class, 'create'], [\App\Middleware\AdminMiddleware::class]);
$router->post('/admin/products/store', [\App\Controllers\Admin\ProductManageController::class, 'store'], [\App\Middleware\AdminMiddleware::class]);
$router->get('/admin/products/:id/edit', [\App\Controllers\Admin\ProductManageController::class, 'edit'], [\App\Middleware\AdminMiddleware::class]);
$router->post('/admin/products/:id', [\App\Controllers\Admin\ProductManageController::class, 'update'], [\App\Middleware\AdminMiddleware::class]);
$router->post('/admin/products/:id/delete', [\App\Controllers\Admin\ProductManageController::class, 'delete'], [\App\Middleware\AdminMiddleware::class]);
$router->get('/admin/orders', [\App\Controllers\Admin\OrderManageController::class, 'index'], [\App\Middleware\AdminMiddleware::class]);
$router->get('/admin/orders/:id', [\App\Controllers\Admin\OrderManageController::class, 'show'], [\App\Middleware\AdminMiddleware::class]);
$router->post('/admin/orders/:id/refund', [\App\Controllers\Admin\OrderManageController::class, 'refund'], [\App\Middleware\AdminMiddleware::class]);
$router->get('/admin/users', [\App\Controllers\Admin\UserManageController::class, 'index'], [\App\Middleware\AdminMiddleware::class]);
$router->get('/admin/users/:id', [\App\Controllers\Admin\UserManageController::class, 'show'], [\App\Middleware\AdminMiddleware::class]);
$router->post('/admin/users/:id/block', [\App\Controllers\Admin\UserManageController::class, 'block'], [\App\Middleware\AdminMiddleware::class]);
$router->post('/admin/users/:id/impersonate', [\App\Controllers\Admin\UserManageController::class, 'impersonate'], [\App\Middleware\AdminMiddleware::class]);
$router->get('/admin/coupons', [\App\Controllers\Admin\CouponController::class, 'index'], [\App\Middleware\AdminMiddleware::class]);
$router->post('/admin/coupons', [\App\Controllers\Admin\CouponController::class, 'store'], [\App\Middleware\AdminMiddleware::class]);
$router->post('/admin/coupons/:id/delete', [\App\Controllers\Admin\CouponController::class, 'delete'], [\App\Middleware\AdminMiddleware::class]);
$router->get('/admin/analytics', [\App\Controllers\Admin\ReportController::class, 'index'], [\App\Middleware\AdminMiddleware::class]);
$router->get('/admin/analytics/export', [\App\Controllers\Admin\ReportController::class, 'export'], [\App\Middleware\AdminMiddleware::class]);
$router->get('/admin/tickets', [\App\Controllers\Admin\SupportController::class, 'index'], [\App\Middleware\AdminMiddleware::class]);
$router->get('/admin/tickets/:id', [\App\Controllers\Admin\SupportController::class, 'show'], [\App\Middleware\AdminMiddleware::class]);
$router->post('/admin/tickets/:id/reply', [\App\Controllers\Admin\SupportController::class, 'reply'], [\App\Middleware\AdminMiddleware::class]);
$router->post('/admin/tickets/:id/assign', [\App\Controllers\Admin\SupportController::class, 'assign'], [\App\Middleware\AdminMiddleware::class]);
$router->get('/admin/faqs', [\App\Controllers\Admin\SupportController::class, 'faqs'], [\App\Middleware\AdminMiddleware::class]);
$router->post('/admin/faqs', [\App\Controllers\Admin\SupportController::class, 'storeFaq'], [\App\Middleware\AdminMiddleware::class]);
$router->post('/admin/faqs/:id/delete', [\App\Controllers\Admin\SupportController::class, 'deleteFaq'], [\App\Middleware\AdminMiddleware::class]);
$router->get('/admin/settings', [\App\Controllers\Admin\SettingsController::class, 'index'], [\App\Middleware\AdminMiddleware::class]);
$router->post('/admin/settings', [\App\Controllers\Admin\SettingsController::class, 'update'], [\App\Middleware\AdminMiddleware::class]);
$router->get('/admin/affiliates', [\App\Controllers\Admin\SettingsController::class, 'affiliates'], [\App\Middleware\AdminMiddleware::class]);
$router->post('/admin/affiliates/:id/approve', [\App\Controllers\Admin\SettingsController::class, 'approvePayout'], [\App\Middleware\AdminMiddleware::class]);
$router->get('/admin/reviews', [\App\Controllers\Admin\ProductManageController::class, 'reviews'], [\App\Middleware\AdminMiddleware::class]);
$router->post('/admin/reviews/:id', [\App\Controllers\Admin\ProductManageController::class, 'reviewAction'], [\App\Middleware\AdminMiddleware::class]);
$router->get('/admin/activity-log', [\App\Controllers\Admin\DashboardController::class, 'activityLog'], [\App\Middleware\AdminMiddleware::class]);
$router->get('/admin/login', [\App\Controllers\Admin\DashboardController::class, 'loginForm']);
$router->post('/admin/login', [\App\Controllers\Admin\DashboardController::class, 'login']);
$router->get('/admin/logout', [\App\Controllers\Admin\DashboardController::class, 'logout']);

// ── DISPATCH ──────────────────────────────────────────────────
$router->dispatch($request);
