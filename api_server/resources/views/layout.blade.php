<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>🎬 Modern Cinema Automation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        .progress-scroll-wrapper {
            overflow-x: auto;
            max-width: 100%;
            scrollbar-width: none;
            scroll-behavior: smooth;
        }

        .progress-scroll-wrapper::-webkit-scrollbar {
            display: none;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .seat {
            transition: all 0.2s ease;
        }

        .seat:hover {
            transform: scale(1.1);
        }

        .loading {
            animation: pulse 2s infinite;
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .floating-animation {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .stat-card {
            background: linear-gradient(145deg, #ffffff, #f3f4f6);
            box-shadow: 20px 20px 60px #d1d5db, -20px -20px 60px #ffffff;
        }

        .admin-tab-btn.active {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        /* Select dropdown styling */
        select {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.1) !important;
        }

        select option {
            background-color: #1f2937 !important;
            color: white !important;
            padding: 8px;
        }

        select option:hover,
        select option:focus,
        select option:checked {
            background-color: #374151 !important;
            color: white !important;
        }

        /* Select dropdown when opened */
        select:focus option {
            background-color: #1f2937 !important;
            color: white !important;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .loading {
            border-top-color: transparent;
            animation: spin 1s linear infinite;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-900 via-green-900 to-slate-900 min-h-screen">
    <!-- Navigation -->
    <nav class="glass-effect sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="/" class="flex items-center space-x-3 hover:opacity-90 transition-opacity">
                        <div class="w-12 h-12 rounded-full bg-white/10 border-2 border-emerald-400/60 shadow-md flex items-center justify-center p-1.5">
                            <img src="{{ asset('images/logo.png') }}" alt="Cinema Automation" class="w-full h-full object-contain rounded-full" style="display: block !important;" onerror="console.error('Logo could not be loaded:', this.src); this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div style="display: none;" class="w-full h-full rounded-full bg-gradient-to-r from-green-500 to-emerald-500 flex items-center justify-center">
                                <i class="fas fa-film text-white text-lg"></i>
                            </div>
                        </div>
                        <h1 class="text-xl font-bold text-white">Cinema Automation</h1>
                    </a>
                </div>
                <!-- ✅ Role-based navigation -->
                <div class="hidden md:flex space-x-8">
                    <a href="/" class="nav-link text-white hover:text-green-300 transition-colors">
                        <i class="fas fa-home mr-2"></i>Home
                    </a>
                    <a href="/movies" class="nav-link text-white hover:text-green-300 transition-colors">
                        <i class="fas fa-play mr-2"></i>Movies
                    </a>
                    @auth
                        @if(auth()->user()->isCustomer())
                            <!-- Customer can only buy tickets -->
                            <a href="/buy-tickets" class="nav-link text-white hover:text-green-300 transition-colors">
                                <i class="fas fa-building mr-2"></i>Cinema and Halls
                            </a>
                            <a href="/my-tickets" class="nav-link text-white hover:text-green-300 transition-colors">
                                <i class="fas fa-history mr-2"></i>My Tickets
                            </a>
                        @else
                            <!-- Admins can sell tickets and see management -->
                            <a href="/tickets" class="nav-link text-white hover:text-green-300 transition-colors">
                                <i class="fas fa-ticket-alt mr-2"></i>Ticket Sales
                            </a>
                            <a href="/admin" class="nav-link text-white hover:text-green-300 transition-colors">
                                <i class="fas fa-cog mr-2"></i>Management
                            </a>
                        @endif

                        <!-- Profile -->
                        <a href="/profile" class="nav-link text-white hover:text-green-300 transition-colors">
                            <i class="fas fa-user mr-2"></i>Profile
                        </a>
                    @else
                        <!-- Guest (not authenticated) users -->
                        <a href="/login" class="nav-link text-white hover:text-green-300 transition-colors">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login
                        </a>
                        <a href="/register" class="nav-link text-white hover:text-green-300 transition-colors">
                            <i class="fas fa-user-plus mr-2"></i>Register
                        </a>
                    @endauth
                </div>

                <button class="md:hidden text-white">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md mx-4 text-center">
            <div class="w-16 h-16 bg-emerald-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-white text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Operation Successful!</h3>
            <p id="successMessage" class="text-gray-600 mb-6">Your action has been completed successfully.</p>
            <button onclick="closeSuccessModal()"
                class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-xl font-semibold transition-all">
                Tamam
            </button>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 text-center">
            <div class="loading w-12 h-12 border-4 border-green-500 border-t-transparent rounded-full mx-auto mb-4">
            </div>
            <p class="text-gray-600 font-medium">Loading...</p>
        </div>
    </div>

    <script>
        // Global functions for modals
        function showSuccessModal(message) {
            document.getElementById('successMessage').textContent = message;
            document.getElementById('successModal').classList.remove('hidden');
        }

        function closeSuccessModal() {
            document.getElementById('successModal').classList.add('hidden');
        }

        function showLoading() {
            document.getElementById('loadingOverlay').classList.remove('hidden');
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').classList.add('hidden');
        }

        // ✅ User permissions for JavaScript
        window.userPermissions = {
            @auth
                isLoggedIn: true,
                role: '{{ auth()->user()->role->name }}',
                roleId: {{ auth()->user()->role_id }},
                isAdmin: {{ auth()->user()->isAdmin() || auth()->user()->isSuperAdmin() ? 'true' : 'false' }},
                isSuperAdmin: {{ auth()->user()->isSuperAdmin() ? 'true' : 'false' }},
                isCustomer: {{ auth()->user()->isCustomer() ? 'true' : 'false' }},
                userName: '{{ auth()->user()->name }}',
                email: '{{ auth()->user()->email }}',
                phone: '{{ auth()->user()->phone ?? '' }}'
            @else
                isLoggedIn: false,
                role: null,
                roleId: null,
                isAdmin: false,
                isSuperAdmin: false,
                isCustomer: false,
                userName: null,
                email: null,
                phone: null
            @endauth
        };

        // ✅ Axios interceptor for handling token expiration globally
        axios.interceptors.response.use(
            response => response,
            error => {
                // Handle 401 (Unauthorized) errors - token expired or invalid
                if (error.response && error.response.status === 401) {
                    // Check if we're not already on the login page to avoid infinite loops
                    if (!window.location.pathname.includes('/login')) {
                        console.warn('⚠️ Token expired or invalid. Redirecting to login...');
                        localStorage.removeItem('token');
                        localStorage.removeItem('user');
                        
                        // Show a brief notification before redirect
                        if (typeof showMessage === 'function') {
                            showMessage('Session expired. Please sign in again.', 'warning');
                        }
                        
                        // Redirect to login after a short delay
                        setTimeout(() => {
                            window.location.href = '/login';
                        }, 1500);
                    }
                }
                return Promise.reject(error);
            }
        );

    </script>
</body>

</html>