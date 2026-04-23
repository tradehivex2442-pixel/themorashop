<?php
// ============================================================
// THEMORA SHOP — Admin CouponController (matches DB schema)
// coupons: code, type(flat|percent), value, min_order, max_uses,
//          used_count, expiry, is_active
// ============================================================
namespace App\Controllers\Admin;

use App\Core\{Controller, Database, Session};

class CouponController extends Controller
{
    public function index(): void
    {
        $this->requireAdmin();
        $coupons = Database::fetchAll("SELECT * FROM coupons ORDER BY created_at DESC");
        $this->view('admin/coupons/index', compact('coupons'), 'admin', 'Coupons');
    }

    public function store(): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();

        $code     = strtoupper(trim($_POST['code'] ?? ''));
        $type     = in_array($_POST['type'] ?? '', ['flat','percent']) ? $_POST['type'] : 'percent';
        $value    = (float)($_POST['value'] ?? 0);
        $minOrder = (float)($_POST['min_order_amount'] ?? 0);
        $maxUses  = (int)($_POST['max_uses'] ?? 0);
        $expiry   = !empty($_POST['expires_at']) ? $_POST['expires_at'] . ' 23:59:59' : null;
        $active   = isset($_POST['is_active']) ? 1 : 0;

        if (empty($code) || $value <= 0) {
            Session::flash('error', 'Code and value are required.');
            $this->redirect('admin/coupons');
        }

        if ($type === 'percent' && $value > 100) {
            Session::flash('error', 'Percentage discount cannot exceed 100%.');
            $this->redirect('admin/coupons');
        }

        // Check unique
        $exists = Database::fetchOne("SELECT id FROM coupons WHERE code=?", [$code]);
        if ($exists) {
            Session::flash('error', "Coupon code '{$code}' already exists.");
            $this->redirect('admin/coupons');
        }

        Database::query(
            "INSERT INTO coupons (code, type, value, min_order, max_uses, expiry, is_active) VALUES (?,?,?,?,?,?,?)",
            [$code, $type, $value, $minOrder, $maxUses, $expiry, $active]
        );

        log_activity('coupon_created', "Code: {$code}");
        Session::flash('success', "Coupon {$code} created!");
        $this->redirect('admin/coupons');
    }

    public function delete(int $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();
        Database::query("DELETE FROM coupons WHERE id=?", [$id]);
        log_activity('coupon_deleted', "ID: {$id}");
        Session::flash('success', 'Coupon deleted.');
        $this->redirect('admin/coupons');
    }
}
