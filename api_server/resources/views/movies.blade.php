@extends('layout')

@section('content')
<div class="glass-effect p-8 rounded-2xl mb-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-3xl font-bold text-white flex items-center">
            <i class="fas fa-play mr-3 text-green-400"></i>
            Movie List
        </h2>
        <a href="/" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>
    
    <div class="mb-8">
        <!-- Search and City Filter -->
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" id="movieSearch" placeholder="Search by movie name..." 
                       onkeypress="if(event.key === 'Enter') searchMovies()"
                       class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-300 focus:bg-white/20 focus:border-green-400 transition-all">
            </div>
            <div class="w-full md:w-64">
                <select id="cityFilter" onchange="filterByCity(this.value)"
                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:bg-white/20 focus:border-green-400 transition-all"
                        style="color: white; background-color: rgba(255, 255, 255, 0.1);">
                    <option value="" style="background-color: #1f2937; color: white;">All Cities</option>
                    <!-- Cities will be loaded here -->
                </select>
            </div>
            <button onclick="searchMovies()" class="bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white px-8 py-3 rounded-xl font-semibold transition-all">
                <i class="fas fa-search mr-2"></i>Search
            </button>
        </div>
    </div>
</div>

<div id="movieGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    <!-- Movies will be loaded here -->
</div>

<script>
let currentSearch = '';
let currentCityId = '';

document.addEventListener('DOMContentLoaded', function() {
    loadCities();
    loadMovies();
});

async function loadCities() {
    try {
        const response = await axios.get('/api/cities');
        const cities = response.data.data || [];
        
        const cityFilter = document.getElementById('cityFilter');
        if (cityFilter) {
            let html = '<option value="" style="background-color: #1f2937; color: white;">All Cities</option>';
            cities.forEach(city => {
                html += `<option value="${city.id}" style="background-color: #1f2937; color: white;">${city.name}</option>`;
            });
            cityFilter.innerHTML = html;
        }
    } catch (error) {
        console.error('Cities could not be loaded:', error);
    }
}

function filterByCity(cityId) {
    currentCityId = cityId;
    loadMovies(currentSearch, cityId);
}

async function loadMovies(search = '', cityId = '') {
    try {
        // Use cityId from parameter or current state
        const selectedCityId = cityId || currentCityId || '';
        
        // Arama varsa normal API'leri kullan
        if (search) {
            let urlNowShowing = '/api/movies';
            let urlComingSoon = '/api/future-movies';
            let params = `?search=${encodeURIComponent(search)}`;
            
            // Add city filter if selected
            if (selectedCityId && selectedCityId !== '') {
                params += `&city_id=${selectedCityId}`;
            }
            
            urlNowShowing += params;
            urlComingSoon += params;
            
            const [nowShowingResponse, comingSoonResponse] = await Promise.all([
                axios.get(urlNowShowing).catch(() => ({ data: { data: { data: [] } } })),
                axios.get(urlComingSoon).catch(() => ({ data: { data: { data: [] } } }))
            ]);
            
            const nowShowingMovies = nowShowingResponse.data.data.data || nowShowingResponse.data.data || [];
            const comingSoonMovies = comingSoonResponse.data.data.data || comingSoonResponse.data.data || [];
            
            renderMoviesByCategory(nowShowingMovies, comingSoonMovies);
            return;
        }
        
        // If there is no search, use the distributed endpoint - distributes 100 movies by date
        let url = '/api/movies/distributed';
        
        // Add city filter if selected
        if (selectedCityId && selectedCityId !== '') {
            url += `?city_id=${selectedCityId}`;
        }
        
        console.log('Movies - Distributed API çağrısı:', url);
        
        const response = await axios.get(url).catch(() => ({ 
            data: { 
                data: { 
                    now_showing: { data: [] }, 
                    coming_soon: { data: [] } 
                } 
            } 
        }));
        
        const nowShowingMovies = response.data.data.now_showing?.data || [];
        const comingSoonMovies = response.data.data.coming_soon?.data || [];
        
        console.log('Movies - Now Showing:', nowShowingMovies.length, 'Coming Soon:', comingSoonMovies.length, 'Toplam:', nowShowingMovies.length + comingSoonMovies.length);
        
        renderMoviesByCategory(nowShowingMovies, comingSoonMovies);
    } catch (error) {
        console.error('Movies could not be loaded:', error);
        console.error('Error details:', error.response?.data);
        renderMoviesByCategory([], []);
    }
}

function isNowShowingDate(releaseDate) {
    if (!releaseDate) return true;
    
    try {
        let dateParts;
        if (releaseDate.includes('-')) {
            dateParts = releaseDate.split('-');
            let day, month, year;
            
            if (dateParts[0].length === 4) {
                year = parseInt(dateParts[0]);
                month = parseInt(dateParts[1]);
                day = parseInt(dateParts[2]);
            } else {
                day = parseInt(dateParts[0]);
                month = parseInt(dateParts[1]);
                year = parseInt(dateParts[2]);
            }
            
            const movieDate = new Date(year, month - 1, day);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            return movieDate <= today;
        }
    } catch (e) {
        console.error('Date parse error:', e, releaseDate);
    }
    
    return true;
}

function renderMoviesByCategory(nowShowingMovies, comingSoonMovies) {
    const movieGrid = document.getElementById('movieGrid');
    let html = '';
    
    // Now Showing section
    if (nowShowingMovies.length > 0) {
        html += `
            <div class="col-span-full mb-8">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-play text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white">Now Showing</h3>
                </div>
            </div>
        `;
        
        nowShowingMovies.forEach(movie => {
            html += renderMovieCard(movie, true);
        });
    }
    
    // Coming Soon section
    if (comingSoonMovies.length > 0) {
        html += `
            <div class="col-span-full mb-8 mt-8">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-calendar-alt text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white">Coming Soon</h3>
                </div>
            </div>
        `;
        
        comingSoonMovies.forEach(movie => {
            html += renderMovieCard(movie, false);
        });
    }
    
    movieGrid.innerHTML = html || '<p class="text-white text-center col-span-full">No movies found.</p>';
}

function renderMovieCard(movie, isNowShowing) {
    const posterUrl = movie.poster_url && movie.poster_url.trim() !== '' ? movie.poster_url : null;
    
    return `
        <div class="glass-effect rounded-2xl overflow-hidden card-hover">
            <div class="h-64 bg-gradient-to-br ${isNowShowing ? 'from-green-600 to-emerald-600' : 'from-blue-600 to-blue-800'} flex items-center justify-center relative overflow-hidden">
                ${posterUrl ? `
                    <img src="${posterUrl}" alt="${movie.title}" 
                         class="w-full h-full object-cover"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="hidden w-full h-full bg-gradient-to-br from-purple-600 to-blue-600 flex items-center justify-center">
                        <i class="fas fa-film text-white text-6xl opacity-50"></i>
                    </div>
                ` : `
                    <i class="fas fa-film text-white text-6xl opacity-50"></i>
                `}
                <div class="absolute inset-0 bg-black bg-opacity-20"></div>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold text-white mb-2">${movie.title}</h3>
                <p class="text-purple-300 text-sm mb-2">${movie.genre} • ${movie.duration} dk</p>
                <p class="text-yellow-400 mb-4">
                    <i class="fas fa-star mr-1"></i>${movie.imdb_raiting || movie.imdb_rating || 'N/A'}
                </p>
                <button onclick="showMovieDetails(${movie.id}, ${isNowShowing})" class="w-full bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white py-2 rounded-xl font-semibold transition-all">
                    <i class="fas fa-info-circle mr-2"></i>Details
                </button>
            </div>
        </div>
    `;
}

function searchMovies() {
    currentSearch = document.getElementById('movieSearch').value;
    loadMovies(currentSearch, currentCityId);
}

async function showMovieDetails(movieId, isNowShowing = true) {
    try {
        let response;
        try {
            response = await axios.get(`/api/movies/${movieId}`);
        } catch (e) {
            if (e.response?.status === 404) {
                response = await axios.get(`/api/future-movies/${movieId}`);
                isNowShowing = false;
            } else {
                throw e;
            }
        }
        
        const movie = response.data.data;
        
        // Determine isNowShowing by movie release date
        if (isNowShowing === undefined || isNowShowing === null) {
            isNowShowing = isNowShowingDate(movie.release_date);
        }
        
        // Create modal
        createMovieDetailModal(movie, isNowShowing);
    } catch (error) {
        console.error('Failed to load movie details:', error);
        alert('Failed to load movie details!');
    }
}

function createMovieDetailModal(movie, isNowShowing = true) {
    // Remove existing modal if any
    const existingModal = document.getElementById('movieDetailModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Poster URL check
    const posterUrl = movie.poster_url && movie.poster_url.trim() !== '' ? movie.poster_url : null;
    
    // Check user login state
    const isLoggedIn = window.userPermissions && window.userPermissions.isLoggedIn;
    
    // "Buy Ticket" button - only for logged-in users and Now Showing
    let buyTicketButton = '';
    if (!isNowShowing) {
        buyTicketButton = `
            <button disabled
                    class="bg-gray-500 text-white px-6 py-3 rounded-lg font-semibold cursor-not-allowed flex items-center opacity-50">
                <i class="fas fa-calendar-alt mr-2"></i>Coming Soon
            </button>
        `;
    } else if (isLoggedIn) {
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
                            ${!isNowShowing ? `
                                <div class="mt-4 bg-blue-500/20 border border-blue-500 rounded-lg p-3 text-center">
                                    <span class="text-blue-300 font-semibold">
                                        <i class="fas fa-calendar-alt mr-2"></i>Coming Soon
                                    </span>
                                </div>
                            ` : ''}
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
                            
                            ${!isLoggedIn && isNowShowing ? `
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

// Search on Enter key
document.getElementById('movieSearch')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        searchMovies();
    }
});

// Load movies on page load
document.addEventListener('DOMContentLoaded', function() {
    loadMovies();
});
</script>
@endsection