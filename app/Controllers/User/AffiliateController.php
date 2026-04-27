<?php
// ============================================================
// THEMORA SHOP — Affiliate Controller
// ============================================================
namespace App\Controllers\User;
use App\Core\{Controller, Request, Database};

class AffiliateController extends Controller
{
    public function index(Request $req): void
    {
        $this->requireAuth();
        $userId = auth()['id'];
        $aff = Database::fetchOne('SELECT * FROM affiliates WHERE user_id=?', [$userId]);
        if (!$aff) {
            Database::execute('INSERT INTO affiliates (user_id) VALUES (?)', [$userId]);
            $aff = Database::fetchOne('SELECT * FROM affiliates WHERE user_id=?', [$userId]);
        }
        $user          = Database::fetchOne('SELECT referral_code FROM users WHERE id=?', [$userId]);
        $aff['code']   = $user['referral_code'] ?? '';
        $referralLink  = url('/') . '?ref=' . $aff['code'];
        $conversions   = Database::fetchAll('SELECT ac.*, o.total as order_total, o.created_at FROM affiliate_conversions ac JOIN orders o ON o.id=ac.order_id WHERE ac.affiliate_id=? ORDER BY ac.created_at DESC LIMIT 20', [$aff['id']]);
        $payouts       = Database::fetchAll('SELECT * FROM affiliate_payouts WHERE affiliate_id=? ORDER BY created_at DESC', [$aff['id']]);
        $this->view('user/affiliate/dashboard', [
            'title'        => 'Affiliate Dashboard',
            'affiliate'    => $aff,
            'referralLink' => $referralLink,
            'referrals'    => $conversions,
            'payouts'      => $payouts
        ]);
    }

    public function requestPayout(Request $req): void
    {
        $this->requireAuth();
        $userId = auth()['id'];
        $aff    = Database::fetchOne('SELECT * FROM affiliates WHERE user_id=?', [$userId]);
        $amount = (float)$req->post('amount', 0);
        $method = $req->post('method', 'upi');
        $account= trim($req->post('account_info', ''));

        if ($amount < 100) {
            $this->error('Minimum payout is ₹100.');
        }
        if ($amount > $aff['pending_earnings']) {
            $this->error('Insufficient pending earnings.');
        }

        Database::execute('INSERT INTO affiliate_payouts (affiliate_id, amount, method, account_info) VALUES (?,?,?,?)', [$aff['id'], $amount, $method, $account]);
        $this->success('Payout request submitted! We review within 3 business days.');
    }
}
