<?php
require_once 'vendor/autoload.php';

use App\Services\ApiService;

echo "=== Test User Management Final ===\n";

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
    
    // 2. Get users
    echo "2. Getting users...\n";
    $usersResponse = $apiService->getUsers();
    
    if (!$usersResponse['success']) {
        echo "❌ Get users failed: " . $usersResponse['message'] . "\n";
        exit;
    }
    
    $users = $usersResponse['data']['data'] ?? $usersResponse['data'];
    echo "✅ Found " . count($users) . " users\n";
    
    // Find a test user (not admin)
    $testUser = null;
    foreach ($users as $user) {
        if ($user['email'] !== 'tritranminh484@gmail.com' && ($user['role_id'] ?? 1) == 1) {
            $testUser = $user;
            break;
        }
    }
    
    if (!$testUser) {
        echo "❌ No test user found\n";
        exit;
    }
    
    echo "Test user: {$testUser['name']} ({$testUser['email']})\n";
    echo "Current role_id: " . ($testUser['role_id'] ?? 'null') . "\n";
    echo "Current is_active: " . ($testUser['is_active'] ? 'true' : 'false') . "\n\n";
    
    // 3. Test assign role
    echo "3. Testing assign role (movie_manager)...\n";
    $assignResponse = $apiService->assignUserRole($testUser['id'], 'movie_manager');
    
    if ($assignResponse['success']) {
        echo "✅ Role assigned successfully\n";
        echo "Response: " . json_encode($assignResponse, JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "❌ Assign role failed: " . $assignResponse['message'] . "\n";
        echo "Response: " . json_encode($assignResponse, JSON_PRETTY_PRINT) . "\n";
    }
    
    // 4. Test toggle status
    echo "\n4. Testing toggle status (deactivate)...\n";
    $toggleResponse = $apiService->toggleUserStatus($testUser['id'], false);
    
    if ($toggleResponse['success']) {
        echo "✅ Status toggled successfully\n";
        echo "Response: " . json_encode($toggleResponse, JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "❌ Toggle status failed: " . $toggleResponse['message'] . "\n";
        echo "Response: " . json_encode($toggleResponse, JSON_PRETTY_PRINT) . "\n";
    }
    
    // 5. Test revoke admin role (if user is admin)
    if (($testUser['role_id'] ?? 1) == 2) {
        echo "\n5. Testing revoke admin role...\n";
        $revokeResponse = $apiService->revokeUserAdminRole($testUser['id']);
        
        if ($revokeResponse['success']) {
            echo "✅ Admin role revoked successfully\n";
            echo "Response: " . json_encode($revokeResponse, JSON_PRETTY_PRINT) . "\n";
        } else {
            echo "❌ Revoke admin failed: " . $revokeResponse['message'] . "\n";
            echo "Response: " . json_encode($revokeResponse, JSON_PRETTY_PRINT) . "\n";
        }
    }
    
    // 6. Test delete user (only if not admin)
    if (($testUser['role_id'] ?? 1) != 2) {
        echo "\n6. Testing delete user...\n";
        $deleteResponse = $apiService->deleteUser($testUser['id']);
        
        if ($deleteResponse['success']) {
            echo "✅ User deleted successfully\n";
            echo "Response: " . json_encode($deleteResponse, JSON_PRETTY_PRINT) . "\n";
        } else {
            echo "❌ Delete user failed: " . $deleteResponse['message'] . "\n";
            echo "Response: " . json_encode($deleteResponse, JSON_PRETTY_PRINT) . "\n";
        }
    }
    
    echo "\n=== Test completed ===\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
