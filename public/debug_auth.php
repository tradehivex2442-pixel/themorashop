<?php
// THEMORA SHOP — Ultimate OAuth Debugger
require_once dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Session;

// Handle form submission to test different values
$testClientId = $_POST['client_id'] ?? env('GOOGLE_CLIENT_ID');
$testRedirectUri = $_POST['redirect_uri'] ?? env('GOOGLE_REDIRECT_URI', url('auth/google/callback'));
$testScope = $_POST['scope'] ?? 'openid email profile';

echo "<!DOCTYPE html><html><head><title>OAuth Debugger</title>";
echo "<style>body{font-family:sans-serif;padding:20px;line-height:1.6;background:#f9f9f9} .box{background:#fff;padding:20px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1);max-width:800px;margin:auto} input,select{width:100%;padding:10px;margin:10px 0;border:1px solid #ccc;border-radius:4px} button{padding:10px 20px;background:#4285f4;color:#fff;border:none;border-radius:4px;cursor:pointer} .log{background:#000;color:#0f0;padding:15px;border-radius:5px;font-family:monospace;margin-top:20px}</style></head><body>";

echo "<div class='box'>";
echo "<h1>Ultimate OAuth Debugger</h1>";
echo "<p>Test different Client ID and Redirect URI combinations without editing .env files.</p>";

echo "<form method='POST'>";
echo "<label><strong>Google Client ID:</strong></label>";
echo "<input type='text' name='client_id' value='" . htmlspecialchars($testClientId) . "' placeholder='Paste your Client ID here'>";

echo "<label><strong>Redirect URI:</strong> (Must match Google Console EXACTLY)</label>";
echo "<input type='text' name='redirect_uri' value='" . htmlspecialchars($testRedirectUri) . "' placeholder='e.g., http://localhost:8080/themora_Shop/public/auth/google/callback'>";

echo "<label><strong>Scope:</strong></label>";
echo "<input type='text' name='scope' value='" . htmlspecialchars($testScope) . "'>";

echo "<button type='submit'>Refresh Test Link</button>";
echo "</form>";

echo "<h2>1. Copy this to Google Cloud Console</h2>";
echo "<div class='log'>" . htmlspecialchars($testRedirectUri) . "</div>";

echo "<h2>2. Test with Google</h2>";
$params = http_build_query([
    'client_id'     => $testClientId,
    'redirect_uri'  => $testRedirectUri,
    'response_type' => 'code',
    'scope'         => $testScope,
    'state'         => 'debug_' . time(),
    'access_type'   => 'online',
], '', '&', PHP_QUERY_RFC3986);

$testUrl = "https://accounts.google.com/o/oauth2/v2/auth?" . $params;

echo "<p>Click below to test. If Google shows 'Invalid Request', the Client ID is likely wrong. If it shows 'mismatch', the Redirect URI is the problem.</p>";
echo "<a href='$testUrl' target='_blank' style='display:inline-block;padding:12px 24px;background:#34a853;color:#fff;text-decoration:none;border-radius:5px;font-weight:bold'>🚀 Execute Google Auth Test</a>";

echo "<hr>";
echo "<h3>Generated URL (Full)</h3>";
echo "<div class='log' style='word-break:break-all;font-size:12px'>" . htmlspecialchars($testUrl) . "</div>";

echo "</div></body></html>";
