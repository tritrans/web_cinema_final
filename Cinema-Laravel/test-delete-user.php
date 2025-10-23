<?php
require_once 'vendor/autoload.php';

use App\Services\ApiService;

echo "=== Test Delete User ===\n";

try {
    $apiService = new ApiService();
    
    // 1. Login as admin
    echo "1. Logging in as admin...\n";
    $loginResponse = $apiService->login('tritranminh484@gmail.com', '123456');
    
    if (!$loginResponse['success']) {
        echo "❌ Login failed: " . $loginResponse['message'] . "\n";
        exit;
    }
    
    echo "✅ Login successful\n";
    echo "Token: " . substr($loginResponse['data']['token'], 0, 20) . "...\n\n";
    
    // 2. Get users to find Test User 7
    echo "2. Getting users...\n";
    $usersResponse = $apiService->getUsers();
    
    if (!$usersResponse['success']) {
        echo "❌ Get users failed: " . $usersResponse['message'] . "\n";
        exit;
    }
    
    $users = $usersResponse['data']['data'] ?? $usersResponse['data'];
    echo "✅ Found " . count($users) . " users\n";
    
    // Find Test User 7
    $testUser = null;
    foreach ($users as $user) {
        if ($user['email'] === 'testuser7@example.com') {
            $testUser = $user;
            break;
        }
    }
    
    if (!$testUser) {
        echo "❌ Test User 7 not found\n";
        exit;
    }
    
    echo "Found Test User 7:\n";
    echo "- ID: " . $testUser['id'] . "\n";
    echo "- Name: " . $testUser['name'] . "\n";
    echo "- Email: " . $testUser['email'] . "\n";
    echo "- Role ID: " . ($testUser['role_id'] ?? 'null') . "\n";
    echo "- Is Active: " . ($testUser['is_active'] ? 'true' : 'false') . "\n\n";
    
    // 3. Test delete user
    echo "3. Testing delete user...\n";
    $deleteResponse = $apiService->deleteUser($testUser['id']);
    
    echo "Delete response:\n";
    echo json_encode($deleteResponse, JSON_PRETTY_PRINT) . "\n\n";
    
    if ($deleteResponse['success']) {
        echo "✅ User deleted successfully!\n";
    } else {
        echo "❌ Delete user failed: " . $deleteResponse['message'] . "\n";
    }
    
    // 4. Verify deletion by getting users again
    echo "\n4. Verifying deletion...\n";
    $usersResponse2 = $apiService->getUsers();
    
    if ($usersResponse2['success']) {
        $users2 = $usersResponse2['data']['data'] ?? $usersResponse2['data'];
        $found = false;
        foreach ($users2 as $user) {
            if ($user['email'] === 'testuser7@example.com') {
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            echo "✅ User successfully deleted from database\n";
        } else {
            echo "❌ User still exists in database\n";
        }
    }
    
    echo "\n=== Test completed ===\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
