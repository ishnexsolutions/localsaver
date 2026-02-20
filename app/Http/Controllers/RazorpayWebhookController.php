<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RazorpayWebhookController extends Controller
{
    public function __construct(
        private RazorpayService $razorpayService
    ) {}

    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature');

        if (!$this->verifyWebhookSignature($payload, $signature)) {
            Log::warning('Razorpay webhook signature invalid');
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $data = json_decode($payload, true);
        $event = $data['event'] ?? null;

        if ($event === 'payment.captured') {
            $paymentData = $data['payload']['payment']['entity'] ?? [];
            $orderId = $paymentData['order_id'] ?? null;
            $paymentId = $paymentData['id'] ?? null;

            $payment = Payment::where('razorpay_order_id', $orderId)->first();
            if ($payment && $this->razorpayService->completePaymentFromWebhook($payment, $paymentId)) {
                Log::info('Webhook: Payment completed', ['payment_id' => $paymentId]);
            }
        }

        return response()->json(['success' => true]);
    }

    private function verifyWebhookSignature(string $payload, ?string $signature): bool
    {
        $secret = config('services.razorpay.webhook_secret');
        if (!$secret || !$signature) {
            return false;
        }
        return hash_hmac('sha256', $payload, $secret) === $signature;
    }
}
