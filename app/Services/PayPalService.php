<?php

namespace App\Services;

use GuzzleHttp\Client;

class PayPalService
{
    protected $clientId;
    protected $appSecret;
    protected $baseURL;
	
    public function __construct()
    {
		
		$gtext = gtext();
		$this->clientId = $gtext['paypal_client_id'];
		$this->appSecret = $gtext['paypal_secret'];
		
		if($gtext['ismode_paypal'] == 1){
			$this->baseURL = 'https://api-m.sandbox.paypal.com';
		}else{
			$this->baseURL = 'https://api-m.paypal.com';
		}
    }

    //Function to get the access token for PayPal API authentication
    public function generateAccessToken()
    {
		$client = new Client();

		$url = $this->baseURL.'/v1/oauth2/token';

		$response = $client->post($url, [
            'auth' => [$this->clientId, $this->appSecret],
            'form_params' => [
                'grant_type' => 'client_credentials',
				'ignoreCache' => 'true',
				'return_authn_schemes' => 'true',
				'return_client_metadata' => 'true',
				'return_unconsented_scopes' => 'true',
            ],
        ]);

        $data = json_decode($response->getBody(), true);

		return $data['access_token'];
    }
	
    public function createOrder($accessToken, $orderData)
    {		
		$url = $this->baseURL.'/v2/checkout/orders';
		
		$client = new Client();
		
        $response = $client->post($url, [
            'headers' => [
                'Authorization' => "Bearer $accessToken",
                'Content-Type' => 'application/json',
				'PayPal-Request-Id' => uniqid('paypal_', true),
            ],
            'json' => $orderData,
        ]);

        $PayPalResponse = json_decode($response->getBody(), true);
		
		return $PayPalResponse;
    }
	
    public function capturePaymentOrder($accessToken, $OrderId)
    {		
		$url = $this->baseURL.'/v2/checkout/orders/'.$OrderId.'/capture';
		
		$client = new Client();
        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken,
				'PayPal-Request-Id' => uniqid('paypal_', true),
            ],
        ]);

		return $response;
    }
}


