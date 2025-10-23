<?php
/**
 * Script test cho Web AdminController endpoints
 * Chạy: php test_admin_web.php
 */

require_once __DIR__ . '/vendor/autoload.php';

class AdminWebTester
{
    private $baseUrl;
    private $sessionCookie;

    public function __construct()
    {
        $this->baseUrl = 'http://localhost:8000'; // Thay đổi URL theo môi trường
        echo "🌐 Admin Web Tester Started\n";
        echo "Base URL: {$this->baseUrl}\n\n";
    }

    public function runAllTests()
    {
        echo "=== ADMIN WEB TESTING ===\n\n";
        
        $this->setupSession();
        $this->testTheaterWebEndpoints();
        $this->testScheduleWebEndpoints();
        
        echo "\n=== TESTING COMPLETED ===\n";
    }

    private function setupSession()
    {
        echo "🔐 Setting up admin session...\n";
        
        // Login as admin
        $loginData = [
            'email' => 'admin@test.com',
            'password' => 'password'
        ];
        
        $response = $this->makeRequest('POST', '/login', $loginData);
        
        if ($response && isset($response['success']) && $response['success']) {
            echo "✅ Admin login successful\n";
        } else {
            echo "❌ Admin login failed\n";
        }
        
        echo "\n";
    }

    private function testTheaterWebEndpoints()
    {
        echo "🏢 Testing Theater Web Endpoints...\n";
        
        // Test 1: View theaters list
        $this->testViewTheatersList();
        
        // Test 2: View theater details
        $this->testViewTheaterDetails();
        
        // Test 3: View edit theater form
        $this->testViewEditTheaterForm();
        
        // Test 4: Update theater
        $this->testUpdateTheater();
        
        // Test 5: Delete theater
        $this->testDeleteTheater();
        
        echo "\n";
    }

    private function testViewTheatersList()
    {
        echo "  📋 Testing view theaters list... ";
        
        $response = $this->makeRequest('GET', '/admin/theaters');
        
        if ($response && strpos($response, 'Quản lý rạp chiếu') !== false) {
            echo "✅ PASS\n";
        } else {
            echo "❌ FAIL - Theater list page not loaded properly\n";
        }
    }

    private function testViewTheaterDetails()
    {
        echo "  👁️  Testing view theater details... ";
        
        $response = $this->makeRequest('GET', '/admin/theaters/1');
        
        if ($response && strpos($response, 'Chi tiết rạp chiếu') !== false) {
            echo "✅ PASS\n";
        } else {
            echo "❌ FAIL - Theater details page not loaded properly\n";
        }
    }

    private function testViewEditTheaterForm()
    {
        echo "  ✏️  Testing view edit theater form... ";
        
        $response = $this->makeRequest('GET', '/admin/theaters/1/edit');
        
        if ($response && strpos($response, 'Chỉnh sửa rạp chiếu') !== false) {
            echo "✅ PASS\n";
        } else {
            echo "❌ FAIL - Edit theater form not loaded properly\n";
        }
    }

    private function testUpdateTheater()
    {
        echo "  💾 Testing update theater... ";
        
        $updateData = [
            'name' => 'Updated Theater Name',
            'address' => '456 Updated Street',
            'phone' => '0987654321',
            'email' => 'updated@theater.com',
            'description' => 'Updated description',
            'is_active' => '1',
            '_token' => $this->getCsrfToken()
        ];
        
        $response = $this->makeRequest('PUT', '/admin/theaters/1', $updateData);
        
        if ($response && (strpos($response, 'success') !== false || strpos($response, 'cập nhật') !== false)) {
            echo "✅ PASS\n";
        } else {
            echo "❌ FAIL - Theater update failed\n";
        }
    }

    private function testDeleteTheater()
    {
        echo "  🗑️  Testing delete theater... ";
        
        $deleteData = [
            '_method' => 'DELETE',
            '_token' => $this->getCsrfToken()
        ];
        
        $response = $this->makeRequest('DELETE', '/admin/theaters/1', $deleteData);
        
        if ($response && (strpos($response, 'success') !== false || strpos($response, 'xóa') !== false)) {
            echo "✅ PASS\n";
        } else {
            echo "❌ FAIL - Theater deletion failed\n";
        }
    }

    private function testScheduleWebEndpoints()
    {
        echo "📅 Testing Schedule Web Endpoints...\n";
        
        // Test 1: View create schedule form
        $this->testViewCreateScheduleForm();
        
        // Test 2: Create schedule
        $this->testCreateSchedule();
        
        // Test 3: View schedule details
        $this->testViewScheduleDetails();
        
        // Test 4: View edit schedule form
        $this->testViewEditScheduleForm();
        
        // Test 5: Update schedule
        $this->testUpdateSchedule();
        
        // Test 6: Delete schedule
        $this->testDeleteSchedule();
        
        echo "\n";
    }

    private function testViewCreateScheduleForm()
    {
        echo "  ➕ Testing view create schedule form... ";
        
        $response = $this->makeRequest('GET', '/admin/theaters/1/schedules/create');
        
        if ($response && strpos($response, 'Tạo suất chiếu') !== false) {
            echo "✅ PASS\n";
        } else {
            echo "❌ FAIL - Create schedule form not loaded properly\n";
        }
    }

    private function testCreateSchedule()
    {
        echo "  💾 Testing create schedule... ";
        
        $scheduleData = [
            'movie_id' => '1',
            'theater_id' => '1',
            'room_name' => 'Room 1',
            'start_time' => date('Y-m-d\TH:i'),
            'price' => '100000',
            'status' => 'active',
            '_token' => $this->getCsrfToken()
        ];
        
        $response = $this->makeRequest('POST', '/admin/theaters/1/schedules', $scheduleData);
        
        if ($response && (strpos($response, 'success') !== false || strpos($response, 'tạo') !== false)) {
            echo "✅ PASS\n";
        } else {
            echo "❌ FAIL - Schedule creation failed\n";
        }
    }

    private function testViewScheduleDetails()
    {
        echo "  👁️  Testing view schedule details... ";
        
        $response = $this->makeRequest('GET', '/admin/schedules/1');
        
        if ($response && strpos($response, 'Chi tiết suất chiếu') !== false) {
            echo "✅ PASS\n";
        } else {
            echo "❌ FAIL - Schedule details page not loaded properly\n";
        }
    }

    private function testViewEditScheduleForm()
    {
        echo "  ✏️  Testing view edit schedule form... ";
        
        $response = $this->makeRequest('GET', '/admin/schedules/1/edit');
        
        if ($response && strpos($response, 'Chỉnh sửa suất chiếu') !== false) {
            echo "✅ PASS\n";
        } else {
            echo "❌ FAIL - Edit schedule form not loaded properly\n";
        }
    }

    private function testUpdateSchedule()
    {
        echo "  💾 Testing update schedule... ";
        
        $updateData = [
            'movie_id' => '1',
            'theater_id' => '1',
            'room_name' => 'Room 2',
            'start_time' => date('Y-m-d\TH:i', strtotime('+2 days')),
            'price' => '120000',
            'status' => 'active',
            '_token' => $this->getCsrfToken()
        ];
        
        $response = $this->makeRequest('PUT', '/admin/schedules/1', $updateData);
        
        if ($response && (strpos($response, 'success') !== false || strpos($response, 'cập nhật') !== false)) {
            echo "✅ PASS\n";
        } else {
            echo "❌ FAIL - Schedule update failed\n";
        }
    }

    private function testDeleteSchedule()
    {
        echo "  🗑️  Testing delete schedule... ";
        
        $deleteData = [
            '_method' => 'DELETE',
            '_token' => $this->getCsrfToken()
        ];
        
        $response = $this->makeRequest('DELETE', '/admin/schedules/1', $deleteData);
        
        if ($response && (strpos($response, 'success') !== false || strpos($response, 'xóa') !== false)) {
            echo "✅ PASS\n";
        } else {
            echo "❌ FAIL - Schedule deletion failed\n";
        }
    }

    private function getCsrfToken()
    {
        // Lấy CSRF token từ trang
        $response = $this->makeRequest('GET', '/admin/theaters');
        
        if (preg_match('/name="_token" value="([^"]+)"/', $response, $matches)) {
            return $matches[1];
        }
        
        return '';
    }

    private function makeRequest($method, $endpoint, $data = null)
    {
        $url = $this->baseUrl . $endpoint;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
        
        if ($data) {
            if ($method === 'POST' || $method === 'PUT' || $method === 'DELETE') {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/x-www-form-urlencoded'
                ]);
            }
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        // Log request details
        echo "    🔍 {$method} {$endpoint} -> HTTP {$httpCode}\n";
        
        return $response;
    }
}

// Chạy tests
$tester = new AdminWebTester();
$tester->runAllTests();
