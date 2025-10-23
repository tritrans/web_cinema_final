<?php
/**
 * Debug session data to check avatar URL
 */

echo "=== Session Debug ===\n\n";

// Start session to access session data
session_start();

echo "Session data:\n";
if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    echo "User ID: " . ($user['id'] ?? 'N/A') . "\n";
    echo "User Name: " . ($user['name'] ?? 'N/A') . "\n";
    echo "User Email: " . ($user['email'] ?? 'N/A') . "\n";
    echo "User Avatar: " . ($user['avatar'] ?? 'N/A') . "\n";
    echo "User Role: " . ($user['role'] ?? 'N/A') . "\n";
    
    if (isset($user['avatar'])) {
        $avatar = $user['avatar'];
        echo "\nAvatar URL Analysis:\n";
        echo "Raw avatar value: '$avatar'\n";
        
        // Test different URL constructions
        echo "\nURL Construction Tests:\n";
        
        // Method 1: Direct (what's causing 404)
        $url1 = 'http://127.0.0.1:8001/' . $avatar;
        echo "1. Direct: $url1\n";
        
        // Method 2: With storage prefix
        $url2 = 'http://127.0.0.1:8001/storage/' . $avatar;
        echo "2. With storage: $url2\n";
        
        // Method 3: Check if it's already a full URL
        if (strpos($avatar, 'http') === 0) {
            echo "3. Already full URL: $avatar\n";
        } else {
            echo "3. Not a full URL, needs prefix\n";
        }
        
        // Test file existence
        echo "\nFile Existence Tests:\n";
        $storageFile = __DIR__ . '/storage/app/public/' . $avatar;
        $publicFile = __DIR__ . '/public/storage/' . $avatar;
        
        echo "Storage file: $storageFile\n";
        echo "Exists: " . (file_exists($storageFile) ? 'YES' : 'NO') . "\n";
        
        echo "Public file: $publicFile\n";
        echo "Exists: " . (file_exists($publicFile) ? 'YES' : 'NO') . "\n";
        
        // Test HTTP access
        echo "\nHTTP Access Tests:\n";
        $context = stream_context_create([
            'http' => [
                'method' => 'HEAD',
                'timeout' => 5
            ]
        ]);
        
        // Test both URLs
        $headers1 = @get_headers($url1, 1, $context);
        $headers2 = @get_headers($url2, 1, $context);
        
        echo "URL 1 ($url1): " . ($headers1 ? $headers1[0] : 'FAILED') . "\n";
        echo "URL 2 ($url2): " . ($headers2 ? $headers2[0] : 'FAILED') . "\n";
    }
} else {
    echo "No user session found\n";
}

echo "\n=== Debug Complete ===\n";

