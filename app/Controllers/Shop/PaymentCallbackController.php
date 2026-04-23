<?php
// ============================================================
// THEMORA SHOP — Payment Callback Controller
// ============================================================
namespace App\Controllers\Shop;
use App\Core\{Controller, Request, Database, Response};

class PaymentCallbackController extends Controller
{
    public function razorpay(Request $req): void
    {
        $body      = file_get_contents('php://input');
        $signature = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] ?? '';
        $secret    = env('RAZORPAY_KEY_SECRET');

        $expectedSig = hash_hmac('sha256', $body, $secret);
        if (!hash_equals($expectedSig, $signature)) {
            http_response_code(400); die('Invalid signature');
        }

        $event = json_decode($body, true);
        if ($event['event'] === 'payment.captured') {
            $txnId   = $event['payload']['payment']['entity']['id'];
            $orderId = $event['payload']['payment']['entity']['notes']['order_id'] ?? null;
            if ($orderId) {
                Database::execute('UPDATE orders SET status="paid", transaction_id=? WHERE id=? AND status="pending"', [$txnId, $orderId]);
            }
        }
        http_response_code(200);
        echo 'OK';
    }

    public function stripe(Request $req): void
    {
        $payload = file_get_contents('php://input');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        $secret    = env('STRIPE_WEBHOOK_SECRET');

        // Simplified Stripe webhook verification
        $parts    = explode(',', $sigHeader);
        $ts       = ''; $v1 = '';
        foreach ($parts as $p) {
            if (str_starts_with($p, 't=')) $ts = substr($p, 2);
            if (str_starts_with($p, 'v1=')) $v1 = substr($p, 3);
        }
        $expected = hash_hmac('sha256', "{$ts}.{$payload}", $secret);
        if (!hash_equals($expected, $v1)) {
            http_response_code(400); die('Invalid signature');
        }

        $event = json_decode($payload, true);
        if ($event['type'] === 'payment_intent.succeeded') {
            $txnId   = $event['data']['object']['id'];
            $orderId = $event['data']['object']['metadata']['order_id'] ?? null;
            if ($orderId) {
                Database::execute('UPDATE orders SET status="paid", transaction_id=? WHERE id=? AND status="pending"', [$txnId, $orderId]);
            }
        }
        http_response_code(200);
    }

    public function paypal(Request $req): void
    {
        // PayPal IPN verification placeholder
        $body  = file_get_contents('php://input');
        $event = json_decode($body, true);
        if (($event['event_type'] ?? '') === 'PAYMENT.CAPTURE.COMPLETED') {
            $txnId   = $event['resource']['id'];
            $orderId = $event['resource']['custom_id'] ?? null;
            if ($orderId) {
                Database::execute('UPDATE orders SET status="paid", transaction_id=? WHERE id=? AND status="pending"', [$txnId, $orderId]);
            }
        }
        http_response_code(200);
    }
}
