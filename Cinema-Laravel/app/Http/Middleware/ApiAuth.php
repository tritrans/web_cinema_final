<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\ApiService;
use Symfony\Component\HttpFoundation\Response;

class ApiAuth
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated via API
        if (!$this->apiService->isAuthenticated()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn cần đăng nhập để truy cập tài nguyên này',
                    'redirect' => route('login')
                ], 401);
            }
            
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để truy cập trang này');
        }

        // Verify token with API if needed
        $user = session('user');
        if (!$user) {
            $currentUserResponse = $this->apiService->getCurrentUser();
            if (!$currentUserResponse['success']) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Phiên đăng nhập đã hết hạn',
                        'redirect' => route('login')
                    ], 401);
                }
                
                return redirect()->route('login')->with('error', 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.');
            }
            
            // Update session with fresh user data
            session(['user' => $currentUserResponse['data']]);
        }

        return $next($request);
    }
}
