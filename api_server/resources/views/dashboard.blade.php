@extends('layout')

@section('content')
    @auth
        @if(auth()->user()->isCustomer())
            <!-- CUSTOMER DASHBOARD -->
            <div class="text-center mb-12">
                <div class="floating-animation inline-block mb-6">
                    <div
                        class="w-24 h-24 rounded-full bg-white/10 border-2 border-emerald-400/60 shadow-lg flex items-center justify-center mx-auto p-2">
                        <img src="{{ asset('images/logo.png') }}" alt="Cinema Automation" class="w-16 h-16 object-contain rounded-full"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div style="display: none;"
                            class="w-16 h-16 rounded-full bg-gradient-to-r from-green-500 to-emerald-500 flex items-center justify-center">
                            <i class="fas fa-user text-white text-3xl"></i>
                        </div>
                    </div>
                </div>
                <h1
                    class="text-5xl font-bold text-white mb-4 bg-gradient-to-r from-green-400 to-emerald-400 bg-clip-text text-transparent">
                    Welcome, {{ auth()->user()->name }}!
                </h1>
                <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                    Discover movies now showing and buy your ticket instantly.
                </p>
            </div>

            <!-- Movie Categories -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-white mb-8 text-center">
                    <i class="fas fa-th-large mr-3 text-blue-400"></i>Movie Categories
                </h2>
                
                <!-- Loading State - Hidden by default -->
                <div id="movieLoadingState" style="display: none !important; visibility: hidden !important;">
                    <div class="w-12 h-12 border-4 border-yellow-400 border-t-transparent rounded-full mx-auto mb-4 animate-spin"></div>
                    <p class="text-white">Loading movies...</p>
                </div>
                
                <!-- Categories Container -->
                <div id="categoriesContainer" class="space-y-12">
                    <!-- Categories will be loaded here via JavaScript -->
                </div>
                
                <!-- Empty State -->
                <div id="movieEmptyState" class="text-center py-12 hidden">
                    <div class="w-24 h-24 bg-gray-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-film text-gray-400 text-3xl"></i>
                    </div>
                    <h4 class="text-xl font-bold text-white mb-2">No Movies Found</h4>
                    <p class="text-gray-400 mb-4">There are currently no movies in theaters.</p>
                </div>
            </div>

        @else
            <!-- ADMIN DASHBOARD -->
            <div class="text-center mb-12">
                <div class="floating-animation inline-block mb-6">
                    <div
                        class="w-24 h-24 rounded-full bg-white/10 border-2 border-emerald-400/60 shadow-lg flex items-center justify-center mx-auto p-2">
                        <img src="{{ asset('images/logo.png') }}" alt="Cinema Automation" class="w-16 h-16 object-contain rounded-full"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div style="display: none;"
                            class="w-16 h-16 rounded-full bg-gradient-to-r from-green-500 to-emerald-500 flex items-center justify-center">
                            <i class="fas fa-cog text-white text-3xl"></i>
                        </div>
                    </div>
                </div>
                <h1
                    class="text-5xl font-bold text-white mb-4 bg-gradient-to-r from-green-400 to-emerald-400 bg-clip-text text-transparent">
                    Management Dashboard
                </h1>
                <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                    Manage your cinema operations and view reports.
                </p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                <div class="stat-card p-6 rounded-2xl text-center card-hover">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-film text-white text-xl"></i>
                    </div>
                    <div class="text-3xl font-bold text-gray-800 mb-2">50</div>
                    <div class="text-gray-600 font-medium">Active Movies</div>
                </div>

                <div class="stat-card p-6 rounded-2xl text-center card-hover">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-building text-white text-xl"></i>
                    </div>
                    <div class="text-3xl font-bold text-gray-800 mb-2">10</div>
                    <div class="text-gray-600 font-medium">Cinemas</div>
                </div>

                <div class="stat-card p-6 rounded-2xl text-center card-hover">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clock text-white text-xl"></i>
                    </div>
                    <div class="text-3xl font-bold text-gray-800 mb-2">150</div>
                    <div class="text-gray-600 font-medium">Daily Showtimes</div>
                </div>

                <div class="stat-card p-6 rounded-2xl text-center card-hover">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-ticket-alt text-white text-xl"></i>
                    </div>
                    <div class="text-3xl font-bold text-gray-800 mb-2">247</div>
                    <div class="text-gray-600 font-medium">Tickets Sold</div>
                </div>
            </div>

            <!-- Admin Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="glass-effect p-8 rounded-2xl text-center card-hover">
                    <div
                        class="w-16 h-16 bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-play text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-4">Movie List</h3>
                    <p class="text-gray-300 mb-6">Browse all movies currently showing.</p>
                    <a href="/movies"
                        class="w-full inline-block bg-gradient-to-r from-green-500 to-emerald-600 text-white py-3 px-6 rounded-xl font-semibold hover:from-green-600 hover:to-emerald-700 transition-all duration-300">
                        View Movies
                    </a>
                </div>

                <div class="glass-effect p-8 rounded-2xl text-center card-hover">
                    <div
                        class="w-16 h-16 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-ticket-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-4">Ticket Sales</h3>
                    <p class="text-gray-300 mb-6">Make fast and easy ticket sales.</p>
                    <a href="/tickets"
                        class="w-full inline-block bg-gradient-to-r from-emerald-500 to-teal-600 text-white py-3 px-6 rounded-xl font-semibold hover:from-emerald-600 hover:to-teal-700 transition-all duration-300">
                        Sell Ticket
                    </a>
                </div>

                <div class="glass-effect p-8 rounded-2xl text-center card-hover">
                    <div
                        class="w-16 h-16 bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-cog text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-4">Admin Panel</h3>
                    <p class="text-gray-300 mb-6">System management and reports.</p>
                    <a href="/admin"
                        class="w-full inline-block bg-gradient-to-r from-green-500 to-emerald-600 text-white py-3 px-6 rounded-xl font-semibold hover:from-green-600 hover:to-emerald-700 transition-all duration-300">
                        Admin Panel
                    </a>
                </div>
            </div>
        @endif
    @else
            <!-- GUEST DASHBOARD -->
            <div class="text-center mb-12">
                <div class="floating-animation inline-block mb-6">
                    <div
                        class="w-24 h-24 rounded-full bg-white/10 border-2 border-emerald-400/60 shadow-lg flex items-center justify-center mx-auto p-2">
                        <img src="{{ asset('images/logo.png') }}" alt="Cinema Automation" class="w-16 h-16 object-contain rounded-full"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div style="display: none;"
                            class="w-16 h-16 rounded-full bg-gradient-to-r from-green-500 to-emerald-500 flex items-center justify-center">
                            <i class="fas fa-film text-white text-3xl"></i>
                        </div>
                    </div>
                </div>
                <h1
                    class="text-5xl font-bold text-white mb-4 bg-gradient-to-r from-green-400 to-emerald-400 bg-clip-text text-transparent">
                    Welcome to Cinema Automation
                </h1>
                <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                    Experience unforgettable cinema with the latest movies, comfortable seats and digital sound.
                </p>
            </div>

            <!-- Guest Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="glass-effect p-8 rounded-2xl text-center card-hover">
                    <div
                        class="w-16 h-16 bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-play text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-4">Now Showing</h3>
                    <p class="text-gray-300 mb-6">Discover all movies currently in theaters.</p>
                    <a href="/movies"
                        class="w-full inline-block bg-gradient-to-r from-green-500 to-emerald-600 text-white py-3 px-6 rounded-xl font-semibold hover:from-green-600 hover:to-emerald-700 transition-all duration-300">
                        View Movies
                    </a>
                </div>

                <div class="glass-effect p-8 rounded-2xl text-center card-hover">
                    <div
                        class="w-16 h-16 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-sign-in-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-4">Login</h3>
                    <p class="text-gray-300 mb-6">Login to buy tickets.</p>
                    <a href="/login"
                        class="w-full inline-block bg-gradient-to-r from-emerald-500 to-teal-600 text-white py-3 px-6 rounded-xl font-semibold hover:from-emerald-600 hover:to-teal-700 transition-all duration-300">
                        Login
                    </a>
                </div>
            </div>
        @endauth

@auth
    @if(auth()->user()->isCustomer())
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadMovies();
        });

        async function loadMovies() {
            try {
                console.log('Dashboard - Loading movies...');
                
                let url = '/api/movies?per_page=100';
                
                console.log('Dashboard - API URL:', url);
                const response = await axios.get(url);
                console.log('Dashboard - API Response:', response.data);
                
                const movies = response.data.data.data || response.data.data;
                console.log('Dashboard - Movie count:', movies.length);
                
                if (movies.length === 0) {
                    console.log('Dashboard - No movies found, showing empty state');
                    showEmpty();
                } else {
                    console.log('Dashboard - Grouping movies by genre');
                    renderMoviesByGenre(movies);
                    console.log('Dashboard - Kategoriler render edildi');
                }
                
            } catch (error) {
                console.error('Dashboard - Failed to load movies:', error);
                console.error('Dashboard - Error details:', error.response?.data);
                showEmpty();
            }
        }

        function showLoading() {
            document.getElementById('movieLoadingState').classList.remove('hidden');
            document.getElementById('movieGrid').classList.add('hidden');
            document.getElementById('movieEmptyState').classList.add('hidden');
        }

        function showGrid() {
            console.log('Dashboard - showGrid called');
            const loadingState = document.getElementById('movieLoadingState');
            const movieGrid = document.getElementById('movieGrid');
            const emptyState = document.getElementById('movieEmptyState');
            
            // Completely hide loading state
            if (loadingState) {
                loadingState.style.display = 'none';
                loadingState.style.visibility = 'hidden';
                loadingState.style.opacity = '0';
                loadingState.classList.add('hidden');
            }
            
            // Show movie grid
            if (movieGrid) {
                movieGrid.style.display = 'grid';
                movieGrid.style.visibility = 'visible';
                movieGrid.style.opacity = '1';
                movieGrid.classList.remove('hidden');
            }
            
            // Hide empty state
            if (emptyState) {
                emptyState.style.display = 'none';
                emptyState.style.visibility = 'hidden';
                emptyState.style.opacity = '0';
                emptyState.classList.add('hidden');
            }
            
            console.log('Dashboard - Loading display:', loadingState?.style.display);
            console.log('Dashboard - Grid display:', movieGrid?.style.display);
        }

        function showEmpty() {
            document.getElementById('movieLoadingState').classList.add('hidden');
            document.getElementById('movieGrid').classList.add('hidden');
            document.getElementById('movieEmptyState').classList.remove('hidden');
        }

        function renderMoviesByGenre(movies) {
            console.log('Dashboard - renderMoviesByGenre called, movie count:', movies.length);
            
            // Group movies by genre - separate Horror and Thriller
            const categories = {};
            movies.forEach(movie => {
                const genreStr = movie.genre || 'Other';
                
                // Split comma separated genres
                const genres = genreStr.split(',').map(g => g.trim()).filter(g => g);
                
                genres.forEach(genre => {
                    // Separate Horror and Thriller categories
                    if (genre.includes('Korku') && !genre.includes('Gerilim')) {
                        if (!categories['Korku']) {
                            categories['Korku'] = [];
                        }
                        categories['Korku'].push(movie);
                    } else if (genre.includes('Gerilim') && !genre.includes('Korku')) {
                        if (!categories['Gerilim']) {
                            categories['Gerilim'] = [];
                        }
                        categories['Gerilim'].push(movie);
                    } else if (genre.includes('Korku') && genre.includes('Gerilim')) {
                        // If contains both Horror and Thriller, add to both
                        if (!categories['Korku']) {
                            categories['Korku'] = [];
                        }
                        if (!categories['Gerilim']) {
                            categories['Gerilim'] = [];
                        }
                        // Ensure unique add (duplicate check)
                        if (!categories['Korku'].some(m => m.id === movie.id)) {
                            categories['Korku'].push(movie);
                        }
                        if (!categories['Gerilim'].some(m => m.id === movie.id)) {
                            categories['Gerilim'].push(movie);
                        }
                    } else {
                        // Other genres
                        if (!categories[genre]) {
                            categories[genre] = [];
                        }
                        // Duplicate check
                        if (!categories[genre].some(m => m.id === movie.id)) {
                            categories[genre].push(movie);
                        }
                    }
                });
            });
            
            // Pick most popular genres (at least 2 movies)
            const popularCategories = Object.entries(categories)
                .filter(([genre, films]) => films.length >= 2)
                .sort((a, b) => b[1].length - a[1].length)
                .slice(0, 8); // Show at most 8 categories
            
            console.log('Dashboard - Kategoriler:', popularCategories.map(([genre, films]) => `${genre}: ${films.length} film`));
            
            const categoriesContainer = document.getElementById('categoriesContainer');
            let html = '';
            
            popularCategories.forEach(([genre, films]) => {
                const genreIcon = getGenreIcon(genre);
                const genreColor = getGenreColor(genre);
                
                html += `
                    <div class="category-section mb-12">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center">
                                <div class="w-12 h-12 ${genreColor} rounded-xl flex items-center justify-center mr-4">
                                    <i class="${genreIcon} text-white text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-white">${genre}</h3>
                                    <p class="text-gray-400">${films.length} film</p>
                                </div>
                            </div>
                            ${films.length > 10 ? `
                                <a href="/movies?genre=${encodeURIComponent(genre)}" 
                                   class="text-blue-400 hover:text-blue-300 font-semibold flex items-center">
                                    View All <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            ` : ''}
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-4">
                `;
                
                // Show at most 10 movies per category
                films.slice(0, 10).forEach(movie => {
                    const posterUrl = movie.poster_url && movie.poster_url.trim() !== '' ? movie.poster_url : null;
                    
                    html += `
                        <div class="glass-effect rounded-xl overflow-hidden card-hover">
                            <div class="h-56 bg-gradient-to-br ${getGenreGradient(genre)} flex items-center justify-center relative overflow-hidden">
                                ${posterUrl ? `
                                    <img src="${posterUrl}" alt="${movie.title}" 
                                         class="w-full h-full object-cover"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="hidden w-full h-full bg-gradient-to-br ${getGenreGradient(genre)} flex items-center justify-center">
                                        <i class="fas fa-film text-white text-4xl opacity-50"></i>
                                    </div>
                                ` : `
                                    <i class="fas fa-film text-white text-4xl opacity-50"></i>
                                `}
                                <div class="absolute inset-0 bg-black bg-opacity-20"></div>
                            </div>
                            <div class="p-4">
                                <h4 class="text-base font-bold text-white mb-2 line-clamp-2">${movie.title}</h4>
                                <p class="text-gray-300 text-sm mb-3">${movie.duration} dk</p>
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center">
                                        <i class="fas fa-star text-yellow-400 mr-1"></i>
                                        <span class="text-yellow-400 font-semibold text-sm">${movie.imdb_raiting || movie.imdb_rating || 'N/A'}</span>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <button onclick="showMovieDetails(${movie.id})" 
                                            class="w-full bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-sm font-semibold transition-all">
                                        <i class="fas fa-info-circle mr-1"></i>Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                // Add "More" button if there are more than 10 movies
                if (films.length > 10) {
                    html += `
                        <div class="glass-effect rounded-xl overflow-hidden card-hover flex items-center justify-center min-h-[300px] border-2 border-dashed border-gray-600 hover:border-blue-500 transition-all">
                            <div class="text-center p-6">
                                <div class="w-16 h-16 ${genreColor} rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-plus text-white text-2xl"></i>
                                </div>
                                <h4 class="text-lg font-bold text-white mb-2">More</h4>
                                <p class="text-gray-400 text-sm mb-4">${films.length - 10} more movies</p>
                                <button onclick="window.location.href='/movies?genre=${encodeURIComponent(genre)}'" 
                                        class="bg-gradient-to-r ${getGenreGradient(genre)} hover:opacity-80 text-white px-6 py-3 rounded-lg font-semibold transition-all">
                                    <i class="fas fa-arrow-right mr-2"></i>View All
                                </button>
                            </div>
                        </div>
                    `;
                }
                
                html += `
                        </div>
                    </div>
                `;
            });
            
            categoriesContainer.innerHTML = html;
            console.log('Dashboard - Categories rendered');
        }
        
        function getGenreIcon(genre) {
            const icons = {
                'Action': 'fas fa-fist-raised',
                'Aksiyon': 'fas fa-fist-raised',
                'Adventure': 'fas fa-mountain',
                'Macera': 'fas fa-mountain',
                'Comedy': 'fas fa-laugh',
                'Komedi': 'fas fa-laugh',
                'Drama': 'fas fa-theater-masks',
                'Dram': 'fas fa-theater-masks',
                'Horror': 'fas fa-ghost',
                'Korku': 'fas fa-ghost',
                'Sci-Fi': 'fas fa-rocket',
                'Bilim-Kurgu': 'fas fa-rocket',
                'Bilim Kurgu': 'fas fa-rocket',
                'Thriller': 'fas fa-eye',
                'Gerilim': 'fas fa-eye',
                'Romance': 'fas fa-heart',
                'Romantik': 'fas fa-heart',
                'Animation': 'fas fa-magic',
                'Animasyon': 'fas fa-magic'
            };
            return icons[genre] || 'fas fa-film';
        }
        
        function getGenreColor(genre) {
            const colors = {
                'Action': 'bg-red-500',
                'Aksiyon': 'bg-red-500',
                'Adventure': 'bg-green-500',
                'Macera': 'bg-green-500',
                'Comedy': 'bg-yellow-500',
                'Komedi': 'bg-yellow-500',
                'Drama': 'bg-purple-500',
                'Dram': 'bg-purple-500',
                'Horror': 'bg-gray-800',
                'Korku': 'bg-gray-800',
                'Sci-Fi': 'bg-blue-500',
                'Bilim-Kurgu': 'bg-blue-500',
                'Bilim Kurgu': 'bg-blue-500',
                'Thriller': 'bg-orange-500',
                'Gerilim': 'bg-orange-500',
                'Romance': 'bg-pink-500',
                'Romantik': 'bg-pink-500',
                'Animation': 'bg-indigo-500',
                'Animasyon': 'bg-indigo-500'
            };
            return colors[genre] || 'bg-gray-500';
        }
        
        function getGenreGradient(genre) {
            const gradients = {
                'Action': 'from-red-600 to-red-800',
                'Aksiyon': 'from-red-600 to-red-800',
                'Adventure': 'from-green-600 to-green-800',
                'Macera': 'from-green-600 to-green-800',
                'Comedy': 'from-yellow-600 to-yellow-800',
                'Komedi': 'from-yellow-600 to-yellow-800',
                'Drama': 'from-purple-600 to-purple-800',
                'Dram': 'from-purple-600 to-purple-800',
                'Horror': 'from-gray-800 to-black',
                'Korku': 'from-gray-800 to-black',
                'Sci-Fi': 'from-blue-600 to-blue-800',
                'Bilim-Kurgu': 'from-blue-600 to-blue-800',
                'Bilim Kurgu': 'from-blue-600 to-blue-800',
                'Thriller': 'from-orange-600 to-orange-800',
                'Gerilim': 'from-orange-600 to-orange-800',
                'Romance': 'from-pink-600 to-pink-800',
                'Romantik': 'from-pink-600 to-pink-800',
                'Animation': 'from-indigo-600 to-indigo-800',
                'Animasyon': 'from-indigo-600 to-indigo-800'
            };
            return gradients[genre] || 'from-gray-600 to-gray-800';
        }
        

        async function showMovieDetails(movieId) {
            try {
                const response = await axios.get(`/api/movies/${movieId}`);
                const movie = response.data.data;
                
                // Create modal
                createMovieDetailModal(movie);
            } catch (error) {
                console.error('Failed to load movie details:', error);
                alert('Failed to load movie details!');
            }
        }
        
        function createMovieDetailModal(movie) {
            // Remove existing modal if any
            const existingModal = document.getElementById('movieDetailModal');
            if (existingModal) {
                existingModal.remove();
            }
            
            // Poster URL check
            const posterUrl = movie.poster_url && movie.poster_url.trim() !== '' ? movie.poster_url : null;
            
            // Check user login state
            const isLoggedIn = window.userPermissions && window.userPermissions.isLoggedIn;
            
            // "" button - only for logged-in users
            let buyTicketButton = '';
            if (isLoggedIn) {
                buyTicketButton = `
                    <button onclick="window.location.href='/buy-tickets?movie=${movie.id}'" 
                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded-lg font-semibold transition-all flex items-center">
                        <i class="fas fa-ticket-alt mr-2"></i>Buy Ticket
                    </button>
                `;
            } else {
                buyTicketButton = `
                    <button onclick="window.location.href='/login'" 
                            class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-lg font-semibold transition-all flex items-center">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </button>
                `;
            }
            
            // Build modal HTML
            const modalHTML = `
                <div id="movieDetailModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4">
                    <div class="bg-gray-900 rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 border-b border-gray-700">
                            <h2 class="text-2xl font-bold text-white">🎬 Movie Details</h2>
                            <button onclick="closeMovieDetailModal()" class="text-gray-400 hover:text-white text-2xl">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <!-- Content -->
                        <div class="p-6">
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                <!-- Poster -->
                                <div class="lg:col-span-1">
                                    <div class="aspect-[2/3] bg-gradient-to-br from-purple-600 to-blue-600 rounded-xl overflow-hidden">
                                        ${posterUrl ? `
                                            <img src="${posterUrl}" alt="${movie.title}" 
                                                 class="w-full h-full object-cover"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="hidden w-full h-full bg-gradient-to-br from-purple-600 to-blue-600 flex items-center justify-center">
                                                <i class="fas fa-film text-white text-6xl opacity-50"></i>
                                            </div>
                                        ` : `
                                            <div class="w-full h-full bg-gradient-to-br from-purple-600 to-blue-600 flex items-center justify-center">
                                                <i class="fas fa-film text-white text-6xl opacity-50"></i>
                                            </div>
                                        `}
                                    </div>
                                </div>
                                
                                <!-- Details -->
                                <div class="lg:col-span-2">
                                    <h1 class="text-3xl font-bold text-white mb-4">${movie.title}</h1>
                                    
                                    <!-- Rating -->
                                    <div class="flex items-center mb-4">
                                        <div class="flex items-center bg-yellow-500/20 px-3 py-1 rounded-full">
                                            <i class="fas fa-star text-yellow-400 mr-2"></i>
                                            <span class="text-yellow-300 font-semibold">${movie.imdb_raiting || movie.imdb_rating || 'N/A'}</span>
                                        </div>
                                        <span class="text-gray-400 ml-4">IMDB Rating</span>
                                    </div>
                                    
                                    <!-- Info Grid -->
                                    <div class="grid grid-cols-2 gap-4 mb-6">
                                        <div class="bg-gray-800/50 p-4 rounded-lg">
                                            <div class="flex items-center mb-2">
                                                <i class="fas fa-clock text-blue-400 mr-2"></i>
                                                <span class="text-gray-300">Duration</span>
                                            </div>
                                            <span class="text-white font-semibold">${movie.duration || 'N/A'} dakika</span>
                                        </div>
                                        
                                        <div class="bg-gray-800/50 p-4 rounded-lg">
                                            <div class="flex items-center mb-2">
                                                <i class="fas fa-tag text-green-400 mr-2"></i>
                                                <span class="text-gray-300">Genre</span>
                                            </div>
                                            <span class="text-white font-semibold">${movie.genre || 'N/A'}</span>
                                        </div>
                                        
                                        <div class="bg-gray-800/50 p-4 rounded-lg">
                                            <div class="flex items-center mb-2">
                                                <i class="fas fa-calendar text-purple-400 mr-2"></i>
                                                <span class="text-gray-300">Release Date</span>
                                            </div>
                                            <span class="text-white font-semibold">${movie.release_date || 'N/A'}</span>
                                        </div>
                                        
                                        <div class="bg-gray-800/50 p-4 rounded-lg">
                                            <div class="flex items-center mb-2">
                                                <i class="fas fa-globe text-orange-400 mr-2"></i>
                                                <span class="text-gray-300">Language</span>
                                            </div>
                                            <span class="text-white font-semibold">${movie.language || 'N/A'}</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Description -->
                                    <div class="mb-6">
                                        <h3 class="text-lg font-semibold text-white mb-3">
                                            <i class="fas fa-info-circle text-blue-400 mr-2"></i>Overview
                                        </h3>
                                        <p class="text-gray-300 leading-relaxed">${movie.description || 'No description available.'}</p>
                                    </div>
                                    
                                    ${!isLoggedIn ? `
                                    <!-- Login warning -->
                                    <div class="mb-6 bg-blue-500/20 border border-blue-500/50 rounded-lg p-4">
                                        <div class="flex items-center">
                                            <i class="fas fa-info-circle text-blue-400 mr-3 text-xl"></i>
                                            <p class="text-blue-200">
                                                You must <strong>log in</strong> to purchase tickets.
                                            </p>
                                        </div>
                                    </div>
                                    ` : ''}
                                    
                            <!-- Actions -->
                                    <div class="flex gap-4">
                                        ${buyTicketButton}
                                        <button onclick="closeMovieDetailModal()" 
                                            class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-semibold transition-all">
                                            <i class="fas fa-times mr-2"></i>Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Append modal to body
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            
            // Show modal
            const modal = document.getElementById('movieDetailModal');
            modal.style.display = 'flex';
            
            // Close on ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeMovieDetailModal();
                }
            });
        }

        function closeMovieDetailModal() {
            const modal = document.getElementById('movieDetailModal');
            if (modal) {
                modal.remove();
            }
        }
        </script>
    @endif
@endauth
@endsection