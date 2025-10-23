<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function showLogin()
    {
        if ($this->apiService->isAuthenticated()) {
            return redirect()->route('home');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $result = $this->apiService->login([
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if ($result['success']) {
            // Store user data and JWT token in session
            if (isset($result['data']['user'])) {
                session(['user' => $result['data']['user']]);
                \Log::info('User stored in session:', ['user' => $result['data']['user']]);
            }
            if (isset($result['data']['access_token'])) {
                session(['jwt_token' => $result['data']['access_token']]);
                \Log::info('JWT token stored in session');
            }
            
            // Force session save
            session()->save();
            \Log::info('Session saved after login');
            
            // Set JWT token in both session and cookie for 7 days
            $response = redirect()->route('home')->with('success', 'Đăng nhập thành công!');
            if (isset($result['data']['access_token'])) {
                // Also store in cookie for persistence
                $response->cookie('jwt_token', $result['data']['access_token'], 60 * 24 * 7); // 7 days
                // Add script to store in localStorage
                $response->with('jwt_token', $result['data']['access_token']);
            }
            return $response;
        }

        return back()->withErrors(['email' => $result['message'] ?? 'Đăng nhập thất bại'])->withInput();
    }

    public function showRegister()
    {
        if ($this->apiService->isAuthenticated()) {
            return redirect()->route('home');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        \Log::info('Register request received:', [
            'name' => $request->name,
            'email' => $request->email,
            'has_password' => !empty($request->password),
            'has_password_confirmation' => !empty($request->password_confirmation)
        ]);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            \Log::warning('Register validation failed:', $validator->errors()->toArray());
            return back()->withErrors($validator)->withInput();
        }

        $registerData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'password_confirmation' => $request->password_confirmation,
        ];

        \Log::info('Calling API register with data:', $registerData);

        $result = $this->apiService->register($registerData);

        \Log::info('API register response:', $result);

        if ($result['success']) {
            // Send OTP for verification
            $otpResponse = $this->apiService->sendOtp([
                'email' => $request->email,
                'type' => 'verification'
            ]);

            \Log::info('OTP send response:', $otpResponse);

            if ($otpResponse['success']) {
                return redirect()->route('verify-otp', [
                    'email' => $request->email,
                    'name' => $request->name,
                    'password' => $request->password,
                    'password_confirmation' => $request->password_confirmation
                ])->with('success', 'Mã OTP đã được gửi đến email của bạn.');
            } else {
                // If OTP sending failed, still redirect to verify-otp page but with warning
                \Log::warning('OTP sending failed, but proceeding with registration:', $otpResponse);
                return redirect()->route('verify-otp', [
                    'email' => $request->email,
                    'name' => $request->name,
                    'password' => $request->password,
                    'password_confirmation' => $request->password_confirmation
                ])->with('warning', 'Đăng ký thành công nhưng không thể gửi OTP. Vui lòng thử lại sau.');
            }
        }

        // If initial registration API call failed
        \Log::error('Register API failed:', $result);
        return back()->withErrors(['email' => $result['message'] ?? 'Đăng ký thất bại'])->withInput();
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $result = $this->apiService->forgotPassword([
            'email' => $request->email,
        ]);

        if ($result['success']) {
            return redirect()->route('reset-password', ['email' => $request->email])
                ->with('success', 'Mã OTP đã được gửi đến email của bạn.');
        }

        return back()->withErrors(['email' => $result['message'] ?? 'Không thể gửi mã OTP'])->withInput();
    }

    public function showResetPassword(Request $request)
    {
        $email = $request->get('email');
        if (!$email) {
            return redirect()->route('forgot-password');
        }

        return view('auth.reset-password', compact('email'));
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $result = $this->apiService->resetPassword([
            'email' => $request->email,
            'otp' => $request->otp,
            'password' => $request->password,
            'password_confirmation' => $request->password_confirmation,
        ]);

        if ($result['success']) {
            return redirect()->route('login')->with('success', 'Đặt lại mật khẩu thành công! Vui lòng đăng nhập.');
        }

        return back()->withErrors(['otp' => $result['message'] ?? 'Đặt lại mật khẩu thất bại'])->withInput();
    }

    public function showVerifyOtp(Request $request)
    {
        $email = $request->get('email');
        $name = $request->get('name');
        $password = $request->get('password');
        $passwordConfirmation = $request->get('password_confirmation');

        if (!$email || !$name || !$password) {
            return redirect()->route('register');
        }

        return view('auth.verify-otp', compact('email', 'name', 'password', 'passwordConfirmation'));
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'name' => 'required|string',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // First verify OTP
        $verifyResponse = $this->apiService->verifyOtp([
            'email' => $request->email,
            'otp' => $request->otp,
        ]);

        if (!$verifyResponse['success']) {
            return back()->withErrors(['otp' => $verifyResponse['message'] ?? 'Mã OTP không hợp lệ'])->withInput();
        }

        // Then complete registration
        $registerResponse = $this->apiService->register([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'password_confirmation' => $request->password_confirmation,
        ]);

        if ($registerResponse['success']) {
            return redirect()->route('login')->with('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
        }

        return back()->withErrors(['email' => $registerResponse['message'] ?? 'Đăng ký thất bại'])->withInput();
    }

    public function logout()
    {
        $this->apiService->logout();
        $response = redirect()->route('home')->with('success', 'Đăng xuất thành công!');
        // Clear JWT token cookie
        $response->cookie('jwt_token', '', -1);
        return $response;
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $response = $this->apiService->changePassword([
            'current_password' => $request->current_password,
            'password' => $request->password,
            'password_confirmation' => $request->password_confirmation,
        ]);

        return response()->json($response);
    }
}
