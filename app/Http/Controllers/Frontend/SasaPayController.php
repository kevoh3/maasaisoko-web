<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\SasaPayService;
use App\Models\Order_master;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SasaPayController extends Controller
{
    protected $sasaPayService;

    public function __construct(SasaPayService $sasaPayService)
    {
        $this->sasaPayService = $sasaPayService;
    }

    /**
     * Initiate SasaPay payment
     */
    public function initiatePayment(Request $request)
    {
        try {
            $request->validate([
                'order_id' => 'required|exists:order_masters,id',
                'phone_number' => 'required|string',
                'network_code' => 'required|string|in:MPESA,AIRTEL,EQUITEL',
            ]);

            $order = Order_master::findOrFail($request->order_id);
            
            // Generate transaction reference
            $transactionReference = $this->sasaPayService->generateTransactionReference();
            
            // Prepare payment data
            $paymentData = [
                'transaction_reference' => $transactionReference,
                'currency_code' => 'KES',
                'amount' => $order->total_amount,
                'sender_account_number' => $request->phone_number,
                'account_reference' => 'ORDER_' . $order->id,
                'charge_account' => 'SENDER',
                'transaction_fee' => 0,
                'biller_type' => 'PAYBILL',
                'network_code' => $request->network_code,
                'reason' => 'Payment for order #' . $order->order_no,
            ];

            // Create payment with SasaPay
            $response = $this->sasaPayService->createPayment($paymentData);

            if ($response && isset($response['status']) && $response['status'] === 'success') {
                // Update order with transaction reference
                $order->update([
                    'payment_method' => 'SasaPay',
                    'payment_status' => 'pending',
                    'transaction_id' => $transactionReference,
                ]);

                // Create payment transaction record
                PaymentTransaction::create([
                    'wallet_id' => null,
                    'amount' => $order->total_amount,
                    'type' => 'payment',
                    'status' => 'pending',
                    'transaction_code' => $transactionReference,
                    'description' => 'SasaPay payment for order #' . $order->order_no,
                    'channel' => 'SasaPay',
                    'reference_number' => $order->order_no,
                    'transaction_date' => now(),
                    'fees_and_charges' => 0,
                    'running_balance' => 0,
                    'party_b_name' => 'MaasaiSoko',
                    'party_b_account_number' => $this->sasaPayService->merchantCode,
                    'party_b_platform' => 'SasaPay'
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Payment initiated successfully',
                    'transaction_reference' => $transactionReference,
                    'payment_instructions' => $response['payment_instructions'] ?? 'Please complete payment on your mobile device',
                    'redirect_url' => route('frontend.sasapay.success', ['order_id' => $order->id])
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate payment: ' . ($response['message'] ?? 'Unknown error')
            ], 400);

        } catch (\Exception $e) {
            Log::error('SasaPay Payment Initiation Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Payment initiation failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Handle SasaPay callback/webhook
     */
    public function handleCallback(Request $request)
    {
        try {
            Log::info('SasaPay Callback Received: ' . json_encode($request->all()));

            $callbackData = $request->all();
            $result = $this->sasaPayService->processCallback($callbackData);

            if ($result['success']) {
                $transactionReference = $result['transaction_reference'];
                $status = $result['status'];

                // Find the order by transaction reference
                $order = Order_master::where('transaction_id', $transactionReference)->first();

                if ($order) {
                    // Update order status based on payment status
                    if ($status === 'completed' || $status === 'success') {
                        $order->update([
                            'payment_status' => 'completed',
                            'order_status' => 'confirmed'
                        ]);

                        // Update payment transaction
                        PaymentTransaction::where('transaction_code', $transactionReference)
                            ->update(['status' => 'completed']);

                        Log::info('SasaPay Payment Completed for Order: ' . $order->id);
                    } elseif ($status === 'failed' || $status === 'cancelled') {
                        $order->update([
                            'payment_status' => 'failed',
                            'order_status' => 'cancelled'
                        ]);

                        // Update payment transaction
                        PaymentTransaction::where('transaction_code', $transactionReference)
                            ->update(['status' => 'failed']);

                        Log::info('SasaPay Payment Failed for Order: ' . $order->id);
                    }
                }
            }

            return response()->json(['status' => 'received']);

        } catch (\Exception $e) {
            Log::error('SasaPay Callback Processing Error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Check payment status
     */
    public function checkPaymentStatus(Request $request)
    {
        try {
            $request->validate([
                'transaction_reference' => 'required|string'
            ]);

            $status = $this->sasaPayService->checkPaymentStatus($request->transaction_reference);

            return response()->json([
                'success' => true,
                'status' => $status
            ]);

        } catch (\Exception $e) {
            Log::error('SasaPay Status Check Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to check payment status'
            ], 500);
        }
    }

    /**
     * Show SasaPay payment form
     */
    public function showPaymentForm(Request $request)
    {
        $orderId = $request->order_id;
        $order = Order_master::findOrFail($orderId);

        return view('frontend.sasapay-payment', compact('order'));
    }

    /**
     * Handle successful payment
     */
    public function paymentSuccess(Request $request)
    {
        $orderId = $request->order_id;
        $order = Order_master::findOrFail($orderId);

        return view('frontend.payment-success', compact('order'));
    }

    /**
     * Handle cancelled payment
     */
    public function paymentCancel(Request $request)
    {
        $orderId = $request->order_id;
        $order = Order_master::findOrFail($orderId);

        // Update order status
        $order->update([
            'payment_status' => 'cancelled',
            'order_status' => 'cancelled'
        ]);

        return view('frontend.payment-cancel', compact('order'));
    }
}
