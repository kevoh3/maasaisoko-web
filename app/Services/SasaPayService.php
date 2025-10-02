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
            // Per docs, sandbox host for API is sandbox.sasapay.app
            $this->baseURL = 'https://sandbox.sasapay.app';
        } else {
            $this->baseURL = 'https://api.sasapay.co.ke'; // Production URL
        }
    }

    /**
     * Get merchant code
     */
    public function getMerchantCode()
    {
        return $this->merchantCode;
    }

    /**
     * Generate OAuth2 access token for SasaPay API authentication
     */
    public function generateAccessToken()
    {
        try {
            Log::info('SasaPay: Attempting to generate access token', [
                'base_url' => $this->baseURL,
                'client_id' => substr($this->clientId, 0, 10) . '...'
            ]);
            
            $client = new Client([
                'timeout' => 30,
                'connect_timeout' => 10,
            ]);

            // Docs: GET /api/v1/auth/token/?grant_type=client_credentials with Basic Auth(client_id:client_secret)
            $authHeader = 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret);

            $response = $client->get($this->baseURL . '/api/v1/auth/token/', [
                'headers' => [
                    'Authorization' => $authHeader,
                    'Accept' => 'application/json',
                    'User-Agent' => 'MaasaiSoko/1.0',
                ],
                'query' => [
                    'grant_type' => 'client_credentials'
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            
            Log::info('SasaPay: Access token response', [
                'status_code' => $response->getStatusCode(),
                'has_access_token' => isset($data['access_token']),
                'response_keys' => array_keys($data)
            ]);
            
            if (isset($data['access_token'])) {
                Log::info('SasaPay: Access token generated successfully');
                return $data['access_token'];
            }
            
            throw new \Exception('Failed to get access token: ' . json_encode($data));
            
        } catch (\Exception $e) {
            Log::error('SasaPay Access Token Error', [
                'message' => $e->getMessage(),
                'base_url' => $this->baseURL,
                'client_id' => substr($this->clientId, 0, 10) . '...',
                'error_type' => get_class($e)
            ]);
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
            Log::info('SasaPay: Creating checkout payment', [
                'amount' => $paymentData['amount'],
                'reference' => $paymentData['transaction_reference'],
                'currency' => $paymentData['currency_code'] ?? 'KES'
            ]);
            
            $accessToken = $this->generateAccessToken();
            $client = new Client([
                'timeout' => 30,
                'connect_timeout' => 10,
            ]);
            
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

            Log::info('SasaPay: Sending checkout request', [
                'endpoint' => $this->baseURL . '/api/v1/payments/card-payments/',
                'merchant_code' => $this->merchantCode,
                'amount' => $payload['Amount'],
                'reference' => $payload['Reference'],
                'callback_url' => $this->callbackUrl,
                'success_url' => $payload['SuccessUrl'],
                'failure_url' => $payload['FailureUrl']
            ]);

            // Docs: POST /api/v1/payments/card-payments/
            $response = $client->post($this->baseURL . '/api/v1/payments/card-payments/', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'MaasaiSoko/1.0',
                ],
                'json' => $payload,
            ]);

            $responseData = json_decode($response->getBody(), true);
            
            Log::info('SasaPay: Checkout response received', [
                'status_code' => $response->getStatusCode(),
                // Docs show response key is "CheckoutUrl" (PascalCase)
                'has_checkout_url' => isset($responseData['CheckoutUrl']) || isset($responseData['checkout_url']),
                'response_keys' => array_keys($responseData)
            ]);
            
            return $responseData;
            
        } catch (\Exception $e) {
            Log::error('SasaPay Checkout Payment Creation Error', [
                'message' => $e->getMessage(),
                'amount' => $paymentData['amount'] ?? 'unknown',
                'reference' => $paymentData['transaction_reference'] ?? 'unknown',
                'error_type' => get_class($e)
            ]);
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

    /**
     * Get base URL for debugging
     */
    public function getBaseUrl()
    {
        return $this->baseURL;
    }


    /**
     * Get callback URL for debugging
     */
    public function getCallbackUrl()
    {
        return $this->callbackUrl;
    }
}
