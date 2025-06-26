<?php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class FLSSApiService
{
    private $baseUrl = 'https://api.pupt-flss.com/api/external/farms/v1';
    private $apiKey;

    public function __construct()
    {
        $this->apiKey = env('FLSS_API_KEY');
        if (!$this->apiKey) {
            Log::error('FLSS API key is not configured. Please check your .env file');
            throw new \RuntimeException('FLSS API key is not configured');
        }
    }

    private function generateSignature($method, $url, $body = '')
    {
        $timestamp = time();
        $nonce = Str::random(32); 
        
        $fullUrl = $url;
        if (!Str::startsWith($url, 'http')) {
            $fullUrl = $this->baseUrl . $url;
        }
        
        $message = strtoupper($method) . '|' . $fullUrl . '|' . $body . '|' . $timestamp . '|' . $nonce;
        
        $signature = hash_hmac('sha256', $message, $this->apiKey, false);
        
        return [
            'signature' => $signature,
            'timestamp' => $timestamp,
            'nonce' => $nonce
        ];
    }

    private function makeApiRequest($endpoint, $method = 'GET', $body = '')
    {
        try {
            $url = $this->baseUrl . $endpoint;
            $auth = $this->generateSignature($method, $url, $body);

            Log::debug('Making API request', [
                'url' => $url,
                'method' => $method,
                'timestamp' => $auth['timestamp'],
                'nonce' => $auth['nonce']
            ]);

            $response = Http::timeout(30)
                ->withHeaders([
                    'X-API-Key' => $this->apiKey,
                    'X-HMAC-Signature' => $auth['signature'],
                    'X-HMAC-Timestamp' => (string)$auth['timestamp'],
                    'X-HMAC-Nonce' => $auth['nonce'],
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->get($url);

            if ($response->successful()) {
                $data = $response->json();
                Log::debug('API request successful', [
                    'endpoint' => $endpoint,
                    'status' => $response->status()
                ]);
                return $data;
            }

            Log::error("FLSS API Error - {$endpoint}:", [
                'status' => $response->status(),
                'response' => $response->json(),
                'url' => $url,
                'headers' => [
                    'X-HMAC-Timestamp' => $auth['timestamp'],
                    'X-HMAC-Nonce' => $auth['nonce']
                ]
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error("FLSS API Exception - {$endpoint}:", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    public function getCourseSchedules()
    {
        $response = $this->makeApiRequest('/course-schedules');
        if (!$response) {
            Log::warning('No course schedules returned from API');
            return [];
        }
        return $response['course_schedules'] ?? [];
    }

    public function getCourseFiles()
    {
        $response = $this->makeApiRequest('/course-files');
        if (!$response) {
            Log::warning('No course files returned from API');
            return [];
        }
        return $response['courses_files'] ?? [];
    }
}