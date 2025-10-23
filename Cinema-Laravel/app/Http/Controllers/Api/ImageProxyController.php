<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ImageProxyController extends Controller
{
    /**
     * Proxy images from Google Drive
     */
    public function proxy(Request $request)
    {
        $url = $request->get('url');
        
        if (!$url) {
            return response()->json(['error' => 'URL parameter is required'], 400);
        }

        // Validate URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json(['error' => 'Invalid URL'], 400);
        }

        // Only allow Google Drive domains
        $parsedUrl = parse_url($url);
        $host = $parsedUrl['host'] ?? '';

        if (!str_contains($host, 'google.com') && !str_contains($host, 'googleusercontent.com')) {
            return response()->json(['error' => 'Domain not allowed'], 403);
        }

        // Create cache key
        $cacheKey = 'image_proxy_' . md5($url);
        
        try {
            // Try to get from cache first
            $imageData = Cache::remember($cacheKey, 60 * 60 * 24, function () use ($url) {
                return $this->fetchImage($url);
            });

            if (!$imageData) {
                return response()->json(['error' => 'Failed to fetch image'], 404);
            }

            // Return the image
            return response($imageData['content'])
                ->header('Content-Type', $imageData['content_type'])
                ->header('Cache-Control', 'public, max-age=3600')
                ->header('Access-Control-Allow-Origin', '*');

        } catch (\Exception $e) {
            Log::error('Image proxy error: ' . $e->getMessage(), [
                'url' => $url,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Failed to proxy image'], 500);
        }
    }

    /**
     * Fetch image from URL
     */
    private function fetchImage($url)
    {
        // Convert Google Drive URL to direct download URL
        $directUrl = $this->convertToDirectUrl($url);

        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Accept' => 'image/webp,image/apng,image/*,*/*;q=0.8',
            'Accept-Language' => 'en-US,en;q=0.9',
            'Accept-Encoding' => 'gzip, deflate, br',
            'DNT' => '1',
            'Connection' => 'keep-alive',
            'Upgrade-Insecure-Requests' => '1',
            'Sec-Fetch-Dest' => 'image',
            'Sec-Fetch-Mode' => 'no-cors',
            'Sec-Fetch-Site' => 'cross-site',
        ])->timeout(15)->get($directUrl);

        if (!$response->successful()) {
            Log::error('Failed to fetch image', [
                'url' => $directUrl,
                'status' => $response->status(),
                'body' => substr($response->body(), 0, 200)
            ]);
            return null;
        }

        return [
            'content' => $response->body(),
            'content_type' => $response->header('Content-Type', 'image/jpeg')
        ];
    }

    /**
     * Convert Google Drive URL to direct download URL
     */
    private function convertToDirectUrl($url)
    {
        // Extract file ID from various Google Drive URL formats
        $patterns = [
            '/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/',
            '/drive\.google\.com\/open\?id=([a-zA-Z0-9_-]+)/',
            '/drive\.google\.com\/uc\?export=view&id=([a-zA-Z0-9_-]+)/',
            '/drive\.google\.com\/uc\?id=([a-zA-Z0-9_-]+)/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                $fileId = $matches[1];
                // Return direct download URL
                return "https://drive.google.com/uc?export=download&id={$fileId}";
            }
        }

        return $url;
    }
}
