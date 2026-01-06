@extends('layout')

@section('content')
<div class="glass-effect p-8 rounded-2xl">
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-3xl font-bold text-white flex items-center">
            <i class="fas fa-images mr-3 text-blue-400"></i>
            Poster Yönetimi
        </h2>
        <a href="/admin" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Geri
        </a>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="stat-card p-6 rounded-2xl text-center">
            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-film text-white text-xl"></i>
            </div>
            <div class="text-3xl font-bold text-gray-800 mb-2">{{ $stats['total_movies'] ?? 0 }}</div>
            <div class="text-gray-600 font-medium">Toplam Film</div>
        </div>
        
        <div class="stat-card p-6 rounded-2xl text-center">
            <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-lg flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-white text-xl"></i>
            </div>
            <div class="text-3xl font-bold text-gray-800 mb-2">{{ $stats['with_posters'] ?? 0 }}</div>
            <div class="text-gray-600 font-medium">Poster Mevcut</div>
        </div>
        
        <div class="stat-card p-6 rounded-2xl text-center">
            <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-red-600 rounded-lg flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-times text-white text-xl"></i>
            </div>
            <div class="text-3xl font-bold text-gray-800 mb-2">{{ $stats['without_posters'] ?? 0 }}</div>
            <div class="text-gray-600 font-medium">Poster Eksik</div>
        </div>
    </div>

    <!-- TMDB Bağlantı Testi -->
    <div class="bg-white/10 p-6 rounded-xl mb-8">
        <h3 class="text-xl font-semibold text-white mb-4">
            <i class="fas fa-plug mr-2"></i>TMDB Bağlantı Testi
        </h3>
        <button onclick="testTMDBConnection()" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold transition-all">
            <i class="fas fa-wifi mr-2"></i>Bağlantıyı Test Et
        </button>
        <div id="connectionResult" class="mt-4"></div>
    </div>

    <!-- Toplu Güncelleme -->
    <div class="bg-white/10 p-6 rounded-xl mb-8">
        <h3 class="text-xl font-semibold text-white mb-4">
            <i class="fas fa-download mr-2"></i>Toplu Poster Güncelleme
        </h3>
        <p class="text-gray-300 mb-4">TMDB API kullanarak film posterlerini otomatik olarak güncelleyin.</p>
        
        <div class="flex items-center space-x-4 mb-4">
            <label class="text-white font-medium">Güncelleme Limiti:</label>
            <select id="updateLimit" class="px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white">
                <option value="10">10 Film</option>
                <option value="25">25 Film</option>
                <option value="50" selected>50 Film</option>
                <option value="100">100 Film</option>
                <option value="200">200 Film</option>
            </select>
        </div>
        
        <button onclick="updatePosters()" id="updateBtn" class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-xl font-semibold transition-all">
            <i class="fas fa-sync mr-2"></i>Posterları Güncelle
        </button>
        
        <div id="updateProgress" class="mt-4 hidden">
            <div class="bg-white/10 rounded-lg p-4">
                <div class="flex items-center mb-2">
                    <div class="loading w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></div>
                    <span class="text-white">Güncelleniyor...</span>
                </div>
                <div class="text-sm text-gray-300">Bu işlem birkaç dakika sürebilir. Lütfen bekleyiniz.</div>
            </div>
        </div>
        
        <div id="updateResult" class="mt-4"></div>
    </div>

    <!-- Manuel Test -->
    <div class="bg-white/10 p-6 rounded-xl">
        <h3 class="text-xl font-semibold text-white mb-4">
            <i class="fas fa-search mr-2"></i>Manuel Test
        </h3>
        <p class="text-gray-300 mb-4">Belirli bir film için poster araması test edin.</p>
        
        <div class="flex space-x-4 mb-4">
            <input type="text" id="testMovieTitle" placeholder="Film başlığı girin..." 
                   class="flex-1 px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-300">
            <button onclick="testSingleMovie()" class="bg-purple-500 hover:bg-purple-600 text-white px-6 py-3 rounded-xl font-semibold transition-all">
                <i class="fas fa-search mr-2"></i>Test Et
            </button>
        </div>
        
        <div id="testResult" class="mt-4"></div>
    </div>
</div>

<script>
async function testTMDBConnection() {
    showMessage('connectionResult', 'TMDB bağlantısı test ediliyor...', 'info');
    
    try {
        const response = await axios.get('/api/posters/test-connection');
        
        if (response.data.success) {
            showMessage('connectionResult', '✅ TMDB bağlantısı başarılı!', 'success');
        } else {
            showMessage('connectionResult', '❌ ' + response.data.message, 'error');
        }
    } catch (error) {
        showMessage('connectionResult', '❌ Bağlantı hatası: ' + error.message, 'error');
    }
}

async function updatePosters() {
    const limit = document.getElementById('updateLimit').value;
    const updateBtn = document.getElementById('updateBtn');
    const progressDiv = document.getElementById('updateProgress');
    const resultDiv = document.getElementById('updateResult');
    
    // UI güncellemeleri
    updateBtn.disabled = true;
    updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Güncelleniyor...';
    progressDiv.classList.remove('hidden');
    resultDiv.innerHTML = '';
    
    try {
        const response = await axios.post('/api/posters/update-batch', {
            limit: limit
        });
        
        if (response.data.success) {
            showMessage('updateResult', `✅ ${limit} film için poster güncelleme tamamlandı!`, 'success');
            
            // Sayfayı yenile (istatistikleri güncellemek için)
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showMessage('updateResult', '❌ ' + response.data.message, 'error');
        }
    } catch (error) {
        showMessage('updateResult', '❌ Güncelleme hatası: ' + error.message, 'error');
    } finally {
        // UI'yi geri al
        updateBtn.disabled = false;
        updateBtn.innerHTML = '<i class="fas fa-sync mr-2"></i>Posterları Güncelle';
        progressDiv.classList.add('hidden');
    }
}

async function testSingleMovie() {
    const title = document.getElementById('testMovieTitle').value;
    if (!title) {
        alert('Lütfen bir film başlığı girin!');
        return;
    }
    
    showMessage('testResult', `"${title}" filmi için poster aranıyor...`, 'info');
    
    try {
        // Bu örnek için TMDB API'yi direkt çağıralım
        const tmdbResponse = await axios.get('https://api.themoviedb.org/3/search/movie', {
            params: {
                api_key: 'fd906554dbafae73a755cb63e9a595df',
                query: title,
                language: 'tr-TR'
            }
        });
        
        if (tmdbResponse.data.results && tmdbResponse.data.results.length > 0) {
            const movie = tmdbResponse.data.results[0];
            const posterUrl = movie.poster_path ? `https://image.tmdb.org/t/p/w500${movie.poster_path}` : null;
            
            if (posterUrl) {
                document.getElementById('testResult').innerHTML = `
                    <div class="bg-emerald-500/20 border border-emerald-500/50 rounded-xl p-4">
                        <div class="flex items-start space-x-4">
                            <img src="${posterUrl}" alt="${movie.title}" class="w-24 h-36 object-cover rounded-lg">
                            <div>
                                <h4 class="text-emerald-300 font-semibold text-lg">${movie.title}</h4>
                                <p class="text-emerald-200 text-sm">${movie.release_date || 'Tarih bilinmiyor'}</p>
                                <p class="text-emerald-100 text-sm mt-2">${movie.overview ? movie.overview.substring(0, 100) + '...' : 'Açıklama yok'}</p>
                                <p class="text-emerald-200 text-xs mt-2">Poster URL: ${posterUrl}</p>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                showMessage('testResult', '❌ Film bulundu ama poster yok', 'error');
            }
        } else {
            showMessage('testResult', '❌ Film bulunamadı', 'error');
        }
    } catch (error) {
        showMessage('testResult', '❌ Arama hatası: ' + error.message, 'error');
    }
}

function showMessage(elementId, message, type) {
    const element = document.getElementById(elementId);
    const bgColor = type === 'success' ? 'bg-emerald-500/20 border-emerald-500/50 text-emerald-300' : 
                   type === 'error' ? 'bg-red-500/20 border-red-500/50 text-red-300' :
                   'bg-blue-500/20 border-blue-500/50 text-blue-300';
    
    element.innerHTML = `
        <div class="border rounded-xl p-4 ${bgColor}">
            ${message}
        </div>
    `;
}

// Enter tuşu ile test
document.getElementById('testMovieTitle')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        testSingleMovie();
    }
});
</script>
@endsection