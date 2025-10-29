<?php

namespace App\Helpers;

class ImageHelper
{
    /**
     * Get safe image URL with fallback
     */
    public static function getSafeImageUrl($url, $fallback = null)
    {
        if (empty($url)) {
            return $fallback ?: '/images/placeholder-movie.svg';
        }

        // If it's already a full URL, return as is
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            // If it's a Google Drive URL, convert to direct image URL
            if (str_contains($url, 'drive.google.com')) {
                return self::convertGoogleDriveUrl($url);
            }
            return $url;
        }

        // If it's a relative path, make it absolute
        if (str_starts_with($url, '/')) {
            return config('app.url') . $url;
        }

        // If it's a storage path, make it absolute
        if (str_starts_with($url, 'storage/')) {
            return config('app.url') . '/' . $url;
        }

        // Default fallback
        return $fallback ?: '/images/placeholder-movie.svg';
    }

    /**
     * Convert Google Drive sharing URL to direct image URL
     */
    private static function convertGoogleDriveUrl($url)
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
                // Try different CDN formats - prioritize thumbnail API
                $cdnUrls = [
                    "https://drive.google.com/thumbnail?id={$fileId}&sz=w1000-h1500",
                    "https://drive.google.com/thumbnail?id={$fileId}&sz=w800-h1200",
                    "https://lh3.googleusercontent.com/d/{$fileId}",
                ];
                
                // Return the first CDN URL (thumbnail API is more reliable)
                return $cdnUrls[0];
            }
        }

        return $url;
    }

    /**
     * Get movie poster URL
     */
    public static function getMoviePoster($movie, $size = 'medium')
    {
        $posterUrl = $movie['poster'] ?? $movie['poster_url'] ?? null;
        
        if (empty($posterUrl)) {
            return '/images/placeholder-movie.svg';
        }

        return self::getSafeImageUrl($posterUrl);
    }

    /**
     * Get movie backdrop URL
     */
    public static function getMovieBackdrop($movie, $size = 'large')
    {
        $backdropUrl = $movie['backdrop'] ?? $movie['backdrop_url'] ?? null;
        
        if (empty($backdropUrl)) {
            return self::getMoviePoster($movie, $size);
        }

        return self::getSafeImageUrl($backdropUrl);
    }

    /**
     * Get user avatar URL
     */
    public static function getUserAvatar($user, $size = 'medium')
    {
        $avatarUrl = $user['avatar'] ?? $user['avatar_url'] ?? null;
        
        if (empty($avatarUrl)) {
            return '/images/default-avatar.svg';
        }

        return self::getSafeImageUrl($avatarUrl, '/images/default-avatar.svg');
    }

    /**
     * Get snack image URL
     */
    public static function getSnackImage($snack, $size = 'medium')
    {
        $imageUrl = $snack['image'] ?? $snack['image_url'] ?? null;
        
        if (empty($imageUrl)) {
            return '/images/placeholder-snack.svg';
        }

        // If it's a storage path (like Snack/Combo.jpg), convert to API URL
        if (!str_starts_with($imageUrl, 'http') && !str_starts_with($imageUrl, '/')) {
            $apiUrl = config('app.api_url', 'http://127.0.0.1:8000/api');
            // Remove /api from the end to get base URL
            $baseUrl = rtrim($apiUrl, '/api');
            return $baseUrl . '/storage/' . $imageUrl;
        }

        return self::getSafeImageUrl($imageUrl, '/images/placeholder-snack.svg');
    }

    /**
     * Get theater image URL
     */
    public static function getTheaterImage($theater, $size = 'medium')
    {
        $imageUrl = $theater['image'] ?? $theater['image_url'] ?? null;
        
        if (empty($imageUrl)) {
            return '/images/placeholder-theater.svg';
        }

        return self::getSafeImageUrl($imageUrl, '/images/placeholder-theater.svg');
    }

    /**
     * Generate responsive image srcset
     */
    public static function getResponsiveImageSrcset($url, $sizes = ['small' => 300, 'medium' => 600, 'large' => 1200])
    {
        $baseUrl = self::getSafeImageUrl($url);
        $srcset = [];

        foreach ($sizes as $size => $width) {
            $srcset[] = "{$baseUrl} {$width}w";
        }

        return implode(', ', $srcset);
    }

    /**
     * Check if image URL is valid
     */
    public static function isValidImageUrl($url)
    {
        if (empty($url)) {
            return false;
        }

        // Check if it's a valid URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        // Check if it's an image URL
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        $pathInfo = pathinfo(parse_url($url, PHP_URL_PATH));
        $extension = strtolower($pathInfo['extension'] ?? '');

        return in_array($extension, $imageExtensions) || str_contains($url, 'drive.google.com');
    }

    /**
     * Get image dimensions from URL (if possible)
     */
    public static function getImageDimensions($url)
    {
        try {
            $headers = get_headers($url, 1);
            if (isset($headers['Content-Type']) && str_contains($headers['Content-Type'], 'image/')) {
                // For Google Drive images, we can't easily get dimensions
                if (str_contains($url, 'drive.google.com')) {
                    return ['width' => 600, 'height' => 900]; // Default movie poster ratio
                }
            }
        } catch (\Exception $e) {
            // Ignore errors
        }

        return ['width' => 600, 'height' => 900]; // Default fallback
    }

    /**
     * Generate lazy loading placeholder
     */
    public static function getLazyPlaceholder($width = 300, $height = 450)
    {
        return "data:image/svg+xml;base64," . base64_encode(
            "<svg width='{$width}' height='{$height}' xmlns='http://www.w3.org/2000/svg'><rect width='100%' height='100%' fill='%23f3f4f6'/><text x='50%' y='50%' text-anchor='middle' dy='.3em' fill='%236b7280' font-family='Arial, sans-serif' font-size='14'>Loading...</text></svg>"
        );
    }
}