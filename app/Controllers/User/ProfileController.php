<?php
// ============================================================
// THEMORA SHOP — User Profile Controller
// ============================================================
namespace App\Controllers\User;
use App\Core\{Controller, Request, Database, Session};

class ProfileController extends Controller
{
    public function index(Request $req): void
    {
        $this->requireAuth();
        $user = Database::fetchOne('SELECT * FROM users WHERE id=?', [auth()['id']]);
        $this->view('user/account/profile', ['title' => 'My Profile', 'profile' => $user, 'success' => Session::getFlash('success'), 'error' => Session::getFlash('error')]);
    }

    public function update(Request $req): void
    {
        $this->requireAuth();
        $name     = trim($req->post('name', ''));
        $email    = strtolower(trim($req->post('email', '')));
        $bio      = trim($req->post('bio', ''));
        $userId   = auth()['id'];

        if (strlen($name) < 2) {
            $this->flashError('Name too short.');
            $this->redirect(url('dashboard/profile'));
        }

        // Handle avatar upload
        $avatar = null;
        if ($req->file('avatar') && $req->file('avatar')['error'] === 0) {
            $dir  = PUB_PATH . '/assets/images/avatars';
            if (!is_dir($dir)) @mkdir($dir, 0755, true);
            $ext  = strtolower(pathinfo($req->file('avatar')['name'], PATHINFO_EXTENSION));
            $name2= uniqid('av_') . '.' . $ext;
            if (move_uploaded_file($req->file('avatar')['tmp_name'], $dir . '/' . $name2)) {
                $avatar = 'images/avatars/' . $name2;
            }
        }

        $sql    = 'UPDATE users SET name=?, bio=?' . ($avatar ? ', avatar=?' : '') . ' WHERE id=?';
        $params = $avatar ? [$name, $bio, $avatar, $userId] : [$name, $bio, $userId];
        Database::execute($sql, $params);

        $user = Database::fetchOne('SELECT * FROM users WHERE id=?', [$userId]);
        Session::set('user', ['id'=>$user['id'],'name'=>$user['name'],'email'=>$user['email'],'role'=>$user['role'],'avatar'=>$user['avatar']]);
        $this->flashSuccess('Profile updated!');
        $this->redirect(url('dashboard/profile'));
    }

    public function updatePassword(Request $req): void
    {
        $this->requireAuth();
        $this->verifyCsrf();
        $current = $req->post('current_password', '');
        $new     = $req->post('new_password', '');
        $confirm = $req->post('confirm_password', '');
        $userId  = auth()['id'];

        $user = Database::fetchOne('SELECT password FROM users WHERE id=?', [$userId]);
        if (!password_verify($current, $user['password'])) {
            $this->flashError('Current password incorrect.');
            $this->redirect(url('dashboard/profile'));
        }

        if ($new !== $confirm || strlen($new) < 8) {
            $this->flashError('Passwords must match and be at least 8 characters.');
            $this->redirect(url('dashboard/profile'));
        }

        Database::execute('UPDATE users SET password=? WHERE id=?', [password_hash($new, PASSWORD_BCRYPT, ['cost' => 12]), $userId]);
        $this->flashSuccess('Password updated successfully.');
        $this->redirect(url('dashboard/profile'));
    }

    public function updateNotifications(Request $req): void
    {
        $this->requireAuth();
        $this->verifyCsrf();
        $userId = auth()['id'];
        
        $notifKeys = [
            'notif_order_success',
            'notif_download_expiry',
            'notif_newsletter',
            'notif_affiliate'
        ];

        $sets = [];
        $params = [];
        foreach ($notifKeys as $key) {
            $val = $req->post($key) ? 1 : 0;
            $sets[] = "{$key}=?";
            $params[] = $val;
        }
        $params[] = $userId;
        
        Database::execute("UPDATE users SET " . implode(', ', $sets) . " WHERE id=?", $params);
        $this->flashSuccess('Notification preferences updated.');
        $this->redirect(url('dashboard/profile'));
    }

    public function twoFaSetup(Request $req): void
    {
        $this->requireAuth();
        $user = Database::fetchOne('SELECT * FROM users WHERE id=?', [auth()['id']]);
        
        // Generate secret if not exists
        if (empty($user['totp_secret'])) {
            $secret = $this->generateSecret();
            Database::execute('UPDATE users SET totp_secret=? WHERE id=?', [$secret, $user['id']]);
            $user['totp_secret'] = $secret;
        }

        $this->view('user/account/profile-2fa', [
            'title'   => '2FA Setup',
            'user'    => $user,
            'success' => Session::getFlash('success'),
            'error'   => Session::getFlash('error')
        ]);
    }

    public function twoFaEnable(Request $req): void
    {
        $this->requireAuth();
        $this->verifyCsrf();
        $code = $req->post('code', '');
        $user = Database::fetchOne('SELECT * FROM users WHERE id=?', [auth()['id']]);

        // Verify code
        if ($this->verifyTotp($user['totp_secret'], $code)) {
            Database::execute('UPDATE users SET is_2fa_enabled=1 WHERE id=?', [$user['id']]);
            $this->flashSuccess('Two-Factor Authentication has been enabled.');
            $this->redirect(url('dashboard/profile'));
        } else {
            $this->flashError('Invalid 2FA code. Setup failed.');
            $this->redirect(url('dashboard/profile/2fa'));
        }
    }

    public function twoFaDisable(Request $req): void
    {
        $this->requireAuth();
        $this->verifyCsrf();
        Database::execute('UPDATE users SET is_2fa_enabled=0, totp_secret=NULL WHERE id=?', [auth()['id']]);
        $this->flashSuccess('Two-Factor Authentication has been disabled.');
        $this->redirect(url('dashboard/profile'));
    }

    private function generateSecret(int $length = 16): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $res = '';
        for ($i = 0; $i < $length; $i++) {
            $res .= $chars[random_int(0, 31)];
        }
        return $res;
    }

    private function verifyTotp(string $secret, string $code): bool
    {
        $time = floor(time() / 30);
        $base32 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = strtoupper($secret);
        $keyBytes = '';
        $buffer = 0;
        $bits = 0;
        foreach (str_split($secret) as $ch) {
            $pos = strpos($base32, $ch);
            if ($pos === false) continue;
            $buffer = ($buffer << 5) | $pos;
            $bits += 5;
            if ($bits >= 8) { $bits -= 8; $keyBytes .= chr(($buffer >> $bits) & 0xff); }
        }
        for ($i = -1; $i <= 1; $i++) {
            $t = pack('N*', 0) . pack('N*', $time + $i);
            $hmac = hash_hmac('sha1', $t, $keyBytes, true);
            $off = ord($hmac[19]) & 0x0f;
            $num = (((ord($hmac[$off]) & 0x7f) << 24 | (ord($hmac[$off + 1]) & 0xff) << 16 | (ord($hmac[$off + 2]) & 0xff) << 8 | (ord($hmac[$off + 3]) & 0xff)) % 1000000);
            if (str_pad($num, 6, '0', STR_PAD_LEFT) === str_pad($code, 6, '0', STR_PAD_LEFT)) return true;
        }
        return false;
    }

    public function wishlist(Request $req): void
    {
        $this->requireAuth();
        $items = Database::fetchAll('SELECT p.*, c.name as category_name FROM wishlist w JOIN products p ON p.id=w.product_id LEFT JOIN categories c ON c.id=p.category_id WHERE w.user_id=? ORDER BY w.created_at DESC', [auth()['id']]);
        $this->view('user/account/wishlist', ['title' => 'My Wishlist', 'wishlist' => $items]);
    }

    public function toggleWishlist(Request $req): void
    {
        $this->requireAuth();
        $productId = (int)$req->post('product_id');
        $userId    = auth()['id'];
        $exists    = Database::fetchOne('SELECT id FROM wishlist WHERE user_id=? AND product_id=?', [$userId, $productId]);
        if ($exists) {
            Database::execute('DELETE FROM wishlist WHERE user_id=? AND product_id=?', [$userId, $productId]);
            $action = 'removed';
        } else {
            Database::execute('INSERT INTO wishlist (user_id, product_id) VALUES (?,?)', [$userId, $productId]);
            $action = 'added';
        }
        $this->success($action === 'added' ? 'Added to wishlist' : 'Removed from wishlist', ['action' => $action]);
    }
}
