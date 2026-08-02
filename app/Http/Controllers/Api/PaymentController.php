<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function initiatePayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100',
            'description' => 'required|string'
        ]);

        $user = auth()->user();
        $amount = $request->amount * 100; // Paystack uses kobo

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY'),
            'Content-Type' => 'application/json'
        ])->post('https://api.paystack.co/transaction/initialize', [
            'email' => $user->email,
            'amount' => $amount,
            'currency' => 'NGN',
            'metadata' => [
                'student_id' => $user->id,
                'description' => $request->description
            ]
        ]);

        $data = $response->json();

        if (!$data['status']) {
            return response()->json(['error' => 'Payment initiation failed'], 500);
        }

        // Save pending fee record
        Fee::create([
            'student_id' => $user->id,
            'description' => $request->description,
            'amount' => $request->amount,
            'status' => 'pending',
            'transaction_reference' => $data['data']['reference']
        ]);

        return response()->json([
            'reference' => $data['data']['reference'],
            'authorization_url' => $data['data']['authorization_url']
        ]);
    }

    public function handleWebhook(Request $request)
    {
        // Verify webhook signature
        $signature = $request->header('x-paystack-signature');
        $expected = hash_hmac('sha512', $request->getContent(), env('PAYSTACK_WEBHOOK_SECRET'));

        if ($signature !== $expected) {
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $event = $request->all();

        if ($event['event'] === 'charge.success') {
            $reference = $event['data']['reference'];
            
            $fee = Fee::where('transaction_reference', $reference)->first();
            if ($fee) {
                $fee->status = 'paid';
                $fee->paid_at = now();
                $fee->save();
                Log::info("Payment verified for reference: $reference");
            }
        }

        return response()->json(['message' => 'Webhook received']);
    }
}