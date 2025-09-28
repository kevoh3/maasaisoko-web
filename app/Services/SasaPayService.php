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
        $gtext = gtext();
        $this->clientId = $gtext['sasapay_client_id'];
        $this->clientSecret = $gtext['sasapay_client_secret'];
        $this->merchantCode = $gtext['sasapay_merchant_code'];
        $this->callbackUrl = $gtext['sasapay_callback_url'];
        
        if($gtext['ismode_sasapay'] == 1){
            $this->baseURL = 'https://api.sasapay.co.ke'; // Sandbox URL
        } else {
            $this->baseURL = 'https://api.sasapay.co.ke'; // Production URL
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
     * Create a payment request
     */
    public function createPayment($paymentData)
    {
        try {
            $accessToken = $this->generateAccessToken();
            $client = new Client();
            
            $payload = [
                'merchantCode' => $this->merchantCode,
                'transactionReference' => $paymentData['transaction_reference'],
                'currencyCode' => $paymentData['currency_code'] ?? 'KES',
                'amount' => $paymentData['amount'],
                'senderAccountNumber' => $paymentData['sender_account_number'],
                'receiverMerchantCode' => $this->merchantCode,
                'accountReference' => $paymentData['account_reference'],
                'chargeAccount' => $paymentData['charge_account'] ?? 'SENDER',
                'transactionFee' => $paymentData['transaction_fee'] ?? 0,
                'billerType' => $paymentData['biller_type'] ?? 'PAYBILL',
                'networkCode' => $paymentData['network_code'] ?? 'MPESA',
                'CallBackUrl' => $this->callbackUrl,
                'Reason' => $paymentData['reason'] ?? 'Payment for order #' . $paymentData['transaction_reference']
            ];

            $response = $client->post($this->baseURL . '/api/v1/payments', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            $responseData = json_decode($response->getBody(), true);
            
            return $responseData;
            
        } catch (\Exception $e) {
            Log::error('SasaPay Payment Creation Error: ' . $e->getMessage());
            throw $e;
        }
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
        // Implement signature verification based on SasaPay documentation
        // This is a placeholder - you'll need to implement this based on their security requirements
        return true;
    }

    /**
     * Generate unique transaction reference
     */
    public function generateTransactionReference()
    {
        return 'SP_' . time() . '_' . uniqid();
    }
}
