<!-- Step 2: Cinema Selection -->
<div id="ticketStep2" class="ticket-step hidden">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-2xl font-bold text-white text-center flex-1">
            <i class="fas fa-building mr-2 text-green-400"></i>Select a Cinema
        </h3>
        <button onclick="goBackToStep(1)" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Change Movie
        </button>
    </div>

    <!-- Selected Movie Info -->
    <div id="selectedMovieInfo" class="bg-white/10 p-4 rounded-xl mb-6">
        <!-- Selected movie info will be shown here -->
    </div>

    <!-- City Filter -->
    <div class="mb-6">
        <div class="max-w-md mx-auto">
            <label class="block text-white text-sm font-medium mb-2">
                <i class="fas fa-map-marker-alt mr-1"></i>City Filter (Optional)
            </label>
            <select id="cityFilter" onchange="window.cinemaSelection.filterByCity(this.value)"
                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:bg-white/20 focus:border-green-400 transition-all"
                style="color: white; background-color: rgba(255, 255, 255, 0.1);">
                <option value="" style="background-color: #1f2937; color: white;">All Cities</option>
                <!-- Cities will be loaded here -->
            </select>
        </div>
    </div>
    
    <!-- Cinema Count Info -->
    <div id="cinemaCountInfo" class="text-center mb-4">
        <span class="text-green-300 text-sm">
            <i class="fas fa-info-circle mr-1"></i>
            <span id="filteredCinemaCount">0</span> cinemas found
        </span>
    </div>

    <!-- Loading State -->
    <div id="cinemaLoadingState" class="text-center py-12 hidden">
        <div class="loading w-12 h-12 border-4 border-green-400 border-t-transparent rounded-full mx-auto mb-4"></div>
        <p class="text-white">Loading cinemas...</p>
    </div>

    <!-- Cinemas Grid -->
    <div id="cinemaGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Cinema selection will be loaded here -->
    </div>

    <!-- Empty State -->
    <div id="cinemaEmptyState" class="text-center py-12 hidden">
        <div class="w-24 h-24 bg-gray-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-building text-gray-400 text-3xl"></i>
        </div>
        <h4 class="text-xl font-bold text-white mb-2">No Cinemas Found</h4>
        <p class="text-gray-400">There are no cinemas for this movie in the selected city.</p>
        <button onclick="window.cinemaSelection.clearFilters()"
            class="mt-4 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-medium">
            <i class="fas fa-refresh mr-2"></i>Show All Cinemas
        </button>
    </div>
</div>

<script>
// Cinema Selection JavaScript
class CinemaSelection {
    constructor() {
        this.cinemas = [];
        this.filteredCinemas = [];
        this.availableCities = [];
        this.selectedMovie = null;
        this.currentCityFilter = '';
        
        // DOM Elements
        this.loadingElement = document.getElementById('cinemaLoadingState');
        this.gridElement = document.getElementById('cinemaGrid');
        this.emptyElement = document.getElementById('cinemaEmptyState');
        this.movieInfoElement = document.getElementById('selectedMovieInfo');
        this.cityFilterElement = document.getElementById('cityFilter');
        this.countInfoElement = document.getElementById('filteredCinemaCount');
    }

    async loadCinemas(preferredCityId = '') {
        try {
            this.showLoading();
            
            if (!this.selectedMovie || !this.selectedMovie.id) {
                throw new Error('No movie selected');
            }

            // Mevcut CinemaController metodunu kullan
            const response = await axios.get(`/api/cinemas/showing/${this.selectedMovie.id}`);
            const data = response.data;
            
            this.cinemas = data.data || [];
            
            // Extract unique cities from cinema payload
            this.availableCities = [];
            const cityMap = new Map();
            
            this.cinemas.forEach(cinema => {
                if (cinema.city && cinema.city.id && cinema.city.name) {
                    // Avoid duplicates with the same ID
                    if (!cityMap.has(cinema.city.id)) {
                        cityMap.set(cinema.city.id, {
                            id: cinema.city.id,
                            name: cinema.city.name
                        });
                    }
                }
            });
            
            // Convert map back to array
            this.availableCities = Array.from(cityMap.values());
            
            this.filteredCinemas = [...this.cinemas];
            
            this.renderCityFilter();
            this.updateCinemaCount();

            const effectiveCityId = preferredCityId || this.currentCityFilter || '';
            
            // Debug info
            console.log('Loaded cinemas:', this.cinemas.length);
            console.log('Detected cities:', this.availableCities);
            
            // Hide loading state and show content
            if (this.cinemas.length === 0) {
                this.showEmpty();
            } else if (effectiveCityId) {
                this.applyCityFilter(effectiveCityId, { 
                    skipLoading: true, 
                    updateDropdown: true 
                });
            } else {
                // Render cinemas first
                this.filteredCinemas = [...this.cinemas];
                this.renderCinemas(this.cinemas);
                // Then reveal the grid (hides loading)
                this.showGrid();
            }
            
        } catch (error) {
            console.error('Cinema loading error:', error);
            console.log('Loading mock data:', error.message);
            this.renderMockCinemas();
            this.showGrid();
        }
    }

    renderCityFilter() {
        let html = '<option value="" style="background-color: #1f2937; color: white;">All Cities</option>';
        
        if (this.availableCities && this.availableCities.length > 0) {
            this.availableCities.forEach(city => {
                if (city && city.id && city.name) {
                    html += `<option value="${city.id}" style="background-color: #1f2937; color: white;">${city.name}</option>`;
                }
            });
        }
        
        this.cityFilterElement.innerHTML = html;
        
        // Warn when no city info is available
        if (this.availableCities.length === 0) {
            console.warn('No city info was returned. Cinema-city relations might be missing.');
        }
    }

    filterByCity(cityId) {
        this.applyCityFilter(cityId);
    }

    applyCityFilter(cityId, options = {}) {
        const { skipLoading = false, updateDropdown = false } = options;

        try {
            if (!skipLoading) {
                this.showLoading();
            }
            
            this.currentCityFilter = cityId || '';

            if (updateDropdown && this.cityFilterElement) {
                this.cityFilterElement.value = this.currentCityFilter;
            }
            
            if (!this.currentCityFilter) {
                this.filteredCinemas = [...this.cinemas];
            } else {
                this.filteredCinemas = this.cinemas.filter(cinema => {
                    if (!cinema.city) return false;
                    return cinema.city.id == this.currentCityFilter || cinema.city_id == this.currentCityFilter;
                });
            }
            
            this.updateCinemaCount();

            const renderFiltered = () => {
                if (this.filteredCinemas.length === 0) {
                    this.showEmpty();
                } else {
                    this.renderCinemas(this.filteredCinemas);
                    this.showGrid();

                    setTimeout(() => {
                        if (this.gridElement && this.gridElement.classList.contains('hidden')) {
                            console.warn('Grid still hidden, forcing it visible...');
                            this.showGrid();
                        }
                    }, 50);
                }
            };

            if (skipLoading) {
                renderFiltered();
            } else {
                setTimeout(renderFiltered, 50);
            }
        } catch (error) {
            console.error('City filtering error:', error);
            this.filteredCinemas = [...this.cinemas];
            this.updateCinemaCount();
            this.renderCinemas(this.filteredCinemas);
            this.showGrid();
        }
    }

    clearFilters() {
        try {
            this.currentCityFilter = '';
            if (this.cityFilterElement) {
                this.cityFilterElement.value = '';
            }
            this.filteredCinemas = [...this.cinemas];
            this.updateCinemaCount();
            this.renderCinemas(this.filteredCinemas);
            this.showGrid();
        } catch (error) {
            console.error('Failed to reset filters:', error);
        }
    }

    updateCinemaCount() {
        this.countInfoElement.textContent = this.filteredCinemas.length;
    }

    renderCinemas(cinemas) {
        if (!this.gridElement) {
            console.error('Cinema grid element not found!');
            return;
        }
        
        if (!cinemas || cinemas.length === 0) {
            this.gridElement.innerHTML = '';
            return;
        }
        
        let html = '';
        
        try {
            cinemas.forEach(cinema => {
                // Calculate showtime count
                const showtimeCount = cinema.halls ? 
                    cinema.halls.reduce((total, hall) => total + (hall.showtimes ? hall.showtimes.length : 0), 0) : 0;
                
                // Escape strings for safety
                const cinemaName = (cinema.name || 'Unnamed Cinema').replace(/'/g, "\\'").replace(/"/g, '&quot;');
                const cinemaAddress = (cinema.address || 'No address on file').replace(/'/g, "\\'").replace(/"/g, '&quot;');
                const cityName = cinema.city ? (cinema.city.name || 'City unavailable') : 'City unavailable';
                
                html += `
                    <div class="glass-effect rounded-2xl p-6 card-hover cursor-pointer" 
                         onclick="selectCinemaForTicket(${cinema.id}, '${cinemaName}', '${cinemaAddress}')">
                        <div class="h-20 bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl flex items-center justify-center mb-4">
                            <i class="fas fa-building text-white text-3xl opacity-70"></i>
                        </div>
                        <h4 class="text-lg font-bold text-white mb-2">${cinemaName}</h4>
                        <p class="text-blue-300 text-sm mb-1">
                            <i class="fas fa-map-marker-alt mr-1"></i>${cityName}
                        </p>
                        <p class="text-gray-400 text-xs mb-2">
                            ${cinemaAddress}
                        </p>
                        <p class="text-emerald-400 text-sm">
                            <i class="fas fa-door-open mr-1"></i>${cinema.halls ? cinema.halls.length : 'N/A'} Auditoriums
                        </p>
                        <p class="text-yellow-400 text-sm">
                            <i class="fas fa-clock mr-1"></i>${showtimeCount} Showtimes
                        </p>
                        ${cinema.phone ? `
                            <p class="text-gray-400 text-xs mt-1">
                                <i class="fas fa-phone mr-1"></i>${cinema.phone}
                            </p>
                        ` : ''}
                    </div>
                `;
            });
            
            this.gridElement.innerHTML = html;
            
            // Ensure the grid becomes visible
            this.gridElement.style.display = 'grid';
            this.gridElement.style.opacity = '1';
            this.gridElement.style.visibility = 'visible';
            
        } catch (error) {
            console.error('Cinema render error:', error);
            this.gridElement.innerHTML = '<div class="text-white text-center p-4">An error occurred while loading cinemas.</div>';
        }
    }

    renderMockCinemas() {
        const mockCinemas = [
            { 
                id: 1, 
                name: "Cinema Automation Gaziantep", 
                address: "Forum AVM, Şehitkamil",
                city: { id: 1, name: "Gaziantep" },
                halls: [{ showtimes: [1, 2, 3] }, { showtimes: [4, 5] }],
                phone: "0342 123 45 67"
            },
            { 
                id: 2, 
                name: "Cinema Automation Ankara", 
                address: "Ankamall AVM",
                city: { id: 2, name: "Ankara" },
                halls: [{ showtimes: [1, 2] }, { showtimes: [3, 4] }],
                phone: "0312 234 56 78"
            },
            { 
                id: 3, 
                name: "CineBonus İstanbul", 
                address: "İstinyePark AVM",
                city: { id: 3, name: "İstanbul" },
                halls: [{ showtimes: [1, 2, 3, 4] }],
                phone: "0212 345 67 89"
            },
            { 
                id: 4, 
                name: "Cinema Automation Adana", 
                address: "M1 AVM",
                city: { id: 4, name: "Adana" },
                halls: [{ showtimes: [1, 2] }],
                phone: "0322 456 78 90"
            }
        ];
        
        this.cinemas = mockCinemas;
        this.filteredCinemas = [...mockCinemas];
        this.availableCities = [
            { id: 1, name: "Gaziantep" },
            { id: 2, name: "Ankara" },
            { id: 3, name: "İstanbul" },
            { id: 4, name: "Adana" }
        ];
        
        this.renderCityFilter();
        this.updateCinemaCount();
        this.renderCinemas(mockCinemas);
    }

    showSelectedMovie(movie) {
        this.selectedMovie = movie;
        this.movieInfoElement.innerHTML = `
            <div class="flex items-center space-x-4">
                <i class="fas fa-film text-yellow-400 text-2xl"></i>
                <div>
                    <h4 class="text-white font-semibold">Selected Movie</h4>
                    <p class="text-purple-300">${movie.title}</p>
                </div>
                <div class="ml-auto">
                    <span class="px-3 py-1 bg-yellow-500/20 text-yellow-300 rounded-full text-sm">
                        <i class="fas fa-check mr-1"></i>Chosen
                    </span>
                </div>
            </div>
        `;
    }

    showLoading() {
        if (this.loadingElement) {
            this.loadingElement.classList.remove('hidden');
        }
        if (this.gridElement) {
            this.gridElement.classList.add('hidden');
        }
        if (this.emptyElement) {
            this.emptyElement.classList.add('hidden');
        }
    }

    showGrid() {
        if (this.loadingElement) {
            this.loadingElement.classList.add('hidden');
            this.loadingElement.style.display = 'none';
        }
        if (this.gridElement) {
            this.gridElement.classList.remove('hidden');
            // Ensure the grid is visible
            this.gridElement.style.display = 'grid';
            this.gridElement.style.opacity = '1';
            this.gridElement.style.visibility = 'visible';
            this.gridElement.style.minHeight = '200px';
        }
        if (this.emptyElement) {
            this.emptyElement.classList.add('hidden');
            this.emptyElement.style.display = 'none';
        }
    }

    showEmpty() {
        if (this.loadingElement) {
            this.loadingElement.classList.add('hidden');
        }
        if (this.gridElement) {
            this.gridElement.classList.add('hidden');
        }
        if (this.emptyElement) {
            this.emptyElement.classList.remove('hidden');
        }
    }

    // Get cinema by ID
    getCinemaById(id) {
        return this.cinemas.find(cinema => cinema.id == id);
    }

    // Get filtered cinemas
    getFilteredCinemas() {
        return this.filteredCinemas;
    }

    // Reset selection
    reset() {
        this.cinemas = [];
        this.filteredCinemas = [];
        this.availableCities = [];
        this.selectedMovie = null;
        this.currentCityFilter = '';
        this.cityFilterElement.value = '';
        this.updateCinemaCount();
    }
}

// Initialize cinema selection
document.addEventListener('DOMContentLoaded', function() {
    window.cinemaSelection = new CinemaSelection();
});

// Global function for movie selection (called from movie component)
async function selectMovieForTicket(movieId, movieTitle) {
    selectedMovie = { id: movieId, title: movieTitle };
    currentTicketStep = 2;
    updateTicketSteps();

    // Show selected movie info
    window.cinemaSelection.showSelectedMovie(selectedMovie);
    
    // Load cinemas for this movie (ALL CINEMAS)
    const preferredCityId = window.movieSelection?.currentCityId || '';
    await window.cinemaSelection.loadCinemas(preferredCityId);
}

// Global function for cinema selection
async function selectCinemaForTicket(cinemaId, cinemaName, cinemaAddress) {
    selectedCinema = { 
        id: cinemaId, 
        name: cinemaName, 
        address: cinemaAddress 
    };
    
    currentTicketStep = 3;
    updateTicketSteps();

    // Show selected cinema and movie info
    document.getElementById('selectedMovieCinemaInfo').innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex items-center space-x-3">
                <i class="fas fa-film text-yellow-400 text-lg"></i>
                <div>
                    <h6 class="text-white font-medium text-sm">Selected Movie</h6>
                    <p class="text-purple-300 text-xs">${selectedMovie.title}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <i class="fas fa-building text-blue-400 text-lg"></i>
                <div>
                    <h6 class="text-white font-medium text-sm">Selected Cinema</h6>
                    <p class="text-blue-300 text-xs">${cinemaName}</p>
                </div>
            </div>
        </div>
    `;

    // Load showtimes for this movie and cinema
    await loadShowtimesForCinema();
}
</script>