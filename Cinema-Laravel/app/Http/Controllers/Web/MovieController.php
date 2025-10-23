<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ApiService;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index(Request $request)
    {
        try {
            $searchQuery = $request->get('search', '');
            $sortBy = $request->get('sort', 'newest');
            $filterYear = $request->get('year', 'all');

            // Get all movies
            $response = $this->apiService->getMovies();
            $allMovies = [];
            
            if ($response['success'] && isset($response['data'])) {
                $allMovies = $response['data'];
                
                // Handle paginated response
                if (isset($allMovies['data']) && is_array($allMovies['data'])) {
                    $allMovies = $allMovies['data'];
                }
            }

            // Apply filters
            $filteredMovies = $this->applyFilters($allMovies, $searchQuery, $filterYear);
            
            // Apply sorting
            $sortedMovies = $this->applySorting($filteredMovies, $sortBy);

            // Create pagination manually
            $perPage = 20;
            $totalMovies = count($sortedMovies);
            $currentPage = $request->get('page', 1);
            $offset = ($currentPage - 1) * $perPage;
            $movies = array_slice($sortedMovies, $offset, $perPage);

            // Create paginator manually
            $movies = new \Illuminate\Pagination\LengthAwarePaginator(
                $movies,
                $totalMovies,
                $perPage,
                $currentPage,
                [
                    'path' => $request->url(),
                    'pageName' => 'page',
                ]
            );

            // Add query parameters to pagination links
            $movies->appends($request->query());

            return view('movies.index', compact(
                'movies', 
                'searchQuery', 
                'sortBy', 
                'filterYear', 
                'totalMovies'
            ));
        } catch (\Exception $e) {
            return view('movies.index', [
                'movies' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20, 1),
                'error' => 'Không thể tải danh sách phim'
            ]);
        }
    }

    public function show($id)
    {
        try {
            $response = $this->apiService->getMovie($id);
            
            if ($response['success']) {
                $movie = $response['data'];
                
                // Try to get cast data
                try {
                    $castResponse = $this->apiService->getMovieCast($id);
                    if ($castResponse['success'] && isset($castResponse['data'])) {
                        $cast = is_array($castResponse['data']) ? $castResponse['data'] : [];
                        $movie['cast'] = array_column($cast, 'name');
                    }
                } catch (\Exception $e) {
                    // If cast API fails, keep original cast data
                }
                
                // Get related movies
                $relatedMovies = [];
                if (isset($movie['genre']) && is_array($movie['genre']) && count($movie['genre']) > 0) {
                    $allMoviesResponse = $this->apiService->getMovies();
                    if ($allMoviesResponse['success']) {
                        $allMovies = $allMoviesResponse['data'];
                        // Handle paginated response from API if necessary
                        if (isset($allMovies['data']) && is_array($allMovies['data'])) {
                            $allMovies = $allMovies['data'];
                        }
                        $relatedMovies = collect($allMovies)
                            ->filter(function($m) use ($movie) {
                                return $m['id'] !== $movie['id'] && 
                                       isset($m['genre']) && 
                                       is_array($m['genre']) && 
                                       array_intersect($m['genre'], $movie['genre']);
                            })
                            ->take(4)
                            ->values()
                            ->toArray();
                    }
                }

                // Get reviews and comments
                $reviewsResponse = $this->apiService->getMovieReviews($id);
                $commentsResponse = $this->apiService->getMovieComments($id);
                
                $reviews = $reviewsResponse['success'] ? $reviewsResponse['data'] : [];
                $comments = $commentsResponse['success'] ? $commentsResponse['data'] : [];

                // Get user favorites
                $userFavorites = [];
                if (session('user')) {
                    $favoritesResponse = $this->apiService->getUserFavorites(session('user')['id']);
                    if ($favoritesResponse['success'] && isset($favoritesResponse['data']['data'])) {
                        $userFavorites = array_column($favoritesResponse['data']['data'], 'movie_id');
                    }
                }

                return view('movies.show', compact('movie', 'relatedMovies', 'reviews', 'comments', 'userFavorites'));
            }

            return redirect()->route('movies.index')->with('error', 'Không tìm thấy phim');
        } catch (\Exception $e) {
            return redirect()->route('movies.index')->with('error', 'Không thể tải thông tin phim');
        }
    }

    public function showBySlug($slug)
    {
        try {
            $response = $this->apiService->getMovieBySlug($slug);
            
            if ($response['success']) {
                $movie = $response['data'];
                
                // Get related movies
                $relatedMovies = [];
                if (isset($movie['genre']) && is_array($movie['genre']) && count($movie['genre']) > 0) {
                    $allMoviesResponse = $this->apiService->getMovies();
                    if ($allMoviesResponse['success']) {
                        $allMovies = $allMoviesResponse['data'];
                        // Handle paginated response from API if necessary
                        if (isset($allMovies['data']) && is_array($allMovies['data'])) {
                            $allMovies = $allMovies['data'];
                        }
                        $relatedMovies = collect($allMovies)
                            ->filter(function($m) use ($movie) {
                                return $m['id'] !== $movie['id'] && 
                                       isset($m['genre']) && 
                                       is_array($m['genre']) && 
                                       array_intersect($m['genre'], $movie['genre']);
                            })
                            ->take(4)
                            ->values()
                            ->toArray();
                    }
                }

                // Get reviews and comments
                $reviewsResponse = $this->apiService->getMovieReviews($movie['id']);
                $commentsResponse = $this->apiService->getMovieComments($movie['id']);
                
                $reviews = $reviewsResponse['success'] ? $reviewsResponse['data'] : [];
                $comments = $commentsResponse['success'] ? $commentsResponse['data'] : [];

                // Get user favorites
                $userFavorites = [];
                if (session('user')) {
                    $favoritesResponse = $this->apiService->getUserFavorites(session('user')['id']);
                    if ($favoritesResponse['success'] && isset($favoritesResponse['data']['data'])) {
                        $userFavorites = array_column($favoritesResponse['data']['data'], 'movie_id');
                    }
                }

                return view('movies.show', compact('movie', 'relatedMovies', 'reviews', 'comments', 'userFavorites'));
            }

            return redirect()->route('movies.index')->with('error', 'Không tìm thấy phim');
        } catch (\Exception $e) {
            return redirect()->route('movies.index')->with('error', 'Không thể tải thông tin phim');
        }
    }

    public function genres(Request $request)
    {
        try {
            $searchQuery = $request->get('search', '');
            $selectedGenre = $request->get('category', 'Tất cả');
            
            // Map URL categories to Vietnamese genre names
            $categoryMap = [
                'drama' => 'Tâm lý',
                'romance' => 'Lãng mạn', 
                'action' => 'Hành động',
                'comedy' => 'Hài hước'
            ];

            if (isset($categoryMap[$selectedGenre])) {
                $selectedGenre = $categoryMap[$selectedGenre];
            }

            $response = $this->apiService->getMovies();
            $allMovies = $response['success'] ? $response['data'] : [];

            // Get available genres
            $availableGenres = [];
            foreach ($allMovies as $movie) {
                if (isset($movie['genre']) && is_array($movie['genre'])) {
                    $availableGenres = array_merge($availableGenres, $movie['genre']);
                }
            }
            $availableGenres = array_unique($availableGenres);
            sort($availableGenres);

            // Filter movies
            $filteredMovies = collect($allMovies)->filter(function($movie) use ($selectedGenre, $searchQuery) {
                $matchesGenre = $selectedGenre === "Tất cả" || 
                    (isset($movie['genre']) && is_array($movie['genre']) && in_array($selectedGenre, $movie['genre']));
                
                $matchesSearch = empty($searchQuery) || 
                    stripos($movie['title'], $searchQuery) !== false ||
                    stripos($movie['description'] ?? '', $searchQuery) !== false;
                
                return $matchesGenre && $matchesSearch;
            })->values()->toArray();

            return view('movies.genres', compact(
                'filteredMovies', 
                'availableGenres', 
                'selectedGenre', 
                'searchQuery'
            ));
        } catch (\Exception $e) {
            return view('movies.genres', [
                'filteredMovies' => [],
                'availableGenres' => [],
                'error' => 'Không thể tải danh sách phim'
            ]);
        }
    }

    private function applyFilters($movies, $searchQuery, $filterYear)
    {
        $filtered = collect($movies);

        // Search filter
        if (!empty($searchQuery)) {
            $filtered = $filtered->filter(function($movie) use ($searchQuery) {
                return stripos($movie['title'], $searchQuery) !== false ||
                       stripos($movie['description'] ?? '', $searchQuery) !== false;
            });
        }

        // Year filter
        if ($filterYear !== 'all') {
            $filtered = $filtered->filter(function($movie) use ($filterYear) {
                $year = date('Y', strtotime($movie['release_date']));
                
                switch ($filterYear) {
                    case '2010s':
                        return $year >= 2010 && $year < 2020;
                    case '2000s':
                        return $year >= 2000 && $year < 2010;
                    case '1990s':
                        return $year >= 1990 && $year < 2000;
                    case 'before1990':
                        return $year < 1990;
                    default:
                        return $year == $filterYear;
                }
            });
        }

        return $filtered->values()->toArray();
    }

    private function applySorting($movies, $sortBy)
    {
        $sorted = collect($movies);

        switch ($sortBy) {
            case 'newest':
                $sorted = $sorted->sortByDesc(function($movie) {
                    return strtotime($movie['release_date']);
                });
                break;
            case 'oldest':
                $sorted = $sorted->sortBy(function($movie) {
                    return strtotime($movie['release_date']);
                });
                break;
            case 'rating':
                $sorted = $sorted->sortByDesc(function($movie) {
                    return $movie['rating'] ?? 0;
                });
                break;
            case 'title':
                $sorted = $sorted->sortBy('title');
                break;
        }

        return $sorted->values()->toArray();
    }

    public function apiMovies()
    {
        try {
            $response = $this->apiService->getMovies();
            
            if ($response['success']) {
                $movies = $response['data'];
                
                // Handle paginated response
                if (isset($movies['data']) && is_array($movies['data'])) {
                    $movies = $movies['data'];
                }
                
                return response()->json([
                    'success' => true,
                    'data' => $movies
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải danh sách phim'
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server'
            ], 500);
        }
    }
}
