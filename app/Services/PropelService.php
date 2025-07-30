<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PropelService
{
    protected $clientId;
    protected $clientSecret;
    protected $baseUrl;

    public function __construct()
    {
        $this->clientId = config('services.propel.MASTER_WALLET_CLIENT_ID');
        $this->clientSecret = config('services.propel.MASTER_WALLET_CLIENT_SECRET');
        $this->baseUrl = 'https://api.propel.co.ke';
    }

    /**
     * Get OAuth Access Token
     */
//    public function getToken()
//    {
//        $url = $this->baseUrl . '/api/v1/auth/token/?grant_type=client_credentials';
//        $basicAuth = base64_encode("{$this->clientId}:{$this->clientSecret}");
//
//        $response = Http::withHeaders([
//            'Authorization' => 'Basic ' . $basicAuth,
//        ])->get($url);
//
//        if ($response->successful()) {
//            return $response->json()['access_token'];
//        }
//
//        Log::error('Propel Token Error', ['response' => $response->body()]);
//        throw new \Exception('Unable to retrieve Propel access token.');
//    }
    public function getToken()
    {
        // Try cache first
        if (Cache::has('propel_access_token')) {
            return Cache::get('propel_access_token');
        }

        $url = $this->baseUrl . '/api/v1/auth/token/?grant_type=client_credentials';
        $basicAuth = base64_encode("{$this->clientId}:{$this->clientSecret}");

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $basicAuth,
        ])->get($url);

        if ($response->successful()) {
            $data = $response->json();

            // Cache token for its duration (minus a buffer)
            $expiresIn = $data['expires_in'] ?? 3600; // in seconds
            $token = $data['access_token'];

            // Cache with buffer (e.g. 10 seconds before expiry)
            Cache::put('propel_access_token', $token, now()->addSeconds($expiresIn - 10));

            return $token;
        }

        Log::error('Propel Token Error', ['response' => $response->body()]);
        throw new \Exception('Unable to retrieve Propel access token.');
    }

    /**
     * Initiate a Collection (STK Push)
     */
    public function initiateCollection(array $data)
    {
        $url = 'https://api.propel.co.ke/api/prod/v1/collection/initiate';
        $token = $this->getToken();

        $response = Http::withToken($token)->asForm()->post($url, [
            'MerchantCode' => $data['MerchantCode'],
            'Amount' => $data['Amount'],
            'Currency' => $data['Currency'],
            'PhoneNumber' => $data['PhoneNumber'],
            'Channel' => $data['Channel'],
            'CallBackUrl' => $data['CallBackUrl'],
            'Reason' => $data['Reason'],
            'InitiatorTransactionReference' => $data['InitiatorTransactionReference'],
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Propel Collection Error', ['response' => $response->body()]);
        throw new \Exception('Collection request failed.');
    }

    /**
     * Initiate a Payout (placeholder)
     */
    public function initiatePayout(array $data)
    {
        // Replace with actual endpoint and parameters
        return ['message' => 'Payout integration not yet implemented'];
    }
}
