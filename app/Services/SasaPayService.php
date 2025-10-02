<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SasaPayService
{
    protected $clientId;
    protected $clientSecret;
    protected $merchantCode;
    protected $baseURL;
    protected $callbackUrl;
    
    public function __construct()
    {
        // Try to get credentials from gtext() first, fallback to config
        try {
            $gtext = gtext();
            $this->clientId = $gtext['sasapay_client_id'] ?? config('sasapay.sandbox.client_id');
            $this->clientSecret = $gtext['sasapay_client_secret'] ?? config('sasapay.sandbox.client_secret');
            $this->merchantCode = $gtext['sasapay_merchant_code'] ?? config('sasapay.sandbox.merchant_code');
            $this->callbackUrl = $gtext['sasapay_callback_url'] ?? config('sasapay.sandbox.callback_url');
            
            $isSandbox = $gtext['ismode_sasapay'] ?? 1; // Default to sandbox
        } catch (\Exception $e) {
            // Fallback to config values if gtext() fails
            $this->clientId = config('sasapay.sandbox.client_id');
            $this->clientSecret = config('sasapay.sandbox.client_secret');
            $this->merchantCode = config('sasapay.sandbox.merchant_code');
            $this->callbackUrl = config('sasapay.sandbox.callback_url');
            $isSandbox = 1; // Default to sandbox
        }
        
        if($isSandbox == 1){
            $this->baseURL = 'https://sandbox.sasapay.app'; // Sandbox URL
        } else {
            $this->baseURL = 'https://api.sasapay.app'; // Production URL
        }
    }

    /**
     * Generate OAuth2 access token for SasaPay API authentication
     */
    public function generateAccessToken()
    {
        try {
            $client = new Client();
            
            $response = $client->post($this->baseURL . '/oauth/token', [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            
            if (isset($data['access_token'])) {
                return $data['access_token'];
            }
            
            throw new \Exception('Failed to get access token: ' . json_encode($data));
            
        } catch (\Exception $e) {
            Log::error('SasaPay Access Token Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a checkout payment request using SasaPay Checkout API
     * This API provides a checkout page with multiple payment options:
     * SasaPay, M-Pesa, Airtel Money, T-KASH, and Card payments
     */
    public function createCheckoutPayment($paymentData)
    {
        try {
            $accessToken = $this->generateAccessToken();
            $client = new Client();
            
            $payload = [
                'MerchantCode' => $this->merchantCode,
                'Amount' => number_format($paymentData['amount'], 2, '.', ''),
                'Reference' => $paymentData['transaction_reference'],
                'Description' => $paymentData['description'] ?? 'Payment for goods',
                'Currency' => $paymentData['currency_code'] ?? 'KES',
                'PayerEmail' => $paymentData['payer_email'] ?? 'customer@example.com',
                'CallbackUrl' => $this->callbackUrl,
                'SuccessUrl' => $paymentData['success_url'] ?? route('frontend.sasapay.success'),
                'FailureUrl' => $paymentData['failure_url'] ?? route('frontend.sasapay.failure'),
                'SasaPayWalletEnabled' => $paymentData['sasapay_wallet_enabled'] ?? true,
                'MpesaEnabled' => $paymentData['mpesa_enabled'] ?? true,
                'CardEnabled' => $paymentData['card_enabled'] ?? true,
                'AirtelEnabled' => $paymentData['airtel_enabled'] ?? true,
                'TkashEnabled' => $paymentData['tkash_enabled'] ?? true,
            ];

            $response = $client->post($this->baseURL . '/api/v1/payments/checkout/', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            $responseData = json_decode($response->getBody(), true);
            
            return $responseData;
            
        } catch (\Exception $e) {
            Log::error('SasaPay Checkout Payment Creation Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a payment request (legacy method for backward compatibility)
     */
    public function createPayment($paymentData)
    {
        // Redirect to new checkout method
        return $this->createCheckoutPayment($paymentData);
    }

    /**
     * Check payment status
     */
    public function checkPaymentStatus($transactionReference)
    {
        try {
            $accessToken = $this->generateAccessToken();
            $client = new Client();
            
            $response = $client->get($this->baseURL . '/api/v1/payments/' . $transactionReference, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
            ]);

            $responseData = json_decode($response->getBody(), true);
            
            return $responseData;
            
        } catch (\Exception $e) {
            Log::error('SasaPay Status Check Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Process payment callback
     */
    public function processCallback($callbackData)
    {
        try {
            // Verify the callback signature if provided
            if (isset($callbackData['signature'])) {
                if (!$this->verifyCallbackSignature($callbackData)) {
                    throw new \Exception('Invalid callback signature');
                }
            }

            return [
                'success' => true,
                'transaction_reference' => $callbackData['transactionReference'] ?? null,
                'status' => $callbackData['status'] ?? null,
                'amount' => $callbackData['amount'] ?? null,
                'message' => $callbackData['message'] ?? null,
            ];
            
        } catch (\Exception $e) {
            Log::error('SasaPay Callback Processing Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify callback signature (implement based on SasaPay documentation)
     */
    private function verifyCallbackSignature($callbackData)
    {
        // Basic HMAC verification: if signature present, compute HMAC-SHA256
        // over canonical JSON of payload fields (excluding 'signature'),
        // using environment-configured client_secret for current mode.
        if (!isset($callbackData['signature'])) {
            return true; // no signature provided; treat as ok in sandbox
        }

        try {
            $providedSignature = (string) $callbackData['signature'];

            // Remove signature field before computing digest
            $dataWithoutSignature = $callbackData;
            unset($dataWithoutSignature['signature']);

            // Canonicalize payload: sort keys recursively and encode
            $canonical = $this->jsonEncodeCanonical($dataWithoutSignature);

            // Choose secret; prefer gtext secret when available
            try {
                $gtext = gtext();
                $secret = $gtext['sasapay_client_secret'] ?? null;
            } catch (\Throwable $e) {
                $secret = null;
            }
            if (!$secret) {
                $mode = config('sasapay.mode', 'sandbox');
                $secret = $mode === 'live'
                    ? (string) config('sasapay.live.client_secret')
                    : (string) config('sasapay.sandbox.client_secret');
            }

            if ($secret === '') {
                // If no secret configured, do not block callbacks in sandbox
                return config('sasapay.mode', 'sandbox') !== 'live';
            }

            $computed = hash_hmac('sha256', $canonical, $secret);

            // Support hex and base64 comparisons
            if (hash_equals($computed, $providedSignature)) {
                return true;
            }
            $computedB64 = base64_encode(hex2bin($computed));
            return hash_equals($computedB64, $providedSignature);

        } catch (\Throwable $e) {
            Log::warning('SasaPay signature verification error: '.$e->getMessage());
            // Fail-open in sandbox
            return config('sasapay.mode', 'sandbox') !== 'live';
        }
    }

    /**
     * Encode array as canonical JSON: recursively sort keys to ensure stable HMAC.
     */
    private function jsonEncodeCanonical(array $data): string
    {
        $normalize = function ($value) use (&$normalize) {
            if (is_array($value)) {
                // Distinguish assoc vs list
                if (array_values($value) === $value) {
                    // list: normalize each element
                    return array_map($normalize, $value);
                }
                // assoc: sort keys
                ksort($value);
                $normalized = [];
                foreach ($value as $k => $v) {
                    $normalized[$k] = $normalize($v);
                }
                return $normalized;
            }
            return $value;
        };

        $normalized = $normalize($data);
        return json_encode($normalized, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Generate unique transaction reference
     */
    public function generateTransactionReference()
    {
        return 'SP_' . time() . '_' . uniqid();
    }
}
