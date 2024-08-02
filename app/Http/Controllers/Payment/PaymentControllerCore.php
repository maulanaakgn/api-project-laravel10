<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentControllerCore extends Controller
{
    public function create(Request $request)
    {
        $paymentType = $request->payment_type;
        $bankCode = $request->bank_code;
        $paymentMethodMapping = [
            'bank_transfer' => 'Bank Transfer',
            'echannel' => 'Mandiri Bill',
            'permata' => 'Permata',
            'credit_card' => 'Credit Card',
            'gopay' => 'GoPay',
        ];

        $params = [
            'transaction_details' => [
                'order_id' => 'INV-' . uniqid(),
                'gross_amount' => $request->price,
            ],
            'item_details' => [
                [
                    'price' => $request->price,
                    'quantity' => 1,
                    'name' => $request->item_name,
                ]
            ],
            'customer_details' => [
                'first_name' => $request->customer_first_name,
                'email' => $request->customer_email,
            ],
        ];

        if ($paymentType === 'bank_transfer') {
            $params['payment_type'] = 'bank_transfer';
            $params['bank_transfer'] = [
                'bank' => $bankCode,
            ];
        } elseif ($paymentType === 'echannel') {
            $params['payment_type'] = 'echannel';
            $params['echannel'] = [
                'bill_info1' => 'Payment:',
                'bill_info2' => 'Online purchase',
            ];
        } elseif ($paymentType === 'permata') {
            $params['payment_type'] = 'permata';
        } elseif ($paymentType === 'credit_card') {
            $params['payment_type'] = 'credit_card';
            $params['credit_card'] = [
                'token_id' => $request->token_id,
                'authentication' => $request->authentication ?? 'true',
            ];
        } elseif ($paymentType === 'gopay') {
            $params['payment_type'] = 'gopay';
        } else {
            return response()->json(['error' => 'Unsupported payment type'], 400);
        }

        $auth = base64_encode(env('MIDTRANS_SERVER_KEY') . ':');

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => "Basic $auth",
        ])->post('https://api.sandbox.midtrans.com/v2/charge', $params);

        $response = json_decode($response->body());

        $payment = new Payment;
        $payment->order_id = $params['transaction_details']['order_id'];
        $payment->status = 'pending';
        $payment->price = $request->price;
        $payment->currency = $response->currency ?? null;
        $payment->customer_first_name = $request->customer_first_name;
        $payment->customer_email = $request->customer_email;
        $payment->item_name = $request->item_name;
        $payment->bank_code = $bankCode ?? null;
        $payment->payment_method = $paymentMethodMapping[$paymentType] ?? 'Unknown Payment Method';
        $payment->checkout_link = null;

        if ($paymentType === 'bank_transfer' && isset($response->va_numbers)) {
            $payment->va_number = $response->va_numbers[0]->va_number;
        } elseif ($paymentType === 'permata' && isset($response->permata_va_number)) {
            $payment->va_number = $response->permata_va_number;
        } elseif ($paymentType === 'echannel') {
            $payment->bill_key = $response->bill_key ?? null;
            $payment->biller_code = $response->biller_code ?? null;
        } elseif ($paymentType === 'gopay' && isset($response->actions)) {
            $payment->checkout_link = $response->actions[0]->url;
        }

        $payment->save();

        if ($response->transaction_status === 'pending' && isset($response->redirect_url)) {
            return response()->json(['redirect_url' => $response->redirect_url], 200);
        }

        return response()->json($response);
    }

    public function webhook(Request $request)
    {
        $auth = base64_encode(env('MIDTRANS_SERVER_KEY') . ':');

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => "Basic $auth",
        ])->get("https://api.sandbox.midtrans.com/v2/{$request->order_id}/status");

        $response = json_decode($response->body());

        $payment = Payment::where('order_id', $response->order_id)->firstOrFail();

        if (in_array($payment->status, ['settlement', 'capture'])) {
            return response()->json('Payment has been already processed');
        }

        $statusMapping = [
            'capture' => 'capture',
            'settlement' => 'settlement',
            'pending' => 'pending',
            'deny' => 'deny',
            'expire' => 'expire',
            'cancel' => 'cancel',
        ];

        if (array_key_exists($response->transaction_status, $statusMapping)) {
            $payment->status = $statusMapping[$response->transaction_status];
            $payment->save();
        }

        return response()->json('success');
    }
}
