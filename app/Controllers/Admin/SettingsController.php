<?php
// ============================================================
// THEMORA SHOP — Admin Settings Controller
// ============================================================

namespace App\Controllers\Admin;

use App\Core\{Controller, Request, Database};

class SettingsController extends Controller
{
    public function index(Request $req): void
    {
        $settings = Database::fetchAll('SELECT * FROM settings ORDER BY `key`');
        $kvMap    = array_column($settings, 'value', 'key');
        $this->view('admin/settings/index', ['title' => 'Settings — Admin', 'settings' => $kvMap], 'admin');
    }

    public function update(Request $req): void
    {
        $allowed = [
            'site_name','site_tagline','currency','currency_symbol','tax_rate','tax_label',
            'maintenance_mode', 'maintenance_msg', 'support_email', 'razorpay_enabled', 'stripe_enabled',
            'paypal_enabled','upi_enabled','bundle_discount_enabled','bundle_discount_percent',
            'bundle_min_items','download_expiry_hours','default_download_limit','affiliate_commission',
            'affiliate_commission_global','pagination_type','products_per_page','footer_text',
            'primary_color','sitemap_enabled','robots_txt',
            'google_client_id','google_client_secret','github_client_id','github_client_secret',
            'smtp_host','smtp_port','smtp_user','smtp_pass','smtp_encryption',
            'mail_from_name','mail_from_email','ga_id','meta_pixel_id','openai_api_key',
            'admin_ip_whitelist','max_login_attempts','lockout_minutes'
        ];

        $post = $_POST;
        $updatedCount = 0;

        foreach ($allowed as $key) {
            if (isset($post[$key])) {
                $value = $post[$key];
                Database::execute(
                    'INSERT INTO settings (`key`, value) VALUES (?,?) ON DUPLICATE KEY UPDATE value=?',
                    [$key, $value, $value]
                );
                $updatedCount++;
            }
        }

        Database::execute('INSERT INTO activity_logs (admin_id, action) VALUES (?,?)', [auth()['id'], "Updated {$updatedCount} settings"]);
        flash_success('Settings saved successfully!');
        $this->redirect(url('admin/settings'));
    }

    public function affiliates(Request $req): void
    {
        $payouts = Database::fetchAll(
            'SELECT ap.*, u.name as user_name, u.email as user_email, a.commission_rate
             FROM affiliate_payouts ap
             JOIN affiliates a ON a.id=ap.affiliate_id
             JOIN users u ON u.id=a.user_id
             ORDER BY ap.created_at DESC'
        );
        $this->view('admin/affiliates', ['title' => 'Affiliate Payouts — Admin', 'payouts' => $payouts], 'admin');
    }

    public function approvePayout(Request $req): void
    {
        $id     = $req->param('id');
        $action = $req->post('action', 'approve');
        $status = $action === 'approve' ? 'paid' : 'rejected';

        $payout = Database::fetchOne('SELECT * FROM affiliate_payouts WHERE id=?', [$id]);
        Database::execute('UPDATE affiliate_payouts SET status=? WHERE id=?', [$status, $id]);

        if ($status === 'paid') {
            Database::execute(
                'UPDATE affiliates SET paid_earnings=paid_earnings+?, pending_earnings=pending_earnings-? WHERE id=?',
                [$payout['amount'], $payout['amount'], $payout['affiliate_id']]
            );
        }

        Database::execute('INSERT INTO activity_logs (admin_id, action, target_type, target_id) VALUES (?,?,?,?)', [auth()['id'], "Payout {$status}", 'payout', $id]);
        flash_success("Payout {$status}.");
        $this->redirect(url('admin/affiliates'));
    }
}
