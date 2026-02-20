<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Payment;
use Razorpay\Api\Api;

class RazorpayService
{
    private Api $api;

    public function __construct()
    {
        $this->api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
    }

    public function createOrder(Business $business, string $type = 'signup', int $amount = 19900): array
    {
        $order = $this->api->order->create([
            'amount' => $amount,
            'currency' => 'INR',
            'receipt' => 'rcpt_' . uniqid(),
            'notes' => [
                'business_id' => $business->id,
                'type' => $type,
            ],
        ]);

        Payment::create([
            'business_id' => $business->id,
            'amount' => $amount / 100,
            'type' => $type,
            'status' => 'pending',
            'razorpay_order_id' => $order->id,
            'metadata' => ['razorpay_order' => $order->toArray()],
        ]);

        return [
            'order_id' => $order->id,
            'amount' => $order->amount,
        ];
    }

    public function verifyPayment(string $orderId, string $paymentId, string $signature): bool
    {
        try {
            $this->api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $orderId,
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature' => $signature,
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function completePayment(Payment $payment, string $paymentId, string $signature): bool
    {
        if ($payment->status === 'completed') {
            return true;
        }
        if (!$this->verifyPayment($payment->razorpay_order_id, $paymentId, $signature)) {
            return false;
        }

        \DB::transaction(function () use ($payment, $paymentId, $signature) {
            $payment->update([
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature' => $signature,
                'status' => 'completed',
            ]);
            if ($payment->type === 'signup') {
                $payment->business->update([
                    'activated' => true,
                    'activated_at' => now(),
                ]);
            }
        });

        return true;
    }

    public function completePaymentFromWebhook(Payment $payment, string $paymentId): bool
    {
        if ($payment->status === 'completed') {
            return true;
        }
        try {
            $entity = $this->api->payment->fetch($paymentId);
            if ($entity->status !== 'captured') {
                return false;
            }
        } catch (\Throwable $e) {
            \Log::error('Razorpay webhook: payment fetch failed', ['error' => $e->getMessage()]);
            return false;
        }
        \DB::transaction(function () use ($payment, $paymentId) {
            $payment->update([
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature' => 'webhook',
                'status' => 'completed',
            ]);
            if ($payment->type === 'signup') {
                $payment->business->update([
                    'activated' => true,
                    'activated_at' => now(),
                ]);
            }
        });
        return true;
    }
}
