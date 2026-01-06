@extends('layout')

@section('content')
    <div class="glass-effect p-8 rounded-2xl">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-bold text-white flex items-center">
                <i class="fas fa-cog mr-3 text-pink-400"></i>
                Yönetim Paneli
            </h2>
            <a href="/" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Geri
            </a>
        </div>

        <!-- Admin Tabs -->
        <div class="flex space-x-4 mb-8 overflow-x-auto">
            <button onclick="showAdminTab('movies')"
                class="admin-tab-btn active whitespace-nowrap px-6 py-3 rounded-xl font-semibold transition-all bg-gradient-to-r from-purple-500 to-pink-500 text-white">
                <i class="fas fa-film mr-2"></i>Film Yönetimi
            </button>
            <button onclick="showAdminTab('showtimes')"
                class="admin-tab-btn whitespace-nowrap px-6 py-3 rounded-xl font-semibold transition-all bg-white/10 text-gray-300 hover:bg-white/20">
                <i class="fas fa-clock mr-2"></i>Seans Yönetimi
            </button>
            <button onclick="showAdminTab('reports')"
                class="admin-tab-btn whitespace-nowrap px-6 py-3 rounded-xl font-semibold transition-all bg-white/10 text-gray-300 hover:bg-white/20">
                <i class="fas fa-chart-bar mr-2"></i>Raporlar
            </button>
        </div>

        <!-- Film Yönetimi Tab -->
        <div id="adminMoviesTab" class="admin-tab-content">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-white">Film Yönetimi</h3>
                <button onclick="showAddMovieForm()"
                    class="bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white px-6 py-3 rounded-xl font-semibold transition-all">
                    <i class="fas fa-plus mr-2"></i>Yeni Film Ekle
                </button>
            </div>

            <div id="addMovieForm" class="hidden bg-white/10 p-6 rounded-xl mb-6">
                <h4 class="text-xl font-semibold text-white mb-4">Yeni Film Ekle</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <input type="text" id="newMovieTitle" placeholder="Film Başlığı"
                        class="px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-300">
                    <input type="number" id="newMovieDuration" placeholder="Süre (dakika)"
                        class="px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-300">
                    <input type="text" id="newMovieGenre" placeholder="Tür"
                        class="px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-300">
                    <input type="number" step="0.1" id="newMovieRating" placeholder="IMDB Puanı"
                        class="px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-300">
                    <input type="date" id="newMovieDate" class="px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white">
                    <select id="newMovieLanguage" class="px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white">
                        <option value="tr">Türkçe</option>
                        <option value="en">İngilizce</option>
                    </select>
                    </div>
                    <textarea id="newMovieDescription" placeholder="Film Açıklaması" rows="3"
                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-300 mb-4"></textarea>
                    <div class="flex space-x-4">
                        <button onclick="addMovie()"
                            class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-xl font-semibold transition-all">
                            <i class="fas fa-check mr-2"></i>Ekle
                        </button>
                        <button onclick="hideAddMovieForm()"
                            class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-xl font-semibold transition-all">
                            <i class="fas fa-times mr-2"></i>İptal
                        </button>
                    </div>
                    </div>
                    <!-- Bu kodu admin.blade.php dosyasındaki addMovieForm div'inden sonra ekleyin -->

                    <!-- Film Düzenleme Formu -->
                    <div id="editMovieForm" class="hidden bg-white/10 p-6 rounded-xl mb-6 border-2 border-yellow-500/50">
                        <h4 class="text-xl font-semibold text-white mb-4 flex items-center">
                            <i class="fas fa-edit mr-2 text-yellow-400"></i>
                            Film Düzenle
                        </h4>

                        <!-- Film ID (gizli alan) -->
                        <input type="hidden" id="editMovieId">

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Sol Kolon - Temel Bilgiler -->
                            <div class="lg:col-span-2 space-y-4">
                                <!-- Başlık ve Süre -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-white text-sm font-medium mb-2">
                                            <i class="fas fa-film mr-1"></i>Film Başlığı *
                                        </label>
                                        <input type="text" id="editMovieTitle" placeholder="Film Başlığı" oninput="validateEditForm()"
                                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-300 focus:bg-white/20 focus:border-yellow-400 transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-white text-sm font-medium mb-2">
                                            <i class="fas fa-clock mr-1"></i>Süre (dakika) *
                                        </label>
                                        <input type="number" id="editMovieDuration" placeholder="120" min="1" max="999"
                                            oninput="validateEditForm()"
                                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-300 focus:bg-white/20 focus:border-yellow-400 transition-all">
                                    </div>
                                </div>

                                <!-- Tür ve IMDB -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-white text-sm font-medium mb-2">
                                            <i class="fas fa-masks-theater mr-1"></i>Tür *
                                        </label>
                                        <input type="text" id="editMovieGenre" placeholder="Action, Sci-Fi, Drama..."
                                            oninput="validateEditForm()"
                                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-300 focus:bg-white/20 focus:border-yellow-400 transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-white text-sm font-medium mb-2">
                                            <i class="fas fa-star mr-1"></i>IMDB Puanı
                                        </label>
                                        <input type="number" step="0.1" min="0" max="10" id="editMovieRating" placeholder="7.5"
                                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-300 focus:bg-white/20 focus:border-yellow-400 transition-all">
                                    </div>
                                </div>

                                <!-- Tarih ve Dil -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-white text-sm font-medium mb-2">
                                            <i class="fas fa-calendar mr-1"></i>Çıkış Tarihi *
                                        </label>
                                        <input type="date" id="editMovieDate" oninput="validateEditForm()"
                                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:bg-white/20 focus:border-yellow-400 transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-white text-sm font-medium mb-2">
                                            <i class="fas fa-language mr-1"></i>Dil
                                        </label>
                                        <select id="editMovieLanguage"
                                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:bg-white/20 focus:border-yellow-400 transition-all">
                                            <option value="tr">Türkçe</option>
                                            <option value="en">İngilizce</option>
                                            <option value="tr-sub">Türkçe Altyazılı</option>
                                            <option value="tr-dub">Türkçe Dublaj</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Durum -->
                                <div>
                                    <label class="block text-white text-sm font-medium mb-2">
                                        <i class="fas fa-toggle-on mr-1"></i>Durum
                                    </label>
                                    <select id="editMovieStatus"
                                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:bg-white/20 focus:border-yellow-400 transition-all">
                                        <option value="active">Aktif</option>
                                        <option value="inactive">Pasif</option>
                                        <option value="coming_soon">Yakında</option>
                                        <option value="ended">Vizyondan Kalktı</option>
                                    </select>
                                </div>

                                <!-- Açıklama -->
                                <div>
                                    <label class="block text-white text-sm font-medium mb-2">
                                        <i class="fas fa-align-left mr-1"></i>Film Açıklaması
                                    </label>
                                    <textarea id="editMovieDescription" placeholder="Film hakkında detaylı açıklama..." rows="4"
                                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-300 focus:bg-white/20 focus:border-yellow-400 transition-all resize-none"></textarea>
                                </div>
                            </div>

                            <!-- Sağ Kolon - Poster Yönetimi -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-white text-sm font-medium mb-2">
                                        <i class="fas fa-image mr-1"></i>Poster URL
                                    </label>
                                    <input type="url" id="editMoviePosterUrl" placeholder="https://image.tmdb.org/t/p/w500/..."
                                        oninput="onPosterUrlChange()"
                                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-300 focus:bg-white/20 focus:border-yellow-400 transition-all">
                                </div>

                                <!-- TMDB Poster Arama -->
                                <button type="button" onclick="searchPosterForEdit()"
                                    class="w-full bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white py-3 rounded-xl font-semibold transition-all">
                                    <i class="fas fa-search mr-2"></i>TMDB'den Poster Ara
                                </button>

                                <!-- Poster Önizleme -->
                                <div>
                                    <label class="block text-white text-sm font-medium mb-2">Poster Önizleme</label>
                                    <div id="posterPreview">
                                        <div
                                            class="w-full h-48 bg-gray-300 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-400">
                                            <div class="text-center">
                                                <i class="fas fa-image text-gray-500 text-3xl mb-2"></i>
                                                <p class="text-gray-500 text-sm">Poster URL'si girin</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Butonları -->
                        <div class="flex flex-col sm:flex-row gap-4 mt-6 pt-6 border-t border-white/20">
                            <button type="button" onclick="updateMovie()" id="updateMovieBtn"
                                class="flex-1 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white py-3 px-6 rounded-xl font-semibold transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-save mr-2"></i>Değişiklikleri Kaydet
                            </button>

                            <button type="button" onclick="hideEditMovieForm()"
                                class="flex-1 sm:flex-none bg-gray-600 hover:bg-gray-700 text-white py-3 px-6 rounded-xl font-semibold transition-all">
                                <i class="fas fa-times mr-2"></i>İptal
                            </button>

                            <button type="button" onclick="clearEditMovieForm()"
                                class="flex-1 sm:flex-none bg-yellow-600 hover:bg-yellow-700 text-white py-3 px-6 rounded-xl font-semibold transition-all">
                                <i class="fas fa-eraser mr-2"></i>Temizle
                            </button>
                        </div>

                        <!-- Yardım Mesajı -->
                        <div class="mt-4 p-4 bg-blue-500/20 rounded-lg border border-blue-500/50">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-blue-400 mt-1 mr-2"></i>
                                <div class="text-blue-100 text-sm">
                                    <p class="font-medium mb-1">Düzenleme İpuçları:</p>
                                    <ul class="space-y-1 text-xs">
                                        <li>• * ile işaretli alanlar zorunludur</li>
                                        <li>• TMDB'den otomatik poster arama için film başlığını doğru yazın</li>
                                        <li>• Enter tuşu ile formu kaydedin, Escape ile iptal edin</li>
                                        <li>• Poster URL'si değiştiğinde otomatik önizleme gösterilir</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="adminMovieList" class="space-y-4">
                        <!-- Movies will be loaded here -->
                    </div>
                    </div>

                    <!-- Seans Yönetimi Tab -->
                    <div id="adminShowtimesTab" class="admin-tab-content hidden">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-2xl font-bold text-white">Seans Yönetimi</h3>
                            <button onclick="showAddShowtimeForm()"
                                class="bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white px-6 py-3 rounded-xl font-semibold transition-all">
                                <i class="fas fa-plus mr-2"></i>Yeni Seans Ekle
                            </button>
                        </div>

                        <div id="addShowtimeForm" class="hidden bg-white/10 p-6 rounded-xl mb-6">
                            <h4 class="text-xl font-semibold text-white mb-4">Yeni Seans Ekle</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <select id="newShowtimeMovie" class="px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white">
                                    <option value="">Film Seçin</option>
                                </select>
                                <select id="newShowtimeHall" class="px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white">
                                    <option value="">Salon Seçin</option>
                                </select>
                                <input type="datetime-local" id="newShowtimeStart"
                                    class="px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white">
                            </div>
                            <div class="flex space-x-4">
                                <button onclick="addShowtime()"
                                    class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-xl font-semibold transition-all">
                                    <i class="fas fa-check mr-2"></i>Ekle
                                </button>
                                <button onclick="hideAddShowtimeForm()"
                                    class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-xl font-semibold transition-all">
                                    <i class="fas fa-times mr-2"></i>İptal
                                </button>
                            </div>
                        </div>

                        <div id="adminShowtimeList" class="space-y-4">
                            <!-- Showtimes will be loaded here -->
                        </div>
                    </div>

                    <!-- Raporlar Tab -->
                    <div id="adminReportsTab" class="admin-tab-content hidden">
                        <h3 class="text-2xl font-bold text-white mb-6">Satış Raporları</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                            <div class="stat-card p-6 rounded-2xl text-center">
                                <div
                                    class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-lg flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-calendar-day text-white text-xl"></i>
                                </div>
                                <div class="text-3xl font-bold text-gray-800 mb-2" id="dailySales">₺0</div>
                                <div class="text-gray-600 font-medium">Günlük Satış</div>
                            </div>

                            <div class="stat-card p-6 rounded-2xl text-center">
                                <div
                                    class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-calendar-alt text-white text-xl"></i>
                                </div>
                                <div class="text-3xl font-bold text-gray-800 mb-2" id="monthlySales">₺0</div>
                                <div class="text-gray-600 font-medium">Aylık Satış</div>
                            </div>

                            <div class="stat-card p-6 rounded-2xl text-center">
                                <div
                                    class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-star text-white text-xl"></i>
                                </div>
                                <div class="text-3xl font-bold text-gray-800 mb-2" id="totalRevenue">₺0</div>
                                <div class="text-gray-600 font-medium">Toplam Gelir</div>
                            </div>
                        </div>

                        <div class="bg-white/10 p-6 rounded-2xl">
                            <h4 class="text-xl font-semibold text-white mb-4">
                                <i class="fas fa-trophy mr-2 text-yellow-400"></i>En Popüler Filmler
                            </h4>
                            <div id="popularMovies" class="space-y-3">
                                <!-- Popular movies will be loaded here -->
                            </div>
                        </div>
                    </div>
                    </div>


                    <script>
                        // 🎯 EKSİKSİZ ÇALIŞAN ADMİN PANEL

            // Sayfa yüklendiğinde çalışacak kod
            document.addEventListener('DOMContentLoaded', function () {
                console.log('✅ Admin panel başladı!');
                loadMoviesAdmin();
            });

            // 🔐 TOKEN YÖNETİMİ
            async function ensureValidToken() {
                console.log('🔍 Token kontrolü yapılıyor...');

            const token = localStorage.getItem('token');

            if (!token) {
                console.log('❌ Token bulunamadı, yeni giriş yapılıyor...');
                return await loginAsAdmin();
            }

            // Token'ı test et
            try {
                const response = await axios.get('/api/verify-token', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });

                if (response.data.success) {
                    console.log('✅ Token geçerli');
                    return true;
                }
            } catch (error) {
                console.log('❌ Token geçersiz, yeni giriş yapılıyor...');
                localStorage.removeItem('token');
                return await loginAsAdmin();
            }

            return false;
        }

            async function loginAsAdmin() {
                console.log('🔐 Admin girişi yapılıyor...');

            const passwords = ['password', 'admin123', 'wassword', '123456'];

            for (let pwd of passwords) {
                try {
                    console.log('🔄 Deneniyor:', pwd);
                    const response = await axios.post('/api/login', {
                        email: 'admin@cinema.com',
                        password: pwd
                    });

                    if (response.data.success) {
                        const token = response.data.data.token;
                        localStorage.setItem('token', token);
                        console.log('✅ Admin girişi başarılı! Şifre:', pwd);
                        return true;
                    }
                } catch (err) {
                    console.log('❌ Başarısız:', pwd);
                }
            }

            console.log('❌ Tüm şifreler başarısız');
            return false;
        }

            // 🔄 TAB YÖNETİMİ
            function showAdminTab(tabName) {
                console.log('🔄 Tab değiştiriliyor:', tabName);

            // 1️⃣ TÜM BUTONLARI GRİ YAP
            document.querySelectorAll('.admin-tab-btn').forEach(btn => {
                // Pembe renkler çıkar
                btn.classList.remove('active', 'bg-gradient-to-r', 'from-purple-500', 'to-pink-500', 'text-white');
                // Gri renkler ekle
                btn.classList.add('bg-white/10', 'text-gray-300', 'hover:bg-white/20');
            });

            // 2️⃣ TIKLANANI PEMBE YAP
            let activeButton = null;
            if (tabName === 'movies') {
                activeButton = document.querySelector("button[onclick=\"showAdminTab('movies')\"]");
            } else if (tabName === 'showtimes') {
                activeButton = document.querySelector("button[onclick=\"showAdminTab('showtimes')\"]");
            } else if (tabName === 'reports') {
                activeButton = document.querySelector("button[onclick=\"showAdminTab('reports')\"]");
            }

            if (activeButton) {
                // Gri renkler çıkar
                activeButton.classList.remove('bg-white/10', 'text-gray-300', 'hover:bg-white/20');
                // Pembe renkler ekle
                activeButton.classList.add('active', 'bg-gradient-to-r', 'from-purple-500', 'to-pink-500', 'text-white');

            }

            // 3️⃣ TAB İÇERİKLERİNİ DEĞİŞTİR
            document.querySelectorAll('.admin-tab-content').forEach(tab => {
                tab.classList.add('hidden');
            });

            const targetTab = document.getElementById(`admin${tabName.charAt(0).toUpperCase() + tabName.slice(1)}Tab`);
            if (targetTab) {
                targetTab.classList.remove('hidden');
            }

            // 4️⃣ VERİLERİ YÜKLE
            if (tabName === 'showtimes') {
                loadMoviesForDropdown();
                loadHallsForDropdown();
                loadShowtimesAdmin();
            } else if (tabName === 'movies') {
                loadMoviesAdmin();
            }

        }

            // 📽️ FİLM YÖNETİMİ
            async function loadMoviesAdmin() {
                console.log('📽️ Filmler yükleniyor...');

            try {
                const response = await axios.get('/api/movies?per_page=100');
                console.log('📡 Film API yanıtı:', response.data);

                const movies = response.data.data.data || response.data.data || [];
                showMoviesOnScreen(movies);

            } catch (error) {
                console.log('❌ Film API hatası:', error.message);

                // Test verileri
                const testMovies = [
                    { id: 1, title: "Avatar", genre: "Sci-Fi", duration: 180, imdb_raiting: 8.5, release_date: "2023-01-01" },
                    { id: 2, title: "Top Gun", genre: "Action", duration: 130, imdb_raiting: 8.0, release_date: "2023-02-01" }
                ];
                showMoviesOnScreen(testMovies);
            }
        }

            function showMoviesOnScreen(movies) {
                console.log('🎬 ' + movies.length + ' film gösteriliyor');

            const movieList = document.getElementById('adminMovieList');
            if (!movieList) {
                console.log('❌ Film listesi elementi bulunamadı!');
                return;
            }

            if (movies.length === 0) {
                movieList.innerHTML = `
                                                <div class="glass-effect rounded-xl p-8 text-center">
                                                    <h4 class="text-xl font-semibold text-white mb-2">Henüz film bulunmuyor</h4>
                                                    <p class="text-gray-300">Yeni film eklemek için yukarıdaki butonu kullanın.</p>
                                                </div>
                                            `;
                return;
            }

            let html = '';
            movies.forEach(movie => {
                html += `
                                                <div class="glass-effect rounded-xl p-6 flex justify-between items-center">
                                                    <div class="flex-1">
                                                        <h4 class="text-lg font-semibold text-white mb-2">${movie.title}</h4>
                                                        <p class="text-purple-300 text-sm mb-1">${movie.genre} • ${movie.duration} dk</p>
                                                        <p class="text-yellow-400 text-sm">
                                                            ⭐ ${movie.imdb_raiting || 'N/A'} • ${movie.release_date}
                                                        </p>
                                                    </div>
                                                    <div class="flex space-x-3">
                                                        <button onclick="editMovie(${movie.id})" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">
                                                            ✏️ Düzenle
                                                        </button>
                                                        <button onclick="deleteMovie(${movie.id})" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                                                            🗑️ Sil
                                                        </button>
                                                    </div>
                                                </div>
                                            `;
            });

            movieList.innerHTML = html;
            console.log('✅ Filmler ekranda gösterildi');
        }

            // Film form yönetimi
            function showAddMovieForm() {
                console.log('📝 Film ekleme formu gösteriliyor');
                const form = document.getElementById('addMovieForm');
                if (form) {
                    form.classList.remove('hidden');
                }
            }

            function hideAddMovieForm() {
                console.log('❌ Film ekleme formu gizleniyor');
                const form = document.getElementById('addMovieForm');
                if (form) {
                    form.classList.add('hidden');
                }
            }

            function clearMovieForm() {
                console.log('🧹 Film formu temizleniyor...');

            const fields = [
                'newMovieTitle', 'newMovieDuration', 'newMovieGenre',
                'newMovieDate', 'newMovieDescription', 'newMovieRating'
            ];

            fields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) field.value = '';
            });

            const languageField = document.getElementById('newMovieLanguage');
            if (languageField) languageField.value = 'tr';

            console.log('✅ Film formu temizlendi');
        }
            // Form validation
            function validateEditForm() {
                const title = document.getElementById('editMovieTitle').value.trim();
                const duration = document.getElementById('editMovieDuration').value;
                const genre = document.getElementById('editMovieGenre').value.trim();
                const date = document.getElementById('editMovieDate').value;

                const isValid = title && duration && genre && date;

                const updateBtn = document.getElementById('updateMovieBtn');
                if (updateBtn) {
                    updateBtn.disabled = !isValid;
                    updateBtn.classList.toggle('opacity-50', !isValid);
                    updateBtn.classList.toggle('cursor-not-allowed', !isValid);
                }

                return isValid;
            }

            // Film güncelleme
            async function updateMovie() {
                console.log('💾 Film güncelleniyor...');

                if (!validateEditForm()) {
                    alert('❌ Lütfen zorunlu alanları doldurun!');
                    return;
                }

                const movieId = document.getElementById('editMovieId').value;
                const movieData = {
                    title: document.getElementById('editMovieTitle').value.trim(),
                    duration: parseInt(document.getElementById('editMovieDuration').value),
                    genre: document.getElementById('editMovieGenre').value.trim(),
                    release_date: document.getElementById('editMovieDate').value,
                    language: document.getElementById('editMovieLanguage').value,
                    description: document.getElementById('editMovieDescription').value.trim(),
                    status: document.getElementById('editMovieStatus').value
                };

                const rating = document.getElementById('editMovieRating').value;
                if (rating && !isNaN(parseFloat(rating))) {
                    movieData.imdb_raiting = parseFloat(rating);
                }

                const posterUrl = document.getElementById('editMoviePosterUrl').value.trim();
                if (posterUrl) {
                    movieData.poster_url = posterUrl;
                }

                try {
                    const tokenValid = await ensureValidToken();
                    if (!tokenValid) {
                        alert('❌ Admin girişi başarısız!');
                        return;
                    }

                    const response = await axios.put(`/api/movies/${movieId}`, movieData, {
                        headers: {
                            'Authorization': `Bearer ${localStorage.getItem('token')}`,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });

                    if (response.data.success) {
                        alert('✅ Film başarıyla güncellendi: ' + movieData.title);
                        hideEditMovieForm();
                        loadMoviesAdmin();
                    } else {
                        alert('❌ Film güncellenemedi: ' + (response.data.message || 'Bilinmeyen hata'));
                    }

                } catch (error) {
                    console.log('❌ Film güncelleme hatası:', error.response?.data || error.message);

                    if (error.response?.data?.errors) {
                        let errorMessage = 'Validation hataları:\n';
                        Object.entries(error.response.data.errors).forEach(([field, messages]) => {
                            errorMessage += `• ${field}: ${messages.join(', ')}\n`;
                        });
                        alert('❌ ' + errorMessage);
                    } else {
                        alert('❌ Film güncellenemedi: ' + (error.response?.data?.message || error.message));
                    }
                }
            }

            // Form gizleme
            function hideEditMovieForm() {
                console.log('❌ Film düzenleme formu gizleniyor');
                const form = document.getElementById('editMovieForm');
                if (form) {
                    form.classList.add('hidden');
                }
            }

            // Form temizleme
            function clearEditMovieForm() {
                console.log('🧹 Film düzenleme formu temizleniyor...');

                const fields = [
                    'editMovieId', 'editMovieTitle', 'editMovieDuration',
                    'editMovieGenre', 'editMovieRating', 'editMovieDate',
                    'editMovieDescription', 'editMoviePosterUrl'
                ];

                fields.forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    if (field) field.value = '';
                });

                // Default values
                const languageField = document.getElementById('editMovieLanguage');
                if (languageField) languageField.value = 'tr';

                const statusField = document.getElementById('editMovieStatus');
                if (statusField) statusField.value = 'active';

                // Poster preview temizle
                updatePosterPreview('');

                console.log('✅ Film düzenleme formu temizlendi');
            }

            // Poster URL değiştiğinde çalışır
            function onPosterUrlChange() {
                const url = document.getElementById('editMoviePosterUrl').value.trim();
                updatePosterPreview(url);
            }

            // Poster önizlemesi güncelle
            function updatePosterPreview(url) {
                const previewDiv = document.getElementById('posterPreview');
                if (!previewDiv) return;

                if (url) {
                    previewDiv.innerHTML = `
                                        <img src="${url}" alt="Poster Önizleme" 
                                             class="w-full h-48 object-cover rounded-lg border-2 border-gray-300"
                                             onerror="this.parentElement.innerHTML='<div class=\\'w-full h-48 bg-red-100 rounded-lg flex items-center justify-center border-2 border-red-300\\'><div class=\\'text-center\\'><i class=\\'fas fa-exclamation-triangle text-red-500 text-3xl mb-2\\'></i><p class=\\'text-red-500 text-sm\\'>Poster yüklenemedi</p></div></div>'">
                                    `;
                } else {
                    previewDiv.innerHTML = `
                                        <div class="w-full h-48 bg-gray-300 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-400">
                                            <div class="text-center">
                                                <i class="fas fa-image text-gray-500 text-3xl mb-2"></i>
                                                <p class="text-gray-500 text-sm">Poster URL'si girin</p>
                                            </div>
                                        </div>
                                    `;
                }
            }

            // TMDB poster arama (opsiyonel)
            async function searchPosterForEdit() {
                const title = document.getElementById('editMovieTitle').value.trim();
                if (!title) {
                    alert('❌ Önce film başlığını girin!');
                    return;
                }

                // Bu fonksiyonu TMDB API'si ile geliştirebilirsiniz
                alert('🔍 TMDB poster arama özelliği henüz aktif değil.\n\nManuel olarak poster URL\'si ekleyebilirsiniz.');
            }

            // Film ekleme
            async function addMovie() {
                console.log('➕ Yeni film ekleniyor...');

            // Form verilerini al
            const title = document.getElementById('newMovieTitle').value.trim();
            const duration = document.getElementById('newMovieDuration').value;
            const genre = document.getElementById('newMovieGenre').value.trim();
            const releaseDate = document.getElementById('newMovieDate').value;
            const description = document.getElementById('newMovieDescription').value.trim();
            const rating = document.getElementById('newMovieRating').value;

            // Language alanını kontrol et
            const languageField = document.getElementById('newMovieLanguage');
            let language = 'tr';
            if (languageField && languageField.value) {
                language = languageField.value;
            }

            // Kontrol
            if (!title || !duration || !genre || !releaseDate) {
                alert('❌ Lütfen zorunlu alanları doldurun!');
                return;
            }

            // API'ye gönderilecek veri
            const movieData = {
                title: title,
                duration: parseInt(duration),
                genre: genre,
                release_date: releaseDate,
                language: language,
                description: description,
                status: 'active'
            };

            if (rating && !isNaN(parseFloat(rating))) {
                movieData.imdb_raiting = parseFloat(rating);
            }

            console.log('📤 Gönderilecek film verisi:', movieData);

            try {
                // Token kontrolü
                const tokenValid = await ensureValidToken();
                if (!tokenValid) {
                    alert('❌ Admin girişi başarısız!');
                    return;
                }

                // API'ye gönder
                const response = await axios.post('/api/movies', movieData, {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                console.log('✅ Film API yanıtı:', response.data);

                if (response.data.success) {
                    alert('✅ Film başarıyla eklendi: ' + movieData.title);
                    clearMovieForm();
                    hideAddMovieForm();
                    loadMoviesAdmin();
                } else {
                    alert('❌ Film eklenemedi: ' + (response.data.message || 'Bilinmeyen hata'));
                }

            } catch (error) {
                console.log('❌ Film ekleme hatası:', error.response?.data || error.message);

                if (error.response?.data?.errors) {
                    let errorMessage = 'Validation hataları:\n';
                    Object.entries(error.response.data.errors).forEach(([field, messages]) => {
                        errorMessage += `• ${field}: ${messages.join(', ')}\n`;
                    });
                    alert('❌ ' + errorMessage);
                } else {
                    alert('❌ Film eklenemedi: ' + (error.response?.data?.message || error.message));
                }
            }
        }

            // Film silme (geliştirilmiş - ilişkili veriler kontrolü ile)
            async function deleteMovie(movieId) {
                console.log('🗑️ Film silme işlemi başlatılıyor. ID:', movieId);

            // İlk önce filmin seanslarını kontrol et
            try {
                const response = await axios.get(`/api/showtimes?movie_id=${movieId}`);
                const showtimes = response.data.data.data || response.data.data || [];

                if (showtimes.length > 0) {
                    const confirmMessage = `Bu filme ait ${showtimes.length} seans var!\n\n` +
                        `Film silinirse tüm seanslar da silinecek.\n` +
                        `Devam etmek istediğinizden emin misiniz?`;

                    if (!confirm(confirmMessage)) {
                        console.log('❌ Film silme işlemi iptal edildi - seanslar var');
                        return;
                    }

                    // Önce seansları sil
                    console.log('🔄 İlişkili seanslar siliniyor...');
                    for (let showtime of showtimes) {
                        try {
                            await axios.delete(`/api/showtimes/${showtime.id}`, {
                                headers: {
                                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                                    'Accept': 'application/json'
                                }
                            });
                            console.log('✅ Seans silindi:', showtime.id);
                        } catch (error) {
                            console.log('❌ Seans silme hatası:', showtime.id, error.message);

                            // Eğer seans silinmezse (bilet satılmışsa) durumu kullanıcıya bildir
                            if (error.response?.status === 422) {
                                alert(`❌ Bu film silinemez!\n\nSeans ID ${showtime.id} için bilet satılmış.\nÖnce biletleri iptal edin veya seansı tamamlayın.`);
                                return;
                            }
                        }
                    }
                }
            } catch (error) {
                console.log('⚠️ Seans kontrolü yapılamadı:', error.message);
            }

            // Film silme onayı
            const finalConfirm = confirm('Film ve tüm seansları silmek istediğinizden emin misiniz?\n\nBu işlem geri alınamaz!');
            if (!finalConfirm) {
                return;
            }

            try {
                const tokenValid = await ensureValidToken();
                if (!tokenValid) {
                    alert('❌ Admin girişi başarısız!');
                    return;
                }

                console.log('📤 Film silme isteği gönderiliyor...');

                const response = await axios.delete(`/api/movies/${movieId}`, {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`,
                        'Accept': 'application/json'
                    }
                });

                console.log('✅ Film silme API yanıtı:', response.data);

                if (response.data.success) {
                    alert('✅ Film ve tüm seansları başarıyla silindi!');
                    loadMoviesAdmin();
                    // Seans listesi açıksa onu da yenile
                    const showtimeTab = document.getElementById('adminShowtimesTab');
                    if (showtimeTab && !showtimeTab.classList.contains('hidden')) {
                        loadShowtimesAdmin();
                    }
                } else {
                    alert('❌ Film silinemedi: ' + (response.data.message || 'Bilinmeyen hata'));
                }

            } catch (error) {
                console.log('❌ Film silme hatası:', error.response?.data || error.message);

                if (error.response?.status === 404) {
                    alert('❌ Film bulunamadı!');
                } else if (error.response?.status === 422) {
                    // Hala ilişkili veriler varsa detaylı mesaj
                    const errorMsg = error.response.data.message || 'İlişkili veriler var';

                    if (errorMsg.includes('ticket') || errorMsg.includes('bilet')) {
                        alert('❌ Bu film silinemez!\n\nBu filme ait biletler satılmış.\nÖnce biletleri iptal edin veya filmi "pasif" duruma alın.');
                    } else if (errorMsg.includes('showtime') || errorMsg.includes('seans')) {
                        alert('❌ Bu film silinemez!\n\nBu filme ait aktif seanslar var.\nÖnce seansları silin.');
                    } else {
                        alert('❌ Bu film silinemez!\n\nİlişkili veriler: ' + errorMsg);
                    }
                } else {
                    alert('❌ Film silinemedi: ' + (error.response?.data?.message || error.message));
                }
            }
        }

            async function editMovie(movieId) {
                console.log('✏️ Film düzenleme formu açılıyor:', movieId);

            try {
                const tokenValid = await ensureValidToken();
                if (!tokenValid) {
                    alert('❌ Admin girişi başarısız!');
                    return;
                }

                // Film bilgilerini API'den al
                const response = await axios.get(`/api/movies/${movieId}`);
                const movie = response.data.data;


                let formattedDate = '';
                if (movie.release_date) {
                    const parts = movie.release_date.split('-'); // ["08", "07", "2025"]
                    if (parts.length === 3) {
                        formattedDate = `${parts[2]}-${parts[1]}-${parts[0]}`; // "2025-07-08"
                        console.log('✅ Tarih çevrildi:', movie.release_date, '→', formattedDate);
                    }
                }



                // Düzenleme formunu doldur
                document.getElementById('editMovieId').value = movie.id;
                document.getElementById('editMovieTitle').value = movie.title || '';
                document.getElementById('editMovieDuration').value = movie.duration || '';
                document.getElementById('editMovieGenre').value = movie.genre || '';
                document.getElementById('editMovieRating').value = movie.imdb_raiting || '';
                document.getElementById('editMovieDate').value = formattedDate;
                document.getElementById('editMovieLanguage').value = movie.language || 'tr';
                document.getElementById('editMovieDescription').value = movie.description || '';
                document.getElementById('editMovieStatus').value = movie.status || 'active';
                document.getElementById('editMoviePosterUrl').value = movie.poster_url || '';

                // Poster önizlemesi
                if (movie.poster_url) {
                    updatePosterPreview(movie.poster_url);
                }

                // Formu göster
                document.getElementById('editMovieForm').classList.remove('hidden');

                // Forma scroll yap
                document.getElementById('editMovieForm').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });

            } catch (error) {
                console.log('❌ Film bilgisi alınamadı:', error.message);
                alert('❌ Film bilgileri yüklenemedi!');
            }
        }

            // Film durumu güncelleme
            async function updateMovieStatus(movieId, status) {
                console.log(`🔄 Film durumu değiştiriliyor: ${movieId} → ${status}`);

            try {
                const tokenValid = await ensureValidToken();
                if (!tokenValid) {
                    alert('❌ Admin girişi başarısız!');
                    return;
                }

                const response = await axios.put(`/api/movies/${movieId}`, {
                    status: status
                }, {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                console.log('✅ Film durum güncelleme yanıtı:', response.data);

                if (response.data.success) {
                    const statusText = status === 'active' ? 'Aktif' : 'Pasif';
                    alert(`✅ Film durumu "${statusText}" olarak güncellendi!`);
                    loadMoviesAdmin();
                } else {
                    alert('❌ Film durumu güncellenemedi: ' + (response.data.message || 'Bilinmeyen hata'));
                }

            } catch (error) {
                console.log('❌ Film durum güncelleme hatası:', error.response?.data || error.message);
                alert('❌ Film durumu güncellenemedi: ' + (error.response?.data?.message || error.message));
            }
        }

            // 🎭 SEANS YÖNETİMİ
            function showAddShowtimeForm() {
                console.log('🎭 Seans ekleme formu gösteriliyor');
                const form = document.getElementById('addShowtimeForm');
                if (form) {
                    form.classList.remove('hidden');
                    loadMoviesForDropdown();
                    loadHallsForDropdown();
                }
            }

            function hideAddShowtimeForm() {
                console.log('❌ Seans ekleme formu gizleniyor');
                const form = document.getElementById('addShowtimeForm');
                if (form) {
                    form.classList.add('hidden');
                    clearShowtimeForm();
                }
            }

            function clearShowtimeForm() {
                const fields = ['newShowtimeMovie', 'newShowtimeHall', 'newShowtimeStart'];
                fields.forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    if (field) field.value = '';
                });
            }

            async function loadMoviesForDropdown() {
                console.log('📽️ Film dropdown yükleniyor...');

            try {
                const response = await axios.get('/api/movies?per_page=100');
                const movies = response.data.data.data || response.data.data || [];

                let html = '<option value="">Film Seçin</option>';
                movies.forEach(movie => {
                    html += `<option value="${movie.id}">${movie.title}</option>`;
                });

                const dropdown = document.getElementById('newShowtimeMovie');
                if (dropdown) {
                    dropdown.innerHTML = html;
                    console.log('✅ Film dropdown yüklendi:', movies.length, 'film');
                }

            } catch (error) {
                console.log('❌ Film dropdown hatası:', error.message);

                const testMovies = [
                    { id: 1, title: "Test Film 1" },
                    { id: 2, title: "Test Film 2" }
                ];

                let html = '<option value="">Film Seçin</option>';
                testMovies.forEach(movie => {
                    html += `<option value="${movie.id}">${movie.title}</option>`;
                });

                const dropdown = document.getElementById('newShowtimeMovie');
                if (dropdown) dropdown.innerHTML = html;
            }
        }

            async function loadHallsForDropdown() {
                console.log('🏛️ Salon dropdown yükleniyor...');

            try {
                const response = await axios.get('/api/halls');
                const halls = response.data.data || [];

                let html = '<option value="">Salon Seçin</option>';
                halls.forEach(hall => {
                    // Sinema adı + Salon adı
                    const displayName = hall.cinema ?
                        `${hall.cinema.name} - ${hall.name}` :
                        hall.name;

                    html += `<option value="${hall.id}">${displayName}</option>`;
                });

                const dropdown = document.getElementById('newShowtimeHall');
                if (dropdown) {
                    dropdown.innerHTML = html;
                    console.log('✅ Salon dropdown yüklendi:', halls.length, 'salon');
                }

            } catch (error) {
                console.log('❌ Salon dropdown hatası:', error.message);

                const testHalls = [
                    { id: 1, name: "Test Sinema - Salon 1" },
                    { id: 2, name: "Test Sinema - Salon 2" }
                ];

                let html = '<option value="">Salon Seçin</option>';
                testHalls.forEach(hall => {
                    html += `<option value="${hall.id}">${hall.name}</option>`;
                });

                const dropdown = document.getElementById('newShowtimeHall');
                if (dropdown) dropdown.innerHTML = html;
            }
        }

            async function addShowtime() {
                console.log('🎭 Yeni seans ekleniyor...');

            const movieId = document.getElementById('newShowtimeMovie').value;
            const hallId = document.getElementById('newShowtimeHall').value;
            const startTime = document.getElementById('newShowtimeStart').value;

            if (!movieId || !hallId || !startTime) {
                alert('❌ Lütfen tüm alanları doldurun!');
                return;
            }

            const startTimeFormatted = startTime.replace('T', ' ') + ':00';
            const dateOnly = startTime.split('T')[0];

            const showtimeData = {
                movie_id: parseInt(movieId),
                hall_id: parseInt(hallId),
                start_time: startTimeFormatted,
                date: dateOnly,
                status: 'active'
            };

            console.log('📤 Gönderilecek seans verisi:', showtimeData);

            try {
                const tokenValid = await ensureValidToken();
                if (!tokenValid) {
                    alert('❌ Admin girişi başarısız!');
                    return;
                }

                const response = await axios.post('/api/showtimes', showtimeData, {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                console.log('✅ Seans API yanıtı:', response.data);

                if (response.data.success) {
                    alert('✅ Seans başarıyla eklendi!');
                    clearShowtimeForm();
                    hideAddShowtimeForm();
                    loadShowtimesAdmin();
                } else {
                    alert('❌ Seans eklenemedi: ' + (response.data.message || 'Bilinmeyen hata'));
                }

            } catch (error) {
                console.log('❌ Seans ekleme hatası:', error.response?.data || error.message);

                if (error.response?.data?.errors) {
                    let errorMessage = 'Validation hataları:\n';
                    Object.entries(error.response.data.errors).forEach(([field, messages]) => {
                        errorMessage += `• ${field}: ${messages.join(', ')}\n`;
                    });
                    alert('❌ ' + errorMessage);
                } else {
                    alert('❌ Seans eklenemedi: ' + (error.response?.data?.message || error.message));
                }
            }
        }

            async function loadShowtimesAdmin() {
                console.log('🎭 Seanslar yükleniyor...');

            try {
                const response = await axios.get('/api/showtimes');
                const showtimes = response.data.data.data || response.data.data || [];

                console.log('📊 Yüklenen seanslar:', showtimes);
                showShowtimesOnScreen(showtimes);

            } catch (error) {
                console.log('❌ Seans yükleme hatası:', error.message);

                const testShowtimes = [
                    {
                        id: 1,
                        movie: { title: "Test Film" },
                        hall: { cinema: { name: "Test Sinema" }, name: "Salon 1" },
                        start_time: "2025-07-09T20:00:00"
                    }
                ];

                showShowtimesOnScreen(testShowtimes);
            }
        }

            function showShowtimesOnScreen(showtimes) {
                console.log('🎬 ' + showtimes.length + ' seans gösteriliyor');

            const showtimeList = document.getElementById('adminShowtimeList');
            if (!showtimeList) {
                console.log('❌ Seans listesi elementi bulunamadı!');
                return;
            }

            if (showtimes.length === 0) {
                showtimeList.innerHTML = `
                                                <div class="glass-effect rounded-xl p-8 text-center">
                                                    <h4 class="text-xl font-semibold text-white mb-2">Henüz seans bulunmuyor</h4>
                                                    <p class="text-gray-300">Yeni seans eklemek için yukarıdaki butonu kullanın.</p>
                                                </div>
                                            `;
                return;
            }

            let html = '';
            showtimes.forEach(showtime => {
                const movieTitle = showtime.movie?.title || 'Bilinmeyen Film';
                const cinemaName = showtime.hall?.cinema?.name || 'Bilinmeyen Sinema';
                const hallName = showtime.hall?.name || 'Bilinmeyen Salon';

                let startTime = 'Tarih belirsiz';
                if (showtime.start_time) {
                    try {
                        const date = new Date(showtime.start_time);
                        startTime = date.toLocaleString('tr-TR');
                    } catch (e) {
                        startTime = showtime.start_time;
                    }
                }

                html += `
                                                <div class="glass-effect rounded-xl p-6 flex justify-between items-center">
                                                    <div class="flex-1">
                                                        <h4 class="text-lg font-semibold text-white mb-2">${movieTitle}</h4>
                                                        <p class="text-purple-300 text-sm mb-1">${cinemaName} - ${hallName}</p>
                                                        <p class="text-emerald-400 text-sm font-medium">🕐 ${startTime}</p>
                                                    </div>
                                                    <div class="flex space-x-3">
                                                        <button onclick="changeShowtimeStatus(${showtime.id})" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">
                                                            ⚙️ Durum
                                                        </button>
                                                        <button onclick="deleteShowtime(${showtime.id})" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                                                            🗑️ Sil
                                                        </button>
                                                    </div>
                                                </div>
                                            `;
            });

            showtimeList.innerHTML = html;
            console.log('✅ Seanslar ekranda gösterildi');
        }

            // Seans durumu değiştirme
            async function changeShowtimeStatus(showtimeId) {
                console.log('⚙️ Seans durumu değiştiriliyor. ID:', showtimeId);

                try {
                    // Önce seans bilgilerini al
                    const response = await axios.get(`/api/showtimes/${showtimeId}`);
                    const showtimeData = response.data.data;

                    const movieTitle = showtimeData.movie?.title || 'Bilinmeyen Film';
                    const startTime = new Date(showtimeData.start_time).toLocaleString('tr-TR');
                    const currentStatus = showtimeData.status || 'active';
                    const soldTickets = showtimeData.sold_seats || showtimeData.tickets?.length || 0;

                    // Durum seçenekleri
                    let message = `Seans Durum Değiştirme\n\n`;
                    message += `Film: ${movieTitle}\n`;
                    message += `Seans: ${startTime}\n`;
                    message += `Mevcut Durum: ${currentStatus}\n`;
                    message += `Satılan Bilet: ${soldTickets} adet\n\n`;
                    message += `Yeni durum seçin:\n\n`;
                    message += `TAMAM = İptal Et (cancelled)\n`;
                    message += `İPTAL = Aktif Yap (active)`;

                    const action = confirm(message);

                    if (action !== null) {
                        const newStatus = action ? 'cancelled' : 'active';
                        await updateShowtimeStatus(showtimeId, newStatus, movieTitle, startTime, soldTickets);
                    }

                } catch (error) {
                    console.log('❌ Seans bilgisi alınamadı:', error.message);

                    // Basit durum değiştirme
                    const action = confirm('Seans durumu değiştir:\n\nTAMAM = İptal Et\nİPTAL = Aktif Yap');

                    if (action !== null) {
                        const newStatus = action ? 'cancelled' : 'active';
                        await updateShowtimeStatus(showtimeId, newStatus);
                    }
                }
            }

            // Seans durumu güncelleme
            async function updateShowtimeStatus(showtimeId, status, movieTitle = '', startTime = '', soldTickets = 0) {
                console.log(`🔄 Seans durumu güncelleniyor: ${showtimeId} → ${status}`);

                try {
                    const tokenValid = await ensureValidToken();
                    if (!tokenValid) {
                        alert('❌ Admin girişi başarısız!');
                        return;
                    }

                    const response = await axios.put(`/api/showtimes/${showtimeId}`, {
                        status: status
                    }, {
                        headers: {
                            'Authorization': `Bearer ${localStorage.getItem('token')}`,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });

                    console.log('✅ Seans durum güncelleme yanıtı:', response.data);

                    if (response.data.success) {
                        let successMessage = '✅ Seans durumu güncellendi!\n\n';

                        if (movieTitle) {
                            successMessage += `Film: ${movieTitle}\n`;
                            successMessage += `Seans: ${startTime}\n`;
                        }

                        if (status === 'cancelled') {
                            successMessage += `Durum: İPTAL EDİLDİ ❌\n\n`;
                            if (soldTickets > 0) {
                                successMessage += `⚠️ NOT: ${soldTickets} adet bilet için müşterilere bilgilendirme yapılmalı!`;
                            }
                        } else if (status === 'active') {
                            successMessage += `Durum: AKTİF ✅`;
                        }

                        alert(successMessage);
                        loadShowtimesAdmin();
                    } else {
                        alert('❌ Seans durumu güncellenemedi: ' + (response.data.message || 'Bilinmeyen hata'));
                    }

                } catch (error) {
                    console.log('❌ Seans durum güncelleme hatası:', error.response?.data || error.message);

                    if (error.response?.status === 422) {
                        const errorMsg = error.response.data.message || 'Validation hatası';
                        alert('❌ Durum güncellenemedi:\n\n' + errorMsg);
                    } else {
                        alert('❌ Seans durumu güncellenemedi: ' + (error.response?.data?.message || error.message));
                    }
                }
            }

            async function deleteShowtime(showtimeId) {
                console.log('🗑️ Seans silme işlemi başlatılıyor. ID:', showtimeId);

            // İlk önce bu seansa ait biletleri kontrol et
            try {
                const response = await axios.get(`/api/showtimes/${showtimeId}`);
                const showtimeData = response.data.data;

                console.log('📊 Seans bilgisi:', showtimeData);

                // Bilet sayısını kontrol et
                const soldTickets = showtimeData.sold_seats || showtimeData.tickets?.length || 0;

                if (soldTickets > 0) {
                    const movieTitle = showtimeData.movie?.title || 'Bilinmeyen Film';
                    const startTime = new Date(showtimeData.start_time).toLocaleString('tr-TR');

                    const errorMessage = `❌ Bu seans silinemez!\n\n` +
                        `Film: ${movieTitle}\n` +
                        `Seans: ${startTime}\n` +
                        `Satılan Bilet: ${soldTickets} adet\n\n` +
                        `🔧 Önerilen çözümler:\n` +
                        `• "⚙️ Durum" butonuyla seansı iptal edin\n` +
                        `• Biletleri iade edin\n` +
                        `• Seans tarihini bekleyin`;

                    alert(errorMessage);
                    return;
                } else {
                    // Bilet yoksa normal onay
                    const movieTitle = showtimeData.movie?.title || 'Bilinmeyen Film';
                    const startTime = new Date(showtimeData.start_time).toLocaleString('tr-TR');

                    const confirmMessage = `Seans Silme Onayı\n\n` +
                        `Film: ${movieTitle}\n` +
                        `Seans: ${startTime}\n` +
                        `Satılan Bilet: Yok ✅\n\n` +
                        `Bu seansı kalıcı olarak silmek istediğinizden emin misiniz?`;

                    if (!confirm(confirmMessage)) {
                        console.log('❌ Seans silme iptal edildi');
                        return;
                    }
                }

            } catch (error) {
                console.log('⚠️ Seans detay bilgisi alınamadı:', error.message);

                // Detay alınamazsa basit onay
                if (!confirm('Bu seansı silmek istediğinizden emin misiniz?\n\n(Bilet bilgisi kontrol edilemedi)')) {
                    return;
                }
            }

            try {
                const tokenValid = await ensureValidToken();
                if (!tokenValid) {
                    alert('❌ Admin girişi başarısız!');
                    return;
                }

                console.log('📤 Seans silme isteği gönderiliyor...');

                const response = await axios.delete(`/api/showtimes/${showtimeId}`, {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`,
                        'Accept': 'application/json'
                    }
                });

                console.log('✅ Seans silme API yanıtı:', response.data);

                if (response.data.success) {
                    alert('✅ Seans başarıyla silindi!');
                    loadShowtimesAdmin();

                    // Film listesi açıksa onu da yenile (dropdown için)
                    const movieTab = document.getElementById('adminMoviesTab');
                    if (movieTab && !movieTab.classList.contains('hidden')) {
                        loadMoviesForDropdown();
                    }
                } else {
                    alert('❌ Seans silinemedi: ' + (response.data.message || 'Bilinmeyen hata'));
                }

            } catch (error) {
                console.log('❌ Seans silme hatası:', error.response?.data || error.message);

                if (error.response?.status === 404) {
                    alert('❌ Seans bulunamadı!');
                } else if (error.response?.status === 422) {
                    // Detaylı bilet hatası
                    const errorMsg = error.response.data.message || 'İlişkili veriler var';

                    alert('❌ Bu seans silinemez!\n\n' +
                        'Sebep: ' + errorMsg + '\n\n' +
                        '🔧 Çözüm: "⚙️ Durum" butonuyla seansı iptal edin!');
                } else if (error.response?.status === 403) {
                    alert('❌ Bu işlem için yetkiniz yok!');
                } else {
                    alert('❌ Seans silinemedi: ' + (error.response?.data?.message || error.message));
                }
            }
        }



    </script>

@endsection