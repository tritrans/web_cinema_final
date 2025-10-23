<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ApiService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index()
    {
        try {
            // Fetch featured movies and all movies
            $featuredResponse = $this->apiService->getFeaturedMovies();
            $moviesResponse = $this->apiService->getMovies();

            // Safely extract data with proper validation
            $featuredMovies = [];
            if ($featuredResponse['success'] && isset($featuredResponse['data']) && is_array($featuredResponse['data'])) {
                $featuredMovies = $featuredResponse['data'];
            }

            $allMovies = [];
            if ($moviesResponse['success'] && isset($moviesResponse['data'])) {
                // Handle paginated response
                if (isset($moviesResponse['data']['data']) && is_array($moviesResponse['data']['data'])) {
                    $allMovies = $moviesResponse['data']['data'];
                } elseif (is_array($moviesResponse['data'])) {
                    $allMovies = $moviesResponse['data'];
                }
            }

            // Get recent movies (sorted by created_at) with safety checks
            $recentMovies = collect($allMovies)
                ->filter(function($movie) {
                    return is_array($movie) && isset($movie['id']) && isset($movie['title']);
                })
                ->sortByDesc(function($movie) {
                    return $movie['created_at'] ?? '1970-01-01';
                })
                ->take(8)
                ->values()
                ->toArray();

            // Ensure featured movies are also arrays
            $featuredMovies = collect($featuredMovies)
                ->filter(function($movie) {
                    return is_array($movie) && isset($movie['id']) && isset($movie['title']);
                })
                ->values()
                ->toArray();

            $heroMovie = !empty($featuredMovies) ? $featuredMovies[0] : null;

            return view('home', compact('featuredMovies', 'recentMovies', 'heroMovie'));
        } catch (\Exception $e) {
            \Log::error('HomeController error: ' . $e->getMessage());
            return view('home', [
                'featuredMovies' => [],
                'recentMovies' => [],
                'heroMovie' => null,
                'error' => 'Không thể tải dữ liệu phim: ' . $e->getMessage()
            ]);
        }
    }
}