<!-- Step 1: Movie Selection -->
<div id="ticketStep1" class="ticket-step">
    <h3 class="text-2xl font-bold text-white mb-6 text-center">
        <i class="fas fa-film mr-2 text-yellow-400"></i>Select a Movie
    </h3>
    
    <!-- City Filter -->
    <div class="max-w-md mx-auto mb-6">
        <label class="block text-white text-sm font-medium mb-2 text-center">
            <i class="fas fa-map-marker-alt mr-2 text-green-400"></i>Select a City (Optional)
        </label>
        <select id="movieCityFilter" onchange="window.movieSelection.filterByCity(this.value)"
            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:bg-white/20 focus:border-green-400 transition-all"
            style="color: white; background-color: rgba(255, 255, 255, 0.1);">
            <option value="" style="background-color: #1f2937; color: white;">All Cities</option>
            <!-- Cities will be loaded here -->
        </select>
    </div>

    <!-- Search Box -->
    <div class="max-w-md mx-auto mb-6">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input type="text" 
                   id="movieSearchInput" 
                   placeholder="Search by title or genre..." 
                   class="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-300 focus:bg-white/20 focus:border-yellow-400 transition-all"
                   oninput="window.movieSelection.handleSearch(this.value)">
        </div>
    </div>
    
    <!-- Loading State -->
    <div id="movieLoadingState" class="text-center py-12">
        <div class="loading w-12 h-12 border-4 border-yellow-400 border-t-transparent rounded-full mx-auto mb-4"></div>
        <p class="text-white">Loading movies...</p>
    </div>
    
    <!-- Movies Grid -->
    <div id="ticketMovieGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 hidden">
        <!-- Movies will be loaded here via JavaScript -->
    </div>
    
    <!-- Empty State -->
    <div id="movieEmptyState" class="text-center py-12 hidden">
        <div class="w-24 h-24 bg-gray-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-film text-gray-400 text-3xl"></i>
        </div>
        <h4 class="text-xl font-bold text-white mb-2">No Movies Found</h4>
        <p class="text-gray-400 mb-4">We couldn't find any movies that match your filters.</p>
        <button onclick="window.movieSelection.clearFilters()" 
                class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg font-medium">
            <i class="fas fa-refresh mr-2"></i>Clear Filters
        </button>
    </div>
</div>

<script>
// Movie Selection JavaScript
class MovieSelection {
    constructor() {
        this.movies = [];
        this.filteredMovies = [];
        this.currentGenre = '';
        this.currentSearch = '';
        this.currentCityId = '';
        this.loadingElement = document.getElementById('movieLoadingState');
        this.gridElement = document.getElementById('ticketMovieGrid');
        this.emptyElement = document.getElementById('movieEmptyState');
        this.cityFilterElement = document.getElementById('movieCityFilter');
    }

    async loadCities() {
        try {
            const response = await axios.get('/api/cities');
            const cities = response.data.data || [];
            
            if (this.cityFilterElement) {
                let html = '<option value="" style="background-color: #1f2937; color: white;">All Cities</option>';
                cities.forEach(city => {
                    html += `<option value="${city.id}" style="background-color: #1f2937; color: white;">${city.name}</option>`;
                });
                this.cityFilterElement.innerHTML = html;
            }
        } catch (error) {
            console.error('Cities failed to load:', error);
        }
    }

    async loadMovies(cityId = '') {
        try {
            this.showLoading();
            
            let url = '/api/movies?per_page=100';
            // Append city filter when provided
            if (cityId && cityId !== '' && cityId !== '0') {
                url += `&city_id=${cityId}`;
                console.log('MovieSelection - City filter enabled:', cityId);
            } else {
                console.log('MovieSelection - No city filter, loading every movie');
            }
            
            console.log('MovieSelection - API URL:', url);
            const response = await axios.get(url);
            console.log('MovieSelection - API Response:', response.data);
            
            this.movies = response.data.data.data || response.data.data;
            this.filteredMovies = [...this.movies];
            console.log('MovieSelection - Movie count:', this.movies.length);
            
            if (this.movies.length === 0) {
                this.showEmpty();
            } else {
                this.renderMovies(this.movies.slice(0, 100)); // Show entire 100-movie batch
                this.showGrid();
            }
            
            // Filmler yüklendikten sonra callback çağır
            if (this.onMoviesLoaded) {
                this.onMoviesLoaded();
            }
            
            // Global event dispatch
            window.dispatchEvent(new CustomEvent('moviesLoaded'));
            
        } catch (error) {
            console.error('Movies could not be loaded:', error);
            this.renderMockMovies();
            this.showGrid();
            
            // Hata durumunda da callback çağır
            if (this.onMoviesLoaded) {
                this.onMoviesLoaded();
            }
            window.dispatchEvent(new CustomEvent('moviesLoaded'));
        }
    }

    filterByCity(cityId) {
        this.currentCityId = cityId;
        this.loadMovies(cityId);
    }

    // Search functionality
    handleSearch(searchTerm) {
        this.currentSearch = searchTerm.toLowerCase();
        this.applyFilters();
    }

    // Genre filter functionality
    filterByGenre(genre) {
        this.currentGenre = genre;
        this.applyFilters();
        this.updateGenreButtons(genre);
    }

    applyFilters() {
        let filtered = [...this.movies];

        // Apply genre filter
        if (this.currentGenre) {
            filtered = filtered.filter(movie => 
                movie.genre.toLowerCase().includes(this.currentGenre.toLowerCase())
            );
        }

        // Apply search filter
        if (this.currentSearch) {
            filtered = filtered.filter(movie => 
                movie.title.toLowerCase().includes(this.currentSearch) ||
                movie.genre.toLowerCase().includes(this.currentSearch)
            );
        }

        this.filteredMovies = filtered;

        if (filtered.length === 0) {
            this.showEmpty();
        } else {
            this.renderMovies(filtered);
            this.showGrid();
        }
    }

    updateGenreButtons(selectedGenre) {
        const buttons = document.querySelectorAll('.genre-filter');
        buttons.forEach(button => {
            button.classList.remove('active', 'bg-yellow-500/20', 'text-yellow-300');
            button.classList.add('bg-white/10', 'text-gray-300');
            
            const buttonGenre = button.textContent.trim();
            if ((selectedGenre === '' && buttonGenre === 'All') || 
                buttonGenre === selectedGenre) {
                button.classList.add('active', 'bg-yellow-500/20', 'text-yellow-300');
                button.classList.remove('bg-white/10', 'text-gray-300');
            }
        });
    }

    clearFilters() {
        this.currentGenre = '';
        this.currentSearch = '';
        this.currentCityId = '';
        document.getElementById('movieSearchInput').value = '';
        if (this.cityFilterElement) {
            this.cityFilterElement.value = '';
        }
        this.updateGenreButtons('');
        this.loadMovies(); // Reload every movie
    }

    renderMovies(movies) {
        let html = '';
        
        movies.forEach(movie => {
            const posterUrl = movie.poster_url && movie.poster_url.trim() !== '' ? movie.poster_url : null;
            
            html += `
                <div class="glass-effect rounded-2xl p-6 card-hover cursor-pointer" 
                     onclick="selectMovieForTicket(${movie.id}, '${movie.title}')">
                    <div class="h-32 bg-gradient-to-br from-purple-600 to-blue-600 rounded-xl flex items-center justify-center relative overflow-hidden mb-4">
                        ${posterUrl ? `
                            <img src="${posterUrl}" alt="${movie.title}" 
                                 class="w-full h-full object-cover rounded-xl"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="hidden w-full h-full bg-gradient-to-br from-purple-600 to-blue-600 rounded-xl flex items-center justify-center">
                                <i class="fas fa-film text-white text-3xl opacity-50"></i>
                            </div>
                        ` : `
                            <i class="fas fa-film text-white text-3xl opacity-50"></i>
                        `}
                        <div class="absolute inset-0 bg-black bg-opacity-20 rounded-xl"></div>
                    </div>
                    <h4 class="text-lg font-bold text-white mb-2">${movie.title}</h4>
                    <p class="text-purple-300 text-sm">${movie.genre} • ${movie.duration} min</p>
                    <p class="text-yellow-400 mt-2">
                        <i class="fas fa-star mr-1"></i>${movie.imdb_raiting || movie.imdb_rating || 'N/A'}
                    </p>
                </div>
            `;
        });
        
        this.gridElement.innerHTML = html;
    }

    renderMockMovies() {
        const mockMovies = [
            { id: 1, title: "Avatar: The Way of Water", genre: "Sci-Fi", duration: 192, imdb_raiting: 7.6 },
            { id: 2, title: "Top Gun: Maverick", genre: "Action", duration: 131, imdb_raiting: 8.3 },
            { id: 3, title: "Black Panther: Wakanda Forever", genre: "Action", duration: 161, imdb_raiting: 6.7 },
            { id: 4, title: "Spider-Man: Across the Spider-Verse", genre: "Animation", duration: 140, imdb_raiting: 8.7 },
            { id: 5, title: "John Wick: Chapter 4", genre: "Action", duration: 169, imdb_raiting: 7.8 },
            { id: 6, title: "Guardians of the Galaxy Vol. 3", genre: "Sci-Fi", duration: 150, imdb_raiting: 7.9 }
        ];
        this.renderMovies(mockMovies);
    }

    showLoading() {
        this.loadingElement.classList.remove('hidden');
        this.gridElement.classList.add('hidden');
        this.emptyElement.classList.add('hidden');
    }

    showGrid() {
        this.loadingElement.classList.add('hidden');
        this.gridElement.classList.remove('hidden');
        this.emptyElement.classList.add('hidden');
    }

    showEmpty() {
        this.loadingElement.classList.add('hidden');
        this.gridElement.classList.add('hidden');
        this.emptyElement.classList.remove('hidden');
    }

    // Search/Filter methods (updated)
    filterMovies(searchTerm) {
        console.warn('filterMovies is deprecated, use handleSearch instead');
        this.handleSearch(searchTerm);
    }

    // Get current filtered movies
    getFilteredMovies() {
        return this.filteredMovies;
    }

    // Get all available genres
    getAvailableGenres() {
        const genres = [...new Set(this.movies.map(movie => movie.genre))];
        return genres.sort();
    }

    // Quick search by pressing Enter
    setupKeyboardShortcuts() {
        document.getElementById('movieSearchInput').addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const firstMovie = this.filteredMovies[0];
                if (firstMovie) {
                    selectMovieForTicket(firstMovie.id, firstMovie.title);
                }
            }
        });
    }
}

// Initialize movie selection when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // URL'den film ID'sini kontrol et
    const urlParams = new URLSearchParams(window.location.search);
    const movieId = urlParams.get('movie');
    
    window.movieSelection = new MovieSelection();
    window.movieSelection.loadCities();
    
    // Eğer URL'de film ID yoksa filmleri yükle (normal akış)
    // Eğer URL'de film ID varsa filmleri yükleme (otomatik seçim yapılacak)
    if (!movieId) {
        window.movieSelection.loadMovies();
    } else {
        // Film ID varsa filmleri yükle ama step 1'i gösterme
    window.movieSelection.loadMovies();
    }
    
    // Setup keyboard shortcuts
    window.movieSelection.setupKeyboardShortcuts();
});
</script>