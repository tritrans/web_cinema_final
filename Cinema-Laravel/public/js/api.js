/**
 * API Client for Cinema Web Application
 */
class ApiClient {
    constructor() {
        this.baseUrl = '/api';
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    }

    /**
     * Make API request
     */
    async request(endpoint, options = {}) {
        const url = `${this.baseUrl}${endpoint}`;
        const config = {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            ...options
        };

        // Add CSRF token if available
        if (this.csrfToken) {
            config.headers['X-CSRF-TOKEN'] = this.csrfToken;
        }

        try {
            const response = await fetch(url, config);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || `HTTP ${response.status}`);
            }

            return data;
        } catch (error) {
            console.error('API Request failed:', error);
            throw error;
        }
    }

    /**
     * GET request
     */
    async get(endpoint, params = {}) {
        const queryString = new URLSearchParams(params).toString();
        const url = queryString ? `${endpoint}?${queryString}` : endpoint;
        return this.request(url, { method: 'GET' });
    }

    /**
     * POST request
     */
    async post(endpoint, data = {}) {
        return this.request(endpoint, {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }

    /**
     * PUT request
     */
    async put(endpoint, data = {}) {
        return this.request(endpoint, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }

    /**
     * DELETE request
     */
    async delete(endpoint) {
        return this.request(endpoint, { method: 'DELETE' });
    }

    // Movie methods
    async getMovies(params = {}) {
        return this.get('/movies', params);
    }

    async getMovie(id) {
        return this.get(`/movies/${id}`);
    }

    async getMovieBySlug(slug) {
        return this.get(`/movies/slug/${slug}`);
    }

    async searchMovies(query) {
        return this.get('/movies/search', { q: query });
    }

    // Review methods
    async createReview(movieId, reviewData) {
        return this.post(`/movies/${movieId}/reviews`, reviewData);
    }

    async getReviews(movieId) {
        return this.get(`/movies/${movieId}/reviews`);
    }

    // Comment methods
    async createComment(movieId, commentData) {
        return this.post(`/movies/${movieId}/comments`, commentData);
    }

    async getComments(movieId) {
        return this.get(`/movies/${movieId}/comments`);
    }

    // Favorite methods
    async addToFavorites(movieId) {
        return this.post(`/movies/${movieId}/favorites`);
    }

    async removeFromFavorites(movieId) {
        return this.delete(`/movies/${movieId}/favorites`);
    }

    async getFavorites() {
        return this.get('/favorites');
    }

    // Booking methods
    async lockSeats(bookingData) {
        return this.post('/booking/lock-seats', bookingData);
    }

    async releaseSeats(bookingData) {
        return this.post('/booking/release-seats', bookingData);
    }

    async createBooking(bookingData) {
        return this.post('/booking', bookingData);
    }

    async getBookingDetails(bookingId) {
        return this.get(`/booking/${bookingId}`);
    }

    async getUserBookings() {
        return this.get('/booking/user');
    }

    async cancelBooking(bookingId) {
        return this.delete(`/booking/${bookingId}`);
    }

    // Snack methods
    async getSnacks() {
        return this.get('/snacks');
    }

    // Schedule methods
    async getSchedules(params = {}) {
        return this.get('/schedules', params);
    }

    async getSchedule(scheduleId) {
        return this.get(`/schedules/${scheduleId}`);
    }

    async getScheduleSeats(scheduleId) {
        return this.get(`/schedules/${scheduleId}/seats`);
    }

    // Theater methods
    async getTheaters() {
        return this.get('/theaters');
    }

    async getTheater(theaterId) {
        return this.get(`/theaters/${theaterId}`);
    }

    // Genre methods
    async getGenres() {
        return this.get('/genres');
    }

    // User methods
    async getCurrentUser() {
        return this.get('/auth/me');
    }

    async updateProfile(userData) {
        return this.put('/auth/profile', userData);
    }

    async uploadAvatar(file) {
        const formData = new FormData();
        formData.append('avatar', file);
        
        try {
            const response = await fetch('/api/upload-avatar', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Update user data in localStorage
                const user = JSON.parse(localStorage.getItem('user') || '{}');
                user.avatar = result.data.avatar_url;
                localStorage.setItem('user', JSON.stringify(user));
                
                // Update session if available
                if (window.apiClient) {
                    window.apiClient.setUser(user);
                }
            }
            
            return result;
        } catch (error) {
            console.error('Avatar upload error:', error);
            return {
                success: false,
                message: 'Có lỗi xảy ra khi upload avatar: ' + error.message
            };
        }
    }
}

// Global API client instance
window.apiClient = new ApiClient();

// Utility functions
window.showToast = function(message, type = 'success') {
    // Simple toast notification
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
    }`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
};

window.showLoading = function(show = true) {
    const loader = document.getElementById('loading-spinner');
    if (loader) {
        loader.style.display = show ? 'block' : 'none';
    }
};

window.handleApiError = function(error) {
    console.error('API Error:', error);
    
    let message = 'Có lỗi xảy ra. Vui lòng thử lại.';
    
    if (error.message) {
        message = error.message;
    }
    
    showToast(message, 'error');
};

// Auto-refresh token logic
setInterval(async () => {
    try {
        await window.apiClient.getCurrentUser();
    } catch (error) {
        // Token might be expired, redirect to login
        if (error.message.includes('401') || error.message.includes('Phiên đăng nhập')) {
            window.location.href = '/login';
        }
    }
}, 5 * 60 * 1000); // Check every 5 minutes

// Image error handling
document.addEventListener('DOMContentLoaded', function() {
    // Handle image loading errors
    document.addEventListener('error', function(e) {
        if (e.target.tagName === 'IMG') {
            const img = e.target;
            const src = img.src;
            
            // If it's a Google Drive image that failed, try alternative CDN
            if (src.includes('drive.google.com') || src.includes('googleusercontent.com')) {
                // Try different Google Drive CDN formats
                if (src.includes('lh3.googleusercontent.com')) {
                    // Try drive.google.com format
                    const fileId = src.match(/d\/([a-zA-Z0-9_-]+)/);
                    if (fileId) {
                        img.src = `https://drive.google.com/uc?export=view&id=${fileId[1]}`;
                        return;
                    }
                } else if (src.includes('drive.google.com')) {
                    // Try lh3.googleusercontent.com format
                    const fileId = src.match(/id=([a-zA-Z0-9_-]+)/);
                    if (fileId) {
                        img.src = `https://lh3.googleusercontent.com/d/${fileId[1]}`;
                        return;
                    }
                }
                
                // If all CDN attempts fail, show placeholder
                img.src = '/images/placeholder-movie.svg';
                img.style.opacity = '0.7';
                img.classList.add('error');
            } else {
                // For other failed images, show placeholder
                img.src = '/images/placeholder-movie.svg';
                img.style.opacity = '0.7';
                img.classList.add('error');
            }
        }
    }, true);
    
    // Add loading states to images
    const images = document.querySelectorAll('img[src*="drive.google.com"], img[src*="googleusercontent.com"], img[src*="/api/image-proxy"]');
    images.forEach(img => {
        img.classList.add('movie-poster');
        
        img.addEventListener('loadstart', function() {
            this.classList.add('loading');
        });
        
        img.addEventListener('load', function() {
            this.classList.remove('loading');
            this.classList.add('loaded');
        });
        
        img.addEventListener('error', function() {
            this.classList.remove('loading');
            this.classList.add('error');
        });
    });
});
