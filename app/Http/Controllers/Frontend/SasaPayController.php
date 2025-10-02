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
            Log::info('SasaPay Initiate: incoming request', [
                'order_id' => $request->input('order_id'),
                'payer_email' => $request->input('payer_email'),
                'ip' => $request->ip(),
                'ua' => $request->userAgent()
            ]);
            $request->validate([
                'order_id' => 'required|exists:order_masters,id',
                'payer_email' => 'required|email',
            ]);

            $order = Order_master::findOrFail($request->order_id);
            
            // Generate transaction reference
            $transactionReference = $this->sasaPayService->generateTransactionReference();
            
            // Prepare payment data for checkout API
            $paymentData = [
                'transaction_reference' => $transactionReference,
                'currency_code' => 'KES',
                'amount' => $order->total_amount,
                'payer_email' => $request->payer_email,
                'description' => 'Payment for order #' . $order->order_no,
                'success_url' => route('frontend.sasapay.success', ['order_id' => $order->id]),
                'failure_url' => route('frontend.sasapay.failure', ['order_id' => $order->id]),
                'sasapay_wallet_enabled' => true,
                'mpesa_enabled' => true,
                'card_enabled' => true,
                'airtel_enabled' => true,
                'tkash_enabled' => true,
            ];

            // Create checkout payment with SasaPay
            $response = $this->sasaPayService->createCheckoutPayment($paymentData);
            Log::info('SasaPay Initiate: API response', [
                'order_id' => $order->id,
                'transaction_reference' => $transactionReference,
                'response' => $response
            ]);

            // Accept either 'CheckoutUrl' (per docs) or 'checkout_url'
            $checkoutUrl = $response['checkout_url'] ?? ($response['CheckoutUrl'] ?? null);
            if ($response && $checkoutUrl) {
                // Update order with transaction reference (column: transaction_no)
                $order->update([
                    'payment_method' => 'SasaPay',
                    'payment_status' => 'pending',
                    'transaction_no' => $transactionReference,
                ]);

                // Get user's wallet ID for transaction tracking
                $walletId = null;
                if ($order->customer_id) {
                    $wallet = \App\Models\Wallet::where('user_id', $order->customer_id)->first();
                    $walletId = $wallet ? $wallet->id : null;
                }

                // Create payment transaction record (only if wallet exists)
                if ($walletId) {
                    PaymentTransaction::create([
                        'wallet_id' => $walletId,
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
                        'party_b_account_number' => $this->sasaPayService->getMerchantCode(),
                        'party_b_platform' => 'SasaPay'
                    ]);
                }

                Log::info('SasaPay Initiate: redirecting to checkout', [
                    'order_id' => $order->id,
                    'checkout_url' => $checkoutUrl
                ]);
                
                // Redirect directly to SasaPay checkout
                return redirect($checkoutUrl);
            }

            return redirect()->back()->with('error', 'Failed to initiate payment: ' . ($response['message'] ?? 'Unknown error'));

        } catch (\Exception $e) {
            Log::error('SasaPay Initiate: error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Payment initiation failed. Please try again.');
        }
    }

    /**
     * Handle SasaPay callback/webhook
     */
    public function handleCallback(Request $request)
    {
        try {
            Log::info('SasaPay Callback: received', $request->all());

            $callbackData = $request->all();
            $result = $this->sasaPayService->processCallback($callbackData);

            if ($result['success']) {
                $transactionReference = $result['transaction_reference'];
                $status = $result['status'];

                // Find the order by transaction reference
                $order = Order_master::where('transaction_no', $transactionReference)->first();

                if ($order) {
                    // Update order status based on payment status
                    if ($status === 'completed' || $status === 'success') {
                        $order->update([
                            'payment_status' => 'completed',
                            'order_status' => 'confirmed'
                        ]);

                        // Update payment transaction using transaction_code
                        PaymentTransaction::where('transaction_code', $transactionReference)
                            ->update(['status' => 'completed']);

                        Log::info('SasaPay Callback: payment completed', ['order_id' => $order->id, 'transaction_reference' => $transactionReference]);
                    } elseif ($status === 'failed' || $status === 'cancelled') {
                        $order->update([
                            'payment_status' => 'failed',
                            'order_status' => 'cancelled'
                        ]);

                        // Update payment transaction using transaction_code
                        PaymentTransaction::where('transaction_code', $transactionReference)
                            ->update(['status' => 'failed']);

                        Log::info('SasaPay Callback: payment failed', ['order_id' => $order->id, 'transaction_reference' => $transactionReference]);
                    }
                }
            }

            return response()->json(['status' => 'received']);

        } catch (\Exception $e) {
            Log::error('SasaPay Callback: error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
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

            Log::info('SasaPay Status: check start', ['transaction_reference' => $request->transaction_reference]);
            $status = $this->sasaPayService->checkPaymentStatus($request->transaction_reference);
            Log::info('SasaPay Status: check result', ['transaction_reference' => $request->transaction_reference, 'status' => $status]);

            return response()->json([
                'success' => true,
                'status' => $status
            ]);

        } catch (\Exception $e) {
            Log::error('SasaPay Status: error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
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
        $gtext = gtext();

        Log::info('SasaPay ShowForm: render', ['order_id' => $order->id]);
        return view('frontend.sasapay-payment', compact('order', 'gtext'));
    }

    /**
     * Handle successful payment
     */
    public function paymentSuccess(Request $request)
    {
        $orderId = $request->order_id;
        $order = Order_master::findOrFail($orderId);

        // Update order status to paid
        $order->update([
            'payment_status_id' => 1, // Assuming 1 is paid status
            'order_status_id' => 2    // Assuming 2 is confirmed status
        ]);

        // Clear the shopping cart
        session()->forget('shopping_cart');
        session()->forget('order_master_ids');

        // Send order notification email if enabled
        $gtext = gtext();
        if($gtext['ismail'] == 1){
            // Use the existing orderNotify method from CheckoutFrontController
            $checkoutController = new \App\Http\Controllers\Frontend\CheckoutFrontController();
            $checkoutController->orderNotify([$orderId]);
        }

        Log::info('SasaPay Success: payment completed and cart cleared', [
            'order_id' => $order->id,
            'user_id' => auth()->id()
        ]);
        
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

        Log::info('SasaPay Cancel: user landed', ['order_id' => $order->id]);
        return view('frontend.payment-cancel', compact('order'));
    }

    /**
     * Handle failed payment
     */
    public function paymentFailure(Request $request)
    {
        $orderId = $request->order_id;
        $order = Order_master::findOrFail($orderId);

        // Update order status to failed
        $order->update([
            'payment_status_id' => 3, // Assuming 3 is failed status
            'order_status_id' => 4    // Assuming 4 is failed status
        ]);

        Log::info('SasaPay Failure: user landed', ['order_id' => $order->id]);
        return view('frontend.payment-cancel', compact('order'));
    }
}
