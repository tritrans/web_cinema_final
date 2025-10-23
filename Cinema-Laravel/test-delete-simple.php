<?php
echo "=== Test Delete User Simple ===\n";

// API base URL
$apiUrl = 'http://127.0.0.1:8000/api';

// 1. Login as admin
echo "1. Logging in as admin...\n";
$loginData = [
    'email' => 'tritranminh484@gmail.com',
    'password' => '123456'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl . '/auth/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$loginResponse = curl_exec($ch);
$loginHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Login HTTP Code: $loginHttpCode\n";
echo "Login Response: $loginResponse\n\n";

$loginData = json_decode($loginResponse, true);

if (!$loginData['success']) {
    echo "❌ Login failed: " . $loginData['message'] . "\n";
    exit;
}

echo "✅ Login successful\n";
$token = $loginData['data']['access_token'];
echo "Token: " . substr($token, 0, 20) . "...\n\n";

// 2. Get users to find Test User 7
echo "2. Getting users...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl . '/users');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$usersResponse = curl_exec($ch);
$usersHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Users HTTP Code: $usersHttpCode\n";
echo "Users Response: " . substr($usersResponse, 0, 200) . "...\n\n";

$usersData = json_decode($usersResponse, true);

if (!$usersData['success']) {
    echo "❌ Get users failed: " . $usersData['message'] . "\n";
    exit;
}

$users = $usersData['data']['data'] ?? $usersData['data'];
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
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl . '/users/' . $testUser['id']);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$deleteResponse = curl_exec($ch);
$deleteHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Delete HTTP Code: $deleteHttpCode\n";
echo "Delete Response: $deleteResponse\n\n";

$deleteData = json_decode($deleteResponse, true);

if ($deleteData && $deleteData['success']) {
    echo "✅ User deleted successfully!\n";
} else {
    echo "❌ Delete user failed\n";
    if ($deleteData) {
        echo "Message: " . ($deleteData['message'] ?? 'Unknown error') . "\n";
    }
}

echo "\n=== Test completed ===\n";
