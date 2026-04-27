<?php
// ============================================================
// THEMORA SHOP — Auth Controller
// ============================================================

namespace App\Controllers\User;

use App\Core\{Controller, Request, Database, Session, Response};

class AuthController extends Controller
{
    // ── Login Form ────────────────────────────────────────────
    public function loginForm(Request $req): void
    {
        if (logged_in()) $this->redirect(url('dashboard'));
        $this->view('user/account/login', [
            'title'   => 'Login — ' . setting('site_name'),
            'error'   => Session::getFlash('error'),
            'success' => Session::getFlash('success'),
            'info'    => Session::getFlash('info'),
        ]);
    }

    // ── Login POST ────────────────────────────────────────────
    public function login(Request $req): void
    {
        if (!verify_csrf()) {
            flash_error('Invalid form token.');
            $this->redirect(url('login'));
        }

        $email    = trim($req->post('email', ''));
        $password = $req->post('password', '');
        $ip       = $req->ip();

        // Rate limit
        if (rate_limit("login_{$ip}", 5, 60)) {
            flash_error('Too many login attempts. Please wait a minute.');
            $this->redirect(url('login'));
        }

        // Check brute-force lockout (5 fails in 15 min)
        $attempts = Database::fetchOne(
            'SELECT COUNT(*) as cnt FROM login_attempts WHERE identifier = ? AND attempted_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)',
            [$email]
        );
        if ($attempts['cnt'] >= 5) {
            flash_error('Account temporarily locked due to too many failed login attempts. Try again in 15 minutes.');
            $this->redirect(url('login'));
        }

        $user = Database::fetchOne('SELECT * FROM users WHERE email = ?', [$email]);

        if (!$user || !password_verify($password, $user['password'])) {
            Database::execute(
                'INSERT INTO login_attempts (identifier, ip) VALUES (?, ?)',
                [$email, $ip]
            );
            flash_error('Invalid email or password.');
            Session::flash('old_input', ['email' => $email]);
            $this->redirect(url('login'));
        }

        if ($user['is_blocked']) {
            flash_error('Your account has been suspended. Contact support.');
            $this->redirect(url('login'));
        }

        // 2FA check
        if ($user['is_2fa_enabled']) {
            Session::set('2fa_pending_user', $user['id']);
            $this->redirect(url('2fa'));
        }

        $this->loginUser($user);
    }

    // ── 2FA Form ──────────────────────────────────────────────
    public function twoFaForm(Request $req): void
    {
        if (!Session::has('2fa_pending_user')) $this->redirect(url('login'));
        $this->view('user/account/two-fa', ['title' => '2FA Verification']);
    }

    public function twoFaVerify(Request $req): void
    {
        $userId = Session::get('2fa_pending_user');
        if (!$userId) $this->redirect(url('login'));

        $code = $req->post('code', '');
        $user = Database::fetchOne('SELECT * FROM users WHERE id = ?', [$userId]);

        // Verify TOTP
        if (!$this->verifyTotp($user['totp_secret'], $code)) {
            flash_error('Invalid 2FA code. Please try again.');
            $this->redirect(url('2fa'));
        }

        Session::forget('2fa_pending_user');
        $this->loginUser($user);
    }

    // ── Signup Form ───────────────────────────────────────────
    public function signupForm(Request $req): void
    {
        if (logged_in()) $this->redirect(url('dashboard'));
        $this->view('user/account/signup', [
            'title' => 'Create Account — ' . setting('site_name'),
            'error' => Session::getFlash('error'),
        ]);
    }

    // ── Signup POST ───────────────────────────────────────────
    public function signup(Request $req): void
    {
        if (!verify_csrf()) { flash_error('Invalid token.'); $this->redirect(url('signup')); }

        $name     = trim($req->post('name', ''));
        $email    = strtolower(trim($req->post('email', '')));
        $password = $req->post('password', '');
        $confirm  = $req->post('password_confirm', '');
        $refCode  = trim($req->post('ref', ''));

        // Validation
        $errors = [];
        if (strlen($name) < 2)    $errors[] = 'Name must be at least 2 characters.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address.';
        if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
        if ($password !== $confirm) $errors[] = 'Passwords do not match.';

        if (!empty($errors)) {
            flash_error(implode('<br>', $errors));
            Session::flash('old_input', $req->post());
            $this->redirect(url('signup'));
        }

        // Check duplicate
        if (Database::fetchOne('SELECT id FROM users WHERE email = ?', [$email])) {
            flash_error('An account with this email already exists.');
            $this->redirect(url('signup'));
        }

        // Referral
        $referredBy = null;
        if ($refCode) {
            $ref = Database::fetchOne('SELECT id FROM users WHERE referral_code = ?', [$refCode]);
            if ($ref) $referredBy = $ref['id'];
        }

        $hash     = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $myCode   = strtoupper(substr(md5($email . time()), 0, 8));

        $userId = Database::insert(
            'INSERT INTO users (name, email, password, referral_code, referred_by, email_verified) VALUES (?, ?, ?, ?, ?, 1)',
            [$name, $email, $hash, $myCode, $referredBy]
        );

        // Create affiliate entry
        Database::execute('INSERT INTO affiliates (user_id) VALUES (?)', [$userId]);

        $user = Database::fetchOne('SELECT * FROM users WHERE id = ?', [$userId]);
        $this->loginUser($user, url('dashboard'));
    }

    // ── Forgot Password ───────────────────────────────────────
    public function forgotForm(Request $req): void
    {
        $success = Session::getFlash('success');
        $this->view('user/account/forgot-password', [
            'title'   => 'Forgot Password',
            'success' => $success,
            'sent'    => (bool)$success,
            'error'   => Session::getFlash('error'),
        ]);
    }

    public function forgot(Request $req): void
    {
        $email = strtolower(trim($req->post('email', '')));
        $user  = Database::fetchOne('SELECT id, name FROM users WHERE email = ?', [$email]);

        // Always show success to prevent email enumeration
        if ($user) {
            $token   = generate_token();
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            Database::execute('DELETE FROM password_resets WHERE email = ?', [$email]);
            Database::execute(
                'INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)',
                [$email, $token, $expires]
            );

            $resetLink = url("reset-password/{$token}");
            // TODO: Send email - for now log it
            error_log("Password reset link for {$email}: {$resetLink}");
        }

        flash_success('If that email exists, a password reset link has been sent.');
        $this->redirect(url('forgot-password'));
    }

    // ── Reset Password ────────────────────────────────────────
    public function resetForm(Request $req): void
    {
        $token = $req->param('token');
        $reset = Database::fetchOne(
            'SELECT * FROM password_resets WHERE token = ? AND used = 0 AND expires_at > NOW()',
            [$token]
        );
        if (!$reset) {
            flash_error('This reset link is invalid or has expired.');
            $this->redirect(url('forgot-password'));
        }
        $this->view('user/account/reset-password', ['title' => 'Reset Password', 'token' => $token]);
    }

    public function reset(Request $req): void
    {
        $token    = $req->post('token');
        $password = $req->post('password');
        $confirm  = $req->post('password_confirm');

        $reset = Database::fetchOne(
            'SELECT * FROM password_resets WHERE token = ? AND used = 0 AND expires_at > NOW()',
            [$token]
        );
        if (!$reset) { flash_error('Invalid or expired reset link.'); $this->redirect(url('login')); }
        if ($password !== $confirm || strlen($password) < 8) {
            flash_error('Passwords must match and be at least 8 characters.');
            $this->redirect(url("reset-password/{$token}"));
        }

        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        Database::execute('UPDATE users SET password = ? WHERE email = ?', [$hash, $reset['email']]);
        Database::execute('UPDATE password_resets SET used = 1 WHERE token = ?', [$token]);

        flash_success('Password reset successfully. Please log in.');
        $this->redirect(url('login'));
    }

    // ── Logout ────────────────────────────────────────────────
    public function logout(Request $req): void
    {
        Session::destroy();
        $this->redirect(url('/'));
    }

    // ── OAuth ─────────────────────────────────────────────────
    public function googleRedirect(Request $req): void
    {
        $clientId = setting('google_client_id', env('GOOGLE_CLIENT_ID'));
        
        // --- Dev/Mock Mode ---
        if ($clientId === 'mock') {
            $this->oauthLogin('user@themorashop.com', 'Demo User', 'google', 'mock_123');
            return;
        }

        if (empty($clientId)) {
            flash_error('Google Login is not configured. Please add your credentials to the .env file.');
            $this->redirect(url('login'));
        }

        // Allow manual override of redirect URI for debugging
        $redirectUri = env('GOOGLE_REDIRECT_URI', url('auth/google/callback'));

        // Generate CSRF state
        $state = bin2hex(random_bytes(16));
        Session::set('oauth_state', $state);

        $params = http_build_query([
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code',
            'scope'         => 'email profile', // Use simpler scope
            'state'         => $state,
            'access_type'   => 'online',
        ], '', '&', PHP_QUERY_RFC3986);

        $finalUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . $params;

        // Log for diagnostics
        error_log("Google OAuth Redirect URI sent: " . $redirectUri);
        error_log("Full Google OAuth URL generated: " . $finalUrl);

        $this->redirect($finalUrl);
    }

    public function googleCallback(Request $req): void
    {
        $code  = $req->get('code');
        $state = $req->get('state');

        // Verify state
        if (!$code || !$state || $state !== Session::get('oauth_state')) {
            Session::forget('oauth_state');
            flash_error('Authentication failed or session expired. Please try again.');
            $this->redirect(url('login'));
        }
        Session::forget('oauth_state');

        // Exchange code for token
        $response = $this->httpPost('https://oauth2.googleapis.com/token', [
            'code'          => $code,
            'client_id'     => setting('google_client_id', env('GOOGLE_CLIENT_ID')),
            'client_secret' => setting('google_client_secret', env('GOOGLE_CLIENT_SECRET')),
            'redirect_uri'  => url('auth/google/callback'),
            'grant_type'    => 'authorization_code',
        ]);

        $token    = json_decode($response, true);
        $userInfo = json_decode($this->httpGet('https://www.googleapis.com/oauth2/v3/userinfo', $token['access_token']), true);

        $this->oauthLogin($userInfo['email'], $userInfo['name'], 'google', $userInfo['sub']);
    }

    public function githubRedirect(Request $req): void
    {
        $clientId = setting('github_client_id', env('GITHUB_CLIENT_ID'));
        
        // --- Mock Mode ---
        if ($clientId === 'mock') {
            $this->oauthLogin('demo_github@themorashop.com', 'GitHub Demo', 'github', 'mock_gh_123');
            return;
        }

        if (empty($clientId)) {
            flash_error('GitHub Login is not configured. Please add your credentials to the .env file.');
            $this->redirect(url('login'));
        }

        $params = http_build_query([
            'client_id'    => $clientId,
            'redirect_uri' => url('auth/github/callback'),
            'scope'        => 'user:email',
        ]);
        $this->redirect('https://github.com/login/oauth/authorize?' . $params);
    }

    public function githubCallback(Request $req): void
    {
        $code = $req->get('code');
        if (!$code) { flash_error('GitHub auth failed.'); $this->redirect(url('login')); }

        $response = $this->httpPost('https://github.com/login/oauth/access_token', [
            'client_id'     => setting('github_client_id', env('GITHUB_CLIENT_ID')),
            'client_secret' => setting('github_client_secret', env('GITHUB_CLIENT_SECRET')),
            'code'          => $code,
        ], ['Accept: application/json']);

        $token    = json_decode($response, true);
        $userInfo = json_decode($this->httpGet('https://api.github.com/user', $token['access_token'], 'github'), true);
        $email    = $userInfo['email'] ?? $userInfo['login'] . '@github.local';
        $name     = $userInfo['name'] ?? $userInfo['login'];

        $this->oauthLogin($email, $name, 'github', (string)$userInfo['id']);
    }

    // ── Private helpers ───────────────────────────────────────
    private function loginUser(array $user, string $redirectTo = null): never
    {
        Session::regenerate();
        Session::set('user', [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role'],
            'avatar'=> $user['avatar'],
        ]);

        // Update last login (best effort)
        Database::execute('DELETE FROM login_attempts WHERE identifier = ?', [$user['email']]);

        $dest = $redirectTo ?? (in_array($user['role'], ['editor', 'support', 'super_admin'])
            ? url('admin')
            : url('dashboard'));

        $this->redirect($dest);
    }

    private function oauthLogin(string $email, string $name, string $provider, string $provId): never
    {
        $col  = $provider . '_id';
        $user = Database::fetchOne('SELECT * FROM users WHERE email = ?', [$email]);

        if (!$user) {
            $code   = strtoupper(substr(md5($email . time()), 0, 8));
            $userId = Database::insert(
                "INSERT INTO users (name, email, {$col}, referral_code, email_verified) VALUES (?, ?, ?, ?, 1)",
                [$name, $email, $provId, $code]
            );
            Database::execute('INSERT INTO affiliates (user_id) VALUES (?)', [$userId]);
            $user = Database::fetchOne('SELECT * FROM users WHERE id = ?', [$userId]);
        } elseif (!$user[$col]) {
            Database::execute("UPDATE users SET {$col} = ? WHERE id = ?", [$provId, $user['id']]);
        }

        $this->loginUser($user);
    }

    private function verifyTotp(string $secret, string $code): bool
    {
        // Basic TOTP verification (30s window, ±1 step tolerance)
        $time     = floor(time() / 30);
        $base32   = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret   = strtoupper($secret);
        $keyBytes = '';
        $buffer   = 0;
        $bits     = 0;
        foreach (str_split($secret) as $ch) {
            $pos = strpos($base32, $ch);
            if ($pos === false) continue;
            $buffer = ($buffer << 5) | $pos;
            $bits  += 5;
            if ($bits >= 8) { $bits -= 8; $keyBytes .= chr(($buffer >> $bits) & 0xff); }
        }

        for ($i = -1; $i <= 1; $i++) {
            $t   = pack('N*', 0) . pack('N*', $time + $i);
            $hmac = hash_hmac('sha1', $t, $keyBytes, true);
            $off  = ord($hmac[19]) & 0x0f;
            $num  = (
                (ord($hmac[$off]) & 0x7f) << 24 |
                (ord($hmac[$off + 1]) & 0xff) << 16 |
                (ord($hmac[$off + 2]) & 0xff) << 8 |
                (ord($hmac[$off + 3]) & 0xff)
            ) % 1000000;
            if (str_pad($num, 6, '0', STR_PAD_LEFT) === str_pad($code, 6, '0', STR_PAD_LEFT)) {
                return true;
            }
        }
        return false;
    }

    private function httpPost(string $url, array $data, array $headers = []): string
    {
        $ctx = stream_context_create(['http' => [
            'method'  => 'POST',
            'header'  => implode("\r\n", array_merge(['Content-Type: application/x-www-form-urlencoded'], $headers)),
            'content' => http_build_query($data),
        ]]);
        return @file_get_contents($url, false, $ctx) ?: '';
    }

    private function httpGet(string $url, string $token, string $provider = 'google'): string
    {
        $authHeader = $provider === 'github'
            ? "Authorization: token {$token}"
            : "Authorization: Bearer {$token}";
        $ctx = stream_context_create(['http' => [
            'method' => 'GET',
            'header' => $authHeader . "\r\nUser-Agent: ThemoraSop/1.0",
        ]]);
        return @file_get_contents($url, false, $ctx) ?: '{}';
    }
}
