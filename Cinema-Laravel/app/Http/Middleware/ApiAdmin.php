<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\ApiService;
use Symfony\Component\HttpFoundation\Response;

class ApiAdmin
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
        // First check if user is authenticated
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

        // Check if user has admin role
        $user = session('user');
        if (!$user || !in_array($user['role'], ['admin', 'super_admin', 'movie_manager', 'review_manager', 'violation_manager'])) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền truy cập trang này',
                    'redirect' => route('home')
                ], 403);
            }
            
            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        return $next($request);
    }
}
