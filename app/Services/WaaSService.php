<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Http;

class WaaSService
{
    protected $apiKey;
    protected $endpoint;
    public function __construct()
    {
        $this->apiKey = env('PROPEL_API_KEY'); // Use uppercase convention
        $this->endpoint = env('PROPEL_API_BASE_URL');
    }
    public function addBeneficiary(array $data,$user_id)
    {
        $response = Http::withHeaders([
            'X-API-KEY' => $this->apiKey,
            'Accept' => 'application/json',
        ])->post($this->endpoint, $data);
        $result = $response->json();

        if (isset($result['data'])) {
            $walletData = $result['data'];
            // Save the wallet
            Wallet::create([
                'user_id' => $user_id,
                'wallet_name' => $walletData['wallet_name'] ?? null,
                'account_number' => $walletData['account_number'] ?? null,
                'notification_number' => $walletData['notification_number'] ?? null,
                'wallet_type' => $walletData['wallet_type'] ?? 'WAAS',
                'client_id' => $walletData['client_id'] ?? null,
                'client_secret' => $walletData['client_secret'] ?? null,
                'request_id' => $walletData['request_id'] ?? null,
            ]);
        }

        return $result;
    }
}
