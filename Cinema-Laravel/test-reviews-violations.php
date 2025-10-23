<?php
echo "=== Test Reviews and Violations ===\n";

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

// 2. Get reviews
echo "2. Getting reviews...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl . '/admin/reviews');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$reviewsResponse = curl_exec($ch);
$reviewsHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Reviews HTTP Code: $reviewsHttpCode\n";
echo "Reviews Response: " . substr($reviewsResponse, 0, 200) . "...\n\n";

$reviewsData = json_decode($reviewsResponse, true);

if ($reviewsData['success']) {
    $reviews = $reviewsData['data'];
    echo "✅ Found " . count($reviews) . " reviews\n";
    
    if (count($reviews) > 0) {
        $firstReview = $reviews[0];
        echo "First review:\n";
        echo "- ID: " . $firstReview['id'] . "\n";
        echo "- User ID: " . $firstReview['user_id'] . "\n";
        echo "- User Email: " . $firstReview['user_email'] . "\n";
        echo "- User Avatar: " . ($firstReview['user_avatar_url'] ?? 'null') . "\n";
        echo "- Rating: " . $firstReview['rating'] . "\n";
        echo "- Comment: " . substr($firstReview['comment'] ?? '', 0, 50) . "...\n\n";
        
        // 3. Test report violation
        echo "3. Testing report violation...\n";
        $violationData = [
            'reportable_id' => $firstReview['id'],
            'reportable_type' => 'App\\Models\\Review',
            'violation_type' => 'inappropriate_content',
            'description' => 'Test violation report from PHP script'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl . '/violations');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($violationData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $violationResponse = curl_exec($ch);
        $violationHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo "Report Violation HTTP Code: $violationHttpCode\n";
        echo "Report Violation Response: $violationResponse\n\n";
        
        $violationData = json_decode($violationResponse, true);
        
        if ($violationData && $violationData['success']) {
            echo "✅ Violation reported successfully!\n";
            echo "Violation ID: " . $violationData['data']['id'] . "\n\n";
            
            // 4. Get violations
            echo "4. Getting violations...\n";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl . '/violations');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'Accept: application/json'
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $violationsResponse = curl_exec($ch);
            $violationsHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            echo "Violations HTTP Code: $violationsHttpCode\n";
            echo "Violations Response: " . substr($violationsResponse, 0, 200) . "...\n\n";
            
            $violationsData = json_decode($violationsResponse, true);
            
            if ($violationsData && $violationsData['success']) {
                $violations = $violationsData['data'];
                echo "✅ Found " . count($violations) . " violations\n";
                
                if (count($violations) > 0) {
                    $firstViolation = $violations[0];
                    echo "First violation:\n";
                    echo "- ID: " . $firstViolation['id'] . "\n";
                    echo "- Reporter: " . ($firstViolation['reporter']['name'] ?? 'N/A') . "\n";
                    echo "- Type: " . $firstViolation['violation_type'] . "\n";
                    echo "- Status: " . $firstViolation['status'] . "\n";
                    echo "- Description: " . substr($firstViolation['description'] ?? '', 0, 50) . "...\n\n";
                }
            } else {
                echo "❌ Get violations failed: " . ($violationsData['message'] ?? 'Unknown error') . "\n";
            }
        } else {
            echo "❌ Report violation failed: " . ($violationData['message'] ?? 'Unknown error') . "\n";
        }
    }
} else {
    echo "❌ Get reviews failed: " . ($reviewsData['message'] ?? 'Unknown error') . "\n";
}

echo "\n=== Test completed ===\n";
