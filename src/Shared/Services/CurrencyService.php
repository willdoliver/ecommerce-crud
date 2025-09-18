<?php

namespace App\Shared\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class CurrencyService
{
    private Client $client;
    private string $baseUrl;

    public function __construct()
    {
        $this->client = new Client(['timeout' => 5.0]);
        $this->baseUrl = $_ENV['EXCHANGE_RATE_API_URL'] ?? 'https://api.exchangerate.host';
    }

    public function getConvertedValues(float $amount, string $fromCurrency): array
    {
        if (strtoupper($fromCurrency) === 'BRL') {
            $usdValue = $this->convert($amount, 'BRL', 'USD');
            return ['value_brl' => (float)$amount, 'value_usd' => $usdValue];
        }

        if (strtoupper($fromCurrency) === 'USD') {
            $brlValue = $this->convert($amount, 'USD', 'BRL');
            return ['value_brl' => $brlValue, 'value_usd' => (float)$amount];
        }
        
        return ['value_brl' => null, 'value_usd' => null];
    }
    
    private function convert(float $amount, string $from, string $to): ?float
    {
        try {
            $response = $this->client->get("{$this->baseUrl}/latest?base={$from}&symbols={$to}");
            $data = json_decode((string)$response->getBody(), true);
            
            if (isset($data['rates'][$to])) {
                return round($amount * $data['rates'][$to], 2);
            }
        } catch (GuzzleException $e) {
            // Em um cenário real, logar o erro
        }
        return null;
    }
}