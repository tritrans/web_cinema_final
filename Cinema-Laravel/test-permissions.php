<?php
echo "=== Test User Permissions ===\n";

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

$loginData = json_decode($loginResponse, true);

if (!$loginData['success']) {
    echo "❌ Login failed: " . $loginData['message'] . "\n";
    exit;
}

echo "✅ Login successful\n";
$token = $loginData['data']['access_token'];
echo "Token: " . substr($token, 0, 20) . "...\n\n";

// 2. Get user info
echo "2. Getting user info...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl . '/auth/me');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$meResponse = curl_exec($ch);
$meHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Me HTTP Code: $meHttpCode\n";
echo "Me Response: $meResponse\n\n";

$meData = json_decode($meResponse, true);

if ($meData['success']) {
    echo "User Info:\n";
    echo "- ID: " . $meData['data']['id'] . "\n";
    echo "- Name: " . $meData['data']['name'] . "\n";
    echo "- Email: " . $meData['data']['email'] . "\n";
    echo "- Role: " . $meData['data']['role'] . "\n";
    echo "- Roles: " . implode(', ', $meData['data']['roles'] ?? []) . "\n";
    echo "- Permissions: " . implode(', ', $meData['data']['permissions'] ?? []) . "\n\n";
}

// 3. Test assign role to see if it works
echo "3. Testing assign role...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl . '/users/7/assign-role');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['role' => 'user']));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$assignResponse = curl_exec($ch);
$assignHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Assign Role HTTP Code: $assignHttpCode\n";
echo "Assign Role Response: $assignResponse\n\n";

// 4. Test toggle status
echo "4. Testing toggle status...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl . '/users/7/toggle-status');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['activate' => true]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$toggleResponse = curl_exec($ch);
$toggleHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Toggle Status HTTP Code: $toggleHttpCode\n";
echo "Toggle Status Response: $toggleResponse\n\n";

echo "=== Test completed ===\n";
