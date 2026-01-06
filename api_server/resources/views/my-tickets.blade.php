@extends('layout')

@section('content')
    <div class="glass-effect p-8 rounded-2xl">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-bold text-white flex items-center">
                <i class="fas fa-history mr-3 text-emerald-400"></i>
                My Tickets
            </h2>
            <a href="/" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Home
            </a>
        </div>

        <!-- Filter Section -->
        <div class="bg-white/10 p-4 rounded-xl mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-white text-sm font-medium mb-2">Date Filter</label>
                    <select id="dateFilter" class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white">
                        <option value="">All Dates</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                    </select>
                </div>
                <div>
                    <label class="block text-white text-sm font-medium mb-2">Status Filter</label>
                    <select id="statusFilter" class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="deactive">Deactive</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="refunded">Refunded</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button onclick="applyFilters()" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white py-2 px-4 rounded-lg font-medium">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading -->
        <div id="loadingState" class="text-center py-12">
            <div class="loading w-12 h-12 border-4 border-emerald-500 border-t-transparent rounded-full mx-auto mb-4"></div>
            <p class="text-white">Your tickets are loading...</p>
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="text-center py-12 hidden">
            <div class="w-24 h-24 bg-gray-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-ticket-alt text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">You don't have any tickets yet</h3>
            <p class="text-gray-400 mb-6">Buy a ticket now and start your cinema experience!</p>
            <a href="/tickets" class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-lg font-medium inline-flex items-center">
                <i class="fas fa-ticket-alt mr-2"></i>Buy Ticket
            </a>
        </div>

        <!-- Tickets List -->
        <div id="ticketsList" class="space-y-4 hidden">
            <!-- Tickets will be loaded here -->
        </div>

        <!-- Pagination -->
        <div id="paginationContainer" class="mt-8 flex justify-center hidden">
            <!-- Pagination will be loaded here -->
        </div>
    </div>

        <!-- Ticket Detail Modal -->
    <div id="ticketModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
        <div class="bg-slate-800 rounded-2xl p-6 max-w-md w-full">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-white">Ticket Details</h3>
                <button onclick="closeTicketModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="ticketModalContent">
                <!-- Ticket details will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        let currentPage = 1;
        let totalPages = 1;

        document.addEventListener('DOMContentLoaded', function() {
            loadMyTickets();
        });

        async function loadMyTickets(page = 1) {
            try {
                const token = localStorage.getItem('token');
                if (!token) {
                    const container = document.getElementById('ticketsList');
                    container.innerHTML = `
                        <div class="glass-effect p-8 rounded-xl text-center">
                            <div class="w-24 h-24 bg-yellow-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-exclamation-triangle text-yellow-400 text-4xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">Not Logged In</h3>
                            <p class="text-gray-400 mb-6">Please sign in to view your tickets.</p>
                            <a href="/login" class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-lg font-medium inline-flex items-center">
                                <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                            </a>
                        </div>
                    `;
                    showTicketsList();
                    hideLoading();
                    return;
                }

                showLoading();

                console.log('Loading tickets, page:', page);
                const response = await axios.get(`/api/my-tickets?page=${page}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });

                console.log('Response received:', response.data);
                hideLoading();

                if (response.data && response.data.success) {
                    const tickets = response.data.data?.data || [];
                    currentPage = response.data.data?.current_page || 1;
                    totalPages = response.data.data?.last_page || 1;

                    if (tickets.length === 0) {
                        showEmptyState();
                    } else {
                        renderTickets(tickets);
                        renderPagination();
                    }
                } else {
                    // Response başarısız veya beklenmeyen format
                    console.error('Unexpected response format:', response.data);
                    const container = document.getElementById('ticketsList');
                    container.innerHTML = `
                        <div class="glass-effect p-8 rounded-xl text-center">
                            <div class="w-24 h-24 bg-red-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-exclamation-circle text-red-400 text-4xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">Error Loading Tickets</h3>
                            <p class="text-gray-400 mb-6">${response.data?.message || 'Failed to load tickets. Please try again.'}</p>
                            <button onclick="loadMyTickets(1)" class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-lg font-medium inline-flex items-center">
                                <i class="fas fa-redo mr-2"></i>Retry
                            </button>
                        </div>
                    `;
                    showTicketsList();
                }

            } catch (error) {
                hideLoading();
                console.error('Failed to load tickets:', error);
                console.error('Error response:', error.response);
                
                if (error.response?.status === 401) {
                    // Token expired - clear token and redirect to login
                    localStorage.removeItem('token');
                    localStorage.removeItem('user');
                    
                    const container = document.getElementById('ticketsList');
                    container.innerHTML = `
                        <div class="glass-effect p-8 rounded-xl text-center">
                            <div class="w-24 h-24 bg-yellow-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-exclamation-triangle text-yellow-400 text-4xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">Session Expired</h3>
                            <p class="text-gray-400 mb-6">Your session has expired. Please sign in again to view your tickets.</p>
                            <div class="flex gap-3 justify-center">
                                <a href="/login" class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-lg font-medium inline-flex items-center">
                                    <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                                </a>
                                <a href="/" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-medium inline-flex items-center">
                                    <i class="fas fa-home mr-2"></i>Go to Home
                                </a>
                            </div>
                        </div>
                    `;
                    showTicketsList();
                } else {
                    // Diğer hatalar için hata mesajı göster
                    const container = document.getElementById('ticketsList');
                    const errorMessage = error.response?.data?.message || error.message || 'Failed to load tickets. Please try again.';
                    container.innerHTML = `
                        <div class="glass-effect p-8 rounded-xl text-center">
                            <div class="w-24 h-24 bg-red-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-exclamation-circle text-red-400 text-4xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">Error Loading Tickets</h3>
                            <p class="text-gray-400 mb-6">${errorMessage}</p>
                            <button onclick="loadMyTickets(1)" class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-lg font-medium inline-flex items-center">
                                <i class="fas fa-redo mr-2"></i>Retry
                            </button>
                        </div>
                    `;
                    showTicketsList();
                }
            }
        }

        function renderTickets(tickets) {
            console.log('Rendering tickets:', tickets);
            const container = document.getElementById('ticketsList');
            if (!container) {
                console.error('ticketsList container not found!');
                return;
            }
            let html = '';

            tickets.forEach(ticket => {
                const showtime = ticket.showtime;
                const movie = showtime.movie;
                const hall = showtime.hall;
                const cinema = hall.cinema;
                const seat = ticket.seat;
                const paymentMethod = ticket.sale?.payment_method || null;

                const showtimeDate = new Date(showtime.start_time);
                const purchaseDate = new Date(ticket.created_at);
                const isExpired = ticket.status === 'deactive';
                const borderColor = isExpired ? 'border-red-500/50' : 'border-emerald-500/50';
                const bgGradient = isExpired ? 'bg-gradient-to-br from-slate-800 to-red-900/20' : 'bg-gradient-to-br from-slate-800 to-emerald-900/20';

                const statusColors = {
                    'active': 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                    'deactive': 'bg-red-500/20 text-red-300 border-red-500/30',
                    'sold': 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                    'cancelled': 'bg-red-500/20 text-red-300 border-red-500/30',
                    'refunded': 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30'
                };

                const statusLabels = {
                    'active': 'Active',
                    'deactive': 'Deactive',
                    'sold': 'Active',
                    'cancelled': 'Cancelled',
                    'refunded': 'Refunded'
                };

                const customerTypeLabels = {
                    'adult': 'Adult',
                    'student': 'Student',
                    'senior': 'Retired',
                    'child': 'Child'
                };

                const paymentMethodLabels = {
                    'card': 'Credit Card',
                    'online': 'Online Payment'
                };

                const discountRate = parseFloat(ticket.discount_rate) || 0;
                const originalPrice = parseFloat(showtime.price) || parseFloat(ticket.price) || 0;
                const hasDiscount = discountRate > 0;

                html += `
                    <div class="glass-effect p-6 rounded-xl card-hover cursor-pointer border-2 ${borderColor} ${bgGradient} relative" onclick="showTicketDetail(${ticket.id})">
                        ${isExpired ? '<div class="absolute top-4 right-4 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1 shadow-lg"><i class="fas fa-ban"></i> EXPIRED</div>' : ''}
                        <div class="flex items-start gap-4 mb-4 ${isExpired ? 'opacity-70' : ''}">
                            <img src="${movie.poster_url ? (movie.poster_url.startsWith('http') ? movie.poster_url : '/storage/' + movie.poster_url) : '/images/default-movie.png'}" 
                                 alt="${movie.title}" 
                                 class="w-20 h-28 object-cover rounded-lg flex-shrink-0"
                                 onerror="this.src='/images/default-movie.png'">
                            <div class="flex-1">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <h3 class="text-xl font-bold text-white mb-1">${movie.title}</h3>
                                        <p class="text-purple-300 text-sm mb-1">${movie.genre} • ${movie.duration} min</p>
                                        <p class="text-gray-400 text-sm">${cinema.name}</p>
                                        <p class="text-emerald-400 text-sm">${hall.name}</p>
                                    </div>
                                    <div class="text-right ml-4">
                                        <div class="px-3 py-1 bg-emerald-500/20 rounded text-center mb-2">
                                            <p class="text-emerald-400 font-bold text-lg">₺${parseFloat(ticket.price).toFixed(2)}</p>
                                        </div>
                                        <span class="px-3 py-1 rounded-full text-xs font-bold border ${statusColors[ticket.status] || statusColors.sold} flex items-center gap-1 shadow-md">
                                            <i class="fas ${isExpired ? 'fa-times-circle' : 'fa-check-circle'}"></i>
                                            ${statusLabels[ticket.status] || 'Active'}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white/5 p-4 rounded-lg border border-white/10 mb-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-gray-400 text-xs mb-1">Date</p>
                                    <p class="text-white font-medium text-sm">${showtimeDate.toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</p>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-xs mb-1">Time</p>
                                    <p class="text-white font-medium text-sm">${showtimeDate.toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'})}</p>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-xs mb-1">Seat</p>
                                    <p class="text-white font-medium text-sm">${seat.row}${seat.number}</p>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-xs mb-1">Ticket Type</p>
                                    <p class="text-white font-medium text-sm">${customerTypeLabels[ticket.customer_type] || ticket.customer_type}</p>
                                </div>
                                ${hasDiscount ? `
                                <div>
                                    <p class="text-gray-400 text-xs mb-1">Discount</p>
                                    <p class="text-emerald-400 font-medium text-sm">${Number(discountRate).toFixed(0)}%</p>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-xs mb-1">Original Price</p>
                                    <p class="text-gray-500 font-medium text-sm line-through">₺${Number(originalPrice).toFixed(2)}</p>
                                </div>
                                ` : ''}
                            </div>
                        </div>

                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-calendar text-gray-400 text-xs"></i>
                            <p class="text-gray-400 text-xs">Purchase Date: <span class="text-white">${purchaseDate.toLocaleDateString('en-US')} ${purchaseDate.toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'})}</span></p>
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t border-white/10">
                            ${paymentMethod ? `
                            <div>
                                <p class="text-gray-400 text-xs">Payment Method</p>
                                <p class="text-emerald-300 text-sm font-medium">${paymentMethodLabels[paymentMethod] || paymentMethod}</p>
                            </div>
                            ` : '<div></div>'}
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
            showTicketsList();
        }

        function renderPagination() {
            if (totalPages <= 1) return;

            const container = document.getElementById('paginationContainer');
            let html = '<div class="flex items-center space-x-2">';

            // Previous button
            if (currentPage > 1) {
                html += `<button onclick="loadMyTickets(${currentPage - 1})" class="px-3 py-2 bg-white/10 text-white rounded-lg hover:bg-white/20">
                    <i class="fas fa-chevron-left"></i>
                </button>`;
            }

            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                if (i === currentPage) {
                    html += `<button class="px-3 py-2 bg-emerald-500 text-white rounded-lg">${i}</button>`;
                } else {
                    html += `<button onclick="loadMyTickets(${i})" class="px-3 py-2 bg-white/10 text-white rounded-lg hover:bg-white/20">${i}</button>`;
                }
            }

            // Next button
            if (currentPage < totalPages) {
                html += `<button onclick="loadMyTickets(${currentPage + 1})" class="px-3 py-2 bg-white/10 text-white rounded-lg hover:bg-white/20">
                    <i class="fas fa-chevron-right"></i>
                </button>`;
            }

            html += '</div>';
            container.innerHTML = html;
            document.getElementById('paginationContainer').classList.remove('hidden');
        }

        function showTicketDetail(ticketId) {
            // Simple detail modal - could be expanded
                    alert(`Ticket ID: ${ticketId}\n\nDetailed ticket information can be shown here.`);
        }

        function closeTicketModal() {
            document.getElementById('ticketModal').classList.add('hidden');
        }

        function applyFilters() {
            // For now, just reload tickets
            // In a full implementation, you would pass filter parameters to the API
            loadMyTickets(1);
        }

        function showLoading() {
            document.getElementById('loadingState').classList.remove('hidden');
            document.getElementById('emptyState').classList.add('hidden');
            document.getElementById('ticketsList').classList.add('hidden');
            document.getElementById('paginationContainer').classList.add('hidden');
        }

        function hideLoading() {
            document.getElementById('loadingState').classList.add('hidden');
        }

        function showEmptyState() {
            document.getElementById('emptyState').classList.remove('hidden');
            document.getElementById('ticketsList').classList.add('hidden');
            document.getElementById('paginationContainer').classList.add('hidden');
        }

        function showTicketsList() {
            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('ticketsList').classList.remove('hidden');
            document.getElementById('emptyState').classList.add('hidden');
        }
    </script>
@endsection