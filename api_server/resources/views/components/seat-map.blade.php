<!-- Step 3: Showtime Selection -->
<div id="ticketStep3" class="ticket-step hidden">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-2xl font-bold text-white text-center flex-1">
            <i class="fas fa-clock mr-2 text-purple-400"></i>Select a Showtime
        </h3>
        <button onclick="goBackToStep(2)"
            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Change Cinema
        </button>
    </div>

    <!-- Selected Movie & Cinema Info -->
    <div id="selectedMovieCinemaInfo" class="bg-white/10 p-4 rounded-xl mb-6"></div>

    <!-- Date Filter -->
    <div class="mb-6">
        <div class="max-w-md mx-auto">
            <label class="block text-white text-sm font-medium mb-2">
                <i class="fas fa-calendar mr-1"></i>Select a Date
            </label>
            <input type="date" id="dateFilter" onchange="filterShowtimesByDate(this.value)"
                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:bg-white/20 focus:border-purple-400 transition-all"
                min="">
        </div>
    </div>

    <!-- Showtime Count Info -->
    <div id="showtimeCountInfo" class="text-center mb-4">
        <span class="text-purple-300 text-sm">
            <i class="fas fa-info-circle mr-1"></i>
            <span id="filteredShowtimeCount">0</span> showtimes found
        </span>
    </div>

    <!-- Loading State -->
    <div id="showtimeLoadingState" class="text-center py-12 hidden">
        <div class="loading w-12 h-12 border-4 border-purple-400 border-t-transparent rounded-full mx-auto mb-4"></div>
        <p class="text-white">Showtimes are loading...</p>
    </div>
    <!-- Showtimes Grid -->
    <div id="showtimeGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Showtimes will be loaded here -->
    </div>

    <!-- Empty State -->
    <div id="showtimeEmptyState" class="text-center py-12 hidden">
        <div class="w-24 h-24 bg-gray-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-clock text-gray-400 text-3xl"></i>
        </div>
        <h4 class="text-xl font-bold text-white mb-2">No Showtimes Found</h4>
        <p class="text-gray-400">No showtimes match your filters.</p>
        <button onclick="clearShowtimeFilters()"
            class="mt-4 bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg font-medium">
            <i class="fas fa-refresh mr-2"></i>Clear Filters
        </button>
    </div>
</div>

<!-- Step 5: Seat Selection -->
<div id="ticketStep5" class="ticket-step hidden">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-2xl font-bold text-white text-center flex-1">
            <i class="fas fa-couch mr-2 text-green-400"></i>Pick Seats
        </h3>
        <button onclick="goBackToStep(4)"
            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Change Ticket Type
        </button>
    </div>

    <!-- Selected Showtime Info -->
    <div id="selectedShowtimeInfo" class="bg-white/10 p-4 rounded-xl mb-6"></div>

    <!-- Seat Map Container -->
    <div class="bg-white/10 p-6 rounded-xl">
        <!-- Refresh Button -->
        <div class="text-center mb-4">
            <button onclick="window.seatMap.manualRefresh()"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                <i class="fas fa-sync-alt mr-2"></i>Refresh Seats
            </button>
        </div>
        
        <!-- Screen -->
        <div class="text-center mb-6">
            <div class="bg-gray-800 text-white px-8 py-2 rounded-lg inline-block">
                <i class="fas fa-desktop mr-2"></i>SCREEN
            </div>
        </div>

        <!-- Loading State -->
        <div id="seatLoadingState" class="text-center py-12">
            <div class="loading w-12 h-12 border-4 border-green-400 border-t-transparent rounded-full mx-auto mb-4">
            </div>
            <p class="text-white">Seats are loading...</p>
        </div>

        <!-- Seat Map -->
        <div id="seatMap" class="max-w-4xl mx-auto hidden"></div>

        <!-- Seat Legend -->
        <div id="seatLegend" class="flex items-center justify-center space-x-8 mt-6 hidden">
            <div class="flex items-center">
                <div class="w-6 h-6 bg-emerald-500 rounded-lg mr-2"></div>
                <span class="text-white">Available</span>
            </div>
            <div class="flex items-center">
                <div class="w-6 h-6 bg-red-500 rounded-lg mr-2"></div>
                <span class="text-white">Taken</span>
            </div>
            <div class="flex items-center">
                <div class="w-6 h-6 bg-blue-500 rounded-lg mr-2"></div>
                <span class="text-white">Selected</span>
            </div>
        </div>

        <!-- Selected Seats Info -->
        <div class="text-center mt-4">
            <div id="selectedSeatsInfo" class="text-white font-medium mb-2">No seats selected</div>
            <div id="seatRequirementInfo" class="text-sm text-gray-300 mb-2"></div>
            <div id="selectedSeatsPrice" class="text-emerald-400 font-bold mb-4 hidden"></div>
            <button id="continueToPaymentStep" onclick="goToPayment()"
                class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-lg font-bold hidden">
                <i class="fas fa-arrow-right mr-2"></i>Proceed to Payment
            </button>
        </div>
    </div>
</div>

<script>
    // Global variables for showtime management
    let allShowtimes = [];
    let filteredShowtimes = [];
    let currentDateFilter = '';

    
    class SeatMap {
        constructor() {
            this.selectedSeats = [];
            this.defaultMaxSeats = 6;
            this.maxSeats = this.defaultMaxSeats;
            this.seatData = null;
            this.selectedShowtime = null;

            // DOM Elements
            this.loadingElement = document.getElementById('seatLoadingState');
            this.mapElement = document.getElementById('seatMap');
            this.legendElement = document.getElementById('seatLegend');
            this.infoElement = document.getElementById('selectedSeatsInfo');
            this.priceElement = document.getElementById('selectedSeatsPrice');
            this.requirementElement = document.getElementById('seatRequirementInfo');
            this.continueBtn = document.getElementById('continueToPaymentStep');
            this.autoCleanupOnLoad();
        }
        forceReset() {
            // Release every selected seat
            this.selectedSeats.forEach(async (seat) => {
                try {
                    await axios.post(`/api/seats/${seat.id}/release`);
                } catch (error) {
                    console.error('Seat could not be released during reset:', error);
                }
            });

            this.selectedSeats = [];
            this.seatData = null;
            this.selectedShowtime = null;
            this.updateSelectedSeatsInfo();
        }

        // Soft reset - just clear the UI without hitting the API
        softReset() {
            this.selectedSeats = [];
            this.seatData = null;
            this.selectedShowtime = null;
            this.updateSelectedSeatsInfo();
        }
        async autoCleanupOnLoad() {
            try {
                const response = await axios.post('/api/seats/auto-cleanup');
                if (response.data.cleaned_seats > 0) {
                    console.log(`🧹 ${response.data.cleaned_seats} expired seats cleaned up`);
                }
            } catch (error) {
                console.error('Auto cleanup failed:', error);
            }
        }

        // toggleSeat - persist via API and allow cancellations
        async toggleSeat(seatId, seatCode) {

            const existingIndex = this.selectedSeats.findIndex(s => s.id == seatId);

            if (existingIndex !== -1) {
                // Cancel seat - release it via the API
                try {
                    const response = await axios.post(`/api/seats/${seatId}/release`);

                    if (response.data.success) {
                        // Cancelled successfully
                        this.selectedSeats.splice(existingIndex, 1);
                        console.log(`Seat ${seatCode} released`);
                    } else {
                        alert('Seat could not be cancelled!');
                        return;
                    }
                } catch (error) {
                    console.error('Seat cancellation error:', error);
                    alert('Seat could not be cancelled!');
                    return;
                }
            } else {
                // Select seat - reserve via the API
                if (this.selectedSeats.length >= this.maxSeats) {
                    alert(`You can select at most ${this.maxSeats} seats!`);
                    return;
                }

                try {
                    const response = await axios.post(`/api/showtimes/${this.selectedShowtime.id}/reserve`, {
                        seat_id: seatId
                    });

                    if (response.data.success) {
                        // Reserved successfully
                        this.selectedSeats.push({ id: seatId, code: seatCode });
                        console.log(`Seat ${seatCode} reserved`);

                        // Auto release after 10 minutes
                        setTimeout(() => {
                            this.autoReleaseSeat(seatId, seatCode);
                        }, 10 * 60 * 1000); // 10 dakika
                    } else {                
                        alert('Seat could not be reserved! Someone else may have taken it.');

                        return;
                    }
                } catch (error) {
                    console.error('Seat reservation error:', error);
                    if (error.response?.status === 400) {
                        alert('This seat has already been selected by someone else!');
                    } else {
                        alert('Seat could not be reserved!');
                    }
                    return;
                }
            }

            // Refresh UI
            await this.loadSeats(this.selectedShowtime.id); // Pull latest status
            this.updateSelectedSeatsInfo();

            setTimeout(() => {
                const clickedSeat = document.querySelector(`button[onclick*="${seatId}"]`);
                if (clickedSeat) {
                    clickedSeat.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }, 100);


        }


        // Automatic release (after 10 minutes)
        async autoReleaseSeat(seatId, seatCode) {
            const seatIndex = this.selectedSeats.findIndex(s => s.id == seatId);

            // Release only if the seat is still pending locally
            if (seatIndex !== -1) {
                try {
                    await axios.post(`/api/seats/${seatId}/release`);
                    this.selectedSeats.splice(seatIndex, 1);

                    console.log(`Seat ${seatCode} auto released (10 minutes elapsed)`);

                    // Refresh UI
                    await this.loadSeats(this.selectedShowtime.id);
                    this.updateSelectedSeatsInfo();

                    // Notify user
                    alert(`Seat ${seatCode} was released because the hold expired!`);

                } catch (error) {
                    console.error('Automatic release error:', error);
                }
            }
        }

        // loadSeats metodu
        async loadSeats(showtimeId) {
            try {
                // Run cleanup before fetching seats
                await this.autoCleanupOnLoad();

                this.showLoading();

                const response = await axios.get(`/api/showtimes/${showtimeId}/available-seats`);
                this.seatData = response.data.data;

                // Split by status (new API response)
                if (this.seatData.seats) {
                    this.renderSeatMapWithStatus();
                } else {
                    // Fallback for legacy format
                    this.renderSeatMap();
                }

                this.showSeatMap();

            } catch (error) {
                console.error('Seats could not be loaded:', error);
                this.renderMockSeatMap();
                this.showSeatMap();
            }
        }

        // Highlight selected seats in blue
        renderSeatMapWithStatus() {
            const { available = [], occupied = [], pending = [] } = this.seatData.seats;

            // Combine all seats
            const allSeats = [
                ...available.map(s => ({ ...s, status: 'available' })),
                ...occupied.map(s => ({ ...s, status: 'occupied' })),
                ...pending.map(s => ({ ...s, status: 'pending' }))
            ];

            const seatsByRow = {};
            allSeats.forEach(seat => {
                if (!seatsByRow[seat.row]) {
                    seatsByRow[seat.row] = [];
                }
                seatsByRow[seat.row].push(seat);
            });

            let html = '';
            Object.keys(seatsByRow).sort().forEach(row => {
                html += `<div class="flex justify-center items-center space-x-2 mb-2">`;
                html += `<div class="w-8 text-center font-bold text-white">${row}</div>`;

                seatsByRow[row].sort((a, b) => a.number - b.number).forEach(seat => {
                    // Check whether we already selected this seat
                    const isMySelected = this.selectedSeats.some(s => s.id == seat.id);

                    let bgColor = 'style="background-color: #cbcbcb;"';
                    let hoverClass = '';
                    let clickHandler = '';
                    let statusText = this.getStatusText(seat.status);

                    switch (seat.status) {
                        case 'Blank':
                            bgColor = 'style="background-color: #10b981;"';
                            hoverClass = 'hover:opacity-80';
                            clickHandler = `onclick="window.seatMap.toggleSeat(${seat.id}, '${seat.row}${seat.number}')"`;
                            break;
                        case 'Filled':
                            bgColor = 'style="background-color: #cbcbcb;"';
                            statusText = 'Sold';
                            break;
                        case 'In Another Basket':
                            // If we own this seat, allow cancel
                            if (isMySelected) {
                                bgColor = 'style="background-color: #f8e71c;"';
                                hoverClass = 'hover:opacity-80';
                                clickHandler = `onclick="window.seatMap.toggleSeat(${seat.id}, '${seat.row}${seat.number}')"`;
                                statusText = 'Selected (Cancelable)';
                            } else {
                                bgColor = 'style="background-color: #ff4061;"';
                                statusText = 'Reserved (Someone else)';
                            }
                            break;
                        // Backward compatibility
                        case 'available':
                            bgColor = 'style="background-color: #10b981;"';
                            hoverClass = 'hover:opacity-80';
                            clickHandler = `onclick="window.seatMap.toggleSeat(${seat.id}, '${seat.row}${seat.number}')"`;
                            break;
                        case 'occupied':
                            bgColor = 'style="background-color: #cbcbcb;"';
                            statusText = 'Sold';
                            break;
                        case 'pending':
                            // If we own this seat, allow cancel
                            if (isMySelected) {
                                bgColor = 'style="background-color: #f8e71c;"';
                                hoverClass = 'hover:opacity-80';
                                clickHandler = `onclick="window.seatMap.toggleSeat(${seat.id}, '${seat.row}${seat.number}')"`;
                                statusText = 'Selected (Cancelable)';
                            } else {
                                bgColor = 'style="background-color: #ff4061;"';
                                statusText = 'Reserved (Someone else)';
                            }
                            break;
                    }

                    html += `
                        <button class="seat w-8 h-8 ${hoverClass} text-white text-xs rounded-lg font-bold transition-all transform hover:scale-110"
                                ${bgColor}
                                ${clickHandler}
                                title="${seat.row}${seat.number} - ${statusText}">
                            ${seat.row}${seat.number}
                        </button>
                    `;
                });

                html += `</div>`;
            });

            this.mapElement.innerHTML = html;
        }

        
        renderSeatMap() {
            const allSeats = [...this.seatData.available_seats, ...this.seatData.sold_seats];
            const seatsByRow = {};

            // Group seats by row
            allSeats.forEach(seat => {
                if (!seatsByRow[seat.row]) {
                    seatsByRow[seat.row] = [];
                }
                seatsByRow[seat.row].push(seat);
            });

            let html = '';
            Object.keys(seatsByRow).sort().forEach(row => {
                html += `<div class="flex justify-center items-center space-x-2 mb-2">`;
                html += `<div class="w-8 text-center font-bold text-white">${row}</div>`;

                seatsByRow[row].sort((a, b) => a.number - b.number).forEach(seat => {
                    const isAvailable = this.seatData.available_seats.some(s => s.id === seat.id);
                    const isSelected = this.selectedSeats.some(s => s.id === seat.id);

                    let bgColor = 'style="background-color: #cbcbcb;"';
                    let hoverClass = '';
                    let clickHandler = 'disabled';

                    if (isAvailable) {
                        bgColor = 'style="background-color: #10b981;"';
                        hoverClass = 'hover:opacity-80';
                        clickHandler = `onclick="window.seatMap.toggleSeat(${seat.id}, '${seat.row}${seat.number}')"`;
                    }

                    if (isSelected) {
                        bgColor = 'style="background-color: #f8e71c;"';
                        hoverClass = 'hover:opacity-80';
                    }

                    html += `
                        <button class="seat w-8 h-8 ${hoverClass} text-white text-xs rounded-lg font-bold transition-all transform hover:scale-110 cursor-pointer"
                                ${bgColor}
                                ${isAvailable ? clickHandler : 'disabled'}
                                title="${seat.row}${seat.number} - ${isAvailable ? 'Available' : 'Taken'}">
                            ${seat.row}${seat.number}
                        </button>
                    `;
                });

                html += `</div>`;
            });

            this.mapElement.innerHTML = html;
        }

        // renderMockSeatMap
        renderMockSeatMap() {
            const rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
            const seatsPerRow = 12;

            let html = '';
            rows.forEach(row => {
                html += `<div class="flex justify-center items-center space-x-2 mb-2">`;
                html += `<div class="w-8 text-center font-bold text-white">${row}</div>`;

                for (let seat = 1; seat <= seatsPerRow; seat++) {
                    const seatId = `${row}${seat}`;
                    const isOccupied = Math.random() < 0.3;
                    const isSelected = this.selectedSeats.some(s => s.id === seatId);

                    let bgColor = 'style="background-color: #10b981;"';
                    let hoverClass = 'hover:opacity-80';

                    if (isOccupied) {
                        bgColor = 'style="background-color: #cbcbcb;"';
                        hoverClass = '';
                    }

                    if (isSelected) {
                        bgColor = 'style="background-color: #f8e71c;"';
                        hoverClass = 'hover:opacity-80';
                    }

                    html += `
                        <button class="seat w-8 h-8 ${hoverClass} text-white text-xs rounded-lg font-bold transition-all transform hover:scale-110 cursor-pointer"
                                ${bgColor}
                                ${!isOccupied ? `onclick="window.seatMap.toggleSeat('${seatId}', '${seatId}')"` : 'disabled'}
                                title="${seatId} - ${isOccupied ? 'Taken' : 'Available'}">
                            ${row}${seat}
                        </button>
                    `;
                }

                html += `</div>`;
            });

            this.mapElement.innerHTML = html;
        }

        // Additional helpers
        getStatusText(status) {
            switch (status) {
                case 'Blank': return 'Blank';
                case 'Filled': return 'Filled';
                case 'In Another Basket': return 'In Another Basket';
                // Backward compatibility
                case 'available': return 'Available';
                case 'occupied': return 'Sold';
                case 'pending': return 'Reserved';
                default: return 'Unknown';
            }
        }

        updateSelectedSeatsInfo() {
            const requiredSeats = window.paymentForm?.getTotalTicketCount() || 0;

            if (this.selectedSeats.length === 0) {
                this.infoElement.textContent = 'No seats selected';
                if (this.requirementElement) {
                    this.requirementElement.textContent = requiredSeats > 0
                        ? `Please select ${requiredSeats} seats.`
                        : 'Select ticket types before continuing.';
                    this.requirementElement.classList.remove('text-emerald-300', 'text-red-400');
                    this.requirementElement.classList.add('text-gray-300');
                }
                this.priceElement.classList.add('hidden');
                if (this.continueBtn) {
                    this.continueBtn.classList.add('hidden');
                }
            } else {
                const seatCodes = this.selectedSeats.map(s => s.code).join(', ');
                this.infoElement.textContent = `${this.selectedSeats.length} seats selected: ${seatCodes}`;

                // Show estimated price
                if (this.selectedShowtime && this.selectedShowtime.price) {
                    const estimatedTotal = this.selectedSeats.length * this.selectedShowtime.price;
                    this.priceElement.classList.remove('hidden');
                }

                if (this.requirementElement) {
                    if (requiredSeats > 0 && this.selectedSeats.length !== requiredSeats) {
                        const diff = requiredSeats - this.selectedSeats.length;
                        const text = diff > 0
                            ? `Select ${diff} more seats to continue.`
                            : `Please release ${Math.abs(diff)} seats.`;
                        this.requirementElement.textContent = text;
                        this.requirementElement.classList.remove('text-emerald-300', 'text-gray-300');
                        this.requirementElement.classList.add('text-red-400');
                        if (this.continueBtn) {
                            this.continueBtn.classList.add('hidden');
                        }
                    } else if (requiredSeats > 0) {
                        this.requirementElement.textContent = 'Great! Ticket and seat counts match.';
                        this.requirementElement.classList.remove('text-red-400', 'text-gray-300');
                        this.requirementElement.classList.add('text-emerald-300');
                        if (this.continueBtn) {
                            this.continueBtn.classList.remove('hidden');
                        }
                    } else {
                        this.requirementElement.textContent = 'Ticket type not selected yet.';
                        this.requirementElement.classList.remove('text-emerald-300', 'text-red-400');
                        this.requirementElement.classList.add('text-gray-300');
                        if (this.continueBtn) {
                            this.continueBtn.classList.add('hidden');
                        }
                    }
                }
            }
        }

        showLoading() {
            this.loadingElement.classList.remove('hidden');
            this.mapElement.classList.add('hidden');
            this.legendElement.classList.add('hidden');
        }

        showSeatMap() {
            this.loadingElement.classList.add('hidden');
            this.mapElement.classList.remove('hidden');

            // Updated legend
            this.legendElement.innerHTML = `
    <div class="bg-white/10 p-4 rounded-xl">
        <div class="flex flex-wrap justify-center gap-3 sm:gap-6">
            <div class="flex items-center">
                <div class="w-4 h-4 rounded mr-2" style="background-color: #f8e71c;"></div>
                <span class="text-white text-xs sm:text-sm">Your Selection</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 rounded mr-2" style="background-color: #ff4061;"></div>
                <span class="text-white text-xs sm:text-sm">In Another Basket</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 rounded mr-2" style="background-color: #cbcbcb;"></div>
                <span class="text-white text-xs sm:text-sm">Filled</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 rounded mr-2" style="background-color: #10b981;"></div>
                <span class="text-white text-xs sm:text-sm">Blank</span>
            </div>
        </div>
    </div>
            `;
            this.legendElement.classList.remove('hidden');
        }

        reset() {
            // Release all selected seats
            this.selectedSeats.forEach(async (seat) => {
                try {
                    await axios.post(`/api/seats/${seat.id}/release`);
                } catch (error) {
                    console.error('Seat could not be released during reset:', error);
                }
            });

            this.selectedSeats = [];
            this.seatData = null;
            this.selectedShowtime = null;
            this.updateSelectedSeatsInfo();
        }

        getSelectedSeats() {
            return this.selectedSeats;
        }

        setShowtime(showtime) {
            this.selectedShowtime = showtime;
        }

        async setSeatLimit(limit) {
            const normalizedLimit = Math.min(limit || this.defaultMaxSeats, this.defaultMaxSeats);
            this.maxSeats = normalizedLimit > 0 ? normalizedLimit : this.defaultMaxSeats;

            await this.enforceSeatLimit();
            this.updateSelectedSeatsInfo();
        }

        async enforceSeatLimit() {
            while (this.selectedSeats.length > this.maxSeats) {
                const seat = this.selectedSeats.pop();
                try {
                    await axios.post(`/api/seats/${seat.id}/release`);
                    console.log(`Seat ${seat.code} was released because the limit was reduced`);
                } catch (error) {
                    console.error('Seat release failed:', error);
                }
            }
        }
        // SeatMap helper methods
        async manualRefresh() {
            if (!this.selectedShowtime) {
                alert('Please select a showtime first!');
                return;
            }

            // Preserve current scroll position
            const currentScrollPosition = window.pageYOffset;

            try {
                // Show loading state
                this.showLoading();

                // Reload seats
                await this.loadSeats(this.selectedShowtime.id);

                // Success message
                console.log('🔄 Seats refreshed manually');

                // Restore scroll position
                setTimeout(() => {
                    window.scrollTo(0, currentScrollPosition);
                    this.showSeatMap();
                }, 100);

            } catch (error) {
                console.error('Manual refresh error:', error);
                alert('Something went wrong while refreshing seats!');
                this.showSeatMap();
            }
        }
    }

    // Showtime management functions 
    function initializeDateFilter() {
        const today = new Date().toISOString().split('T')[0];
        const dateFilter = document.getElementById('dateFilter');
        dateFilter.min = today;
        dateFilter.value = today;
        currentDateFilter = today;
        filterShowtimesByDate(today);
    }

    function filterShowtimesByDate(date) {
        currentDateFilter = date;

        if (!date) {
            filteredShowtimes = [...allShowtimes];
        } else {
            filteredShowtimes = allShowtimes.filter(showtime => {
                const showtimeDate = new Date(showtime.start_time).toISOString().split('T')[0];
                return showtimeDate === date;
            });
        }

        updateShowtimeCount();

        if (filteredShowtimes.length === 0) {
            showEmptyShowtimes();
        } else {
            renderShowtimes(filteredShowtimes);
            showShowtimeGrid();
        }
    }

    function clearShowtimeFilters() {
        currentDateFilter = '';
        document.getElementById('dateFilter').value = '';
        filteredShowtimes = [...allShowtimes];
        updateShowtimeCount();
        renderShowtimes(filteredShowtimes);
        showShowtimeGrid();
    }

    function updateShowtimeCount() {
        const countElement = document.getElementById('filteredShowtimeCount');
        if (countElement) {
            // Önce DOM'daki gerçek showtime kartlarını say (kullanıcı ne görüyorsa onu göster)
            let count = 0;
            try {
                count = document.querySelectorAll('#showtimeGrid .glass-effect').length;
            } catch (e) {
                // ignore DOM errors
            }

            // Eğer DOM'da hiç kart yoksa, yedek olarak filteredShowtimes uzunluğunu kullan
            if (count === 0 && Array.isArray(filteredShowtimes)) {
                count = filteredShowtimes.length;
            }

            countElement.textContent = count;
        }
    }

    function showShowtimeLoading() {
        document.getElementById('showtimeLoadingState').classList.remove('hidden');
        document.getElementById('showtimeGrid').classList.add('hidden');
        document.getElementById('showtimeEmptyState').classList.add('hidden');
    }

    function showShowtimeGrid() {
        document.getElementById('showtimeLoadingState').classList.add('hidden');
        document.getElementById('showtimeGrid').classList.remove('hidden');
        document.getElementById('showtimeEmptyState').classList.add('hidden');
    }

    function showEmptyShowtimes() {
        document.getElementById('showtimeLoadingState').classList.add('hidden');
        document.getElementById('showtimeGrid').classList.add('hidden');
        document.getElementById('showtimeEmptyState').classList.remove('hidden');
    }

    function renderShowtimes(showtimes) {
        const showtimeGrid = document.getElementById('showtimeGrid');
        let html = '';

        // Group showtimes by date
        const groupedByDate = {};
        showtimes.forEach(showtime => {
            const date = new Date(showtime.start_time).toISOString().split('T')[0];
            if (!groupedByDate[date]) {
                groupedByDate[date] = [];
            }
            groupedByDate[date].push(showtime);
        });

        // Sort dates
        const sortedDates = Object.keys(groupedByDate).sort();

        if (sortedDates.length === 0) {
            html = '<div class="col-span-full text-center text-gray-400">No showtimes match your filters.</div>';
        } else {
            sortedDates.forEach(date => {
                const dateShowtimes = groupedByDate[date];
                const formattedDate = new Date(date).toLocaleDateString('tr-TR', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

                html += `
                    <div class="col-span-full mb-4">
                        <h4 class="text-lg font-bold text-white mb-3 border-b border-white/20 pb-2">
                            <i class="fas fa-calendar mr-2 text-purple-400"></i>${formattedDate}
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                `;

                dateShowtimes.forEach(showtime => {
                    // API'den gelen zaman string'ini al
                    let timeStr = showtime.start_time;
                    
                    // Parse API time without converting timezone
                    const startTime = new Date(timeStr);
                    
                    // Extract hour/minute without timezone adjustments
                    const hours = String(startTime.getUTCHours()).padStart(2, '0');
                    const minutes = String(startTime.getUTCMinutes()).padStart(2, '0');
                    const timeString = `${hours}:${minutes}`;
                    
                    const dateString = startTime.toLocaleDateString('tr-TR', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    });
                    const formattedDateTime = `${dateString} ${timeString}`;

                    html += `
                        <div class="glass-effect rounded-xl p-4 card-hover cursor-pointer" 
                             onclick="selectShowtimeForTicket(${showtime.id}, '${formattedDateTime}', '${showtime.hall.name}', ${showtime.price || 45})">
                            <div class="text-center">
                                <h4 class="text-lg font-semibold text-white mb-2">${showtime.hall.name}</h4>
                                <p class="text-emerald-400 font-bold text-xl mb-1">${timeString}</p>
                                <p class="text-purple-300 text-sm mb-2">
                                    <i class="fas fa-clock mr-1"></i>
                                    ${startTime.toLocaleDateString('tr-TR')}
                                </p>
                                <p class="text-yellow-400 font-medium">
                                    <i class="fas fa-ticket-alt mr-1"></i>₺${showtime.price || 45}/person
                                </p>
                                <div class="mt-2 text-xs text-gray-400">
                                    <i class="fas fa-couch mr-1"></i>
                                    ${getAvailableSeatsText(showtime)}
                                </div>
                            </div>
                        </div>
                    `;
                });

                html += `
                        </div>
                    </div>
                `;
            });
        }

        showtimeGrid.innerHTML = html;

        // Ensure count is updated after rendering (avoids brief mismatch showing 0)
        try { updateShowtimeCount(); } catch (e) { console.error('updateShowtimeCount failed:', e); }
    }

    function getAvailableSeatsText(showtime) {
        const totalSeats = showtime.hall?.total_seats || 100;
        const soldSeats = showtime.sold_seats || Math.floor(Math.random() * 30);
        const availableSeats = totalSeats - soldSeats;

        return `${availableSeats} seats available`;
    }

    async function loadShowtimesForCinema() {
        try {
            showShowtimeLoading();

            if (!selectedMovie || !selectedCinema) {
                throw new Error('Movie or cinema not selected');
            }

            // MovieController endpoint'ini kullan (cinema_id parametresi ile)
            const response = await axios.get(`/api/movies/${selectedMovie.id}/showtimes`, {
                params: { cinema_id: selectedCinema.id }
            });

            // Response format: { success: true, data: [...] }
            let showtimes = [];
            if (response.data.success && response.data.data) {
                if (Array.isArray(response.data.data)) {
                    showtimes = response.data.data;
                }
            }

            allShowtimes = showtimes;
            filteredShowtimes = [...allShowtimes];

            initializeDateFilter();
            updateShowtimeCount();

            if (allShowtimes.length === 0) {
                showEmptyShowtimes();
            } else {
                renderShowtimes(filteredShowtimes);
                showShowtimeGrid();
            }

        } catch (error) {
            console.error('Showtimes could not be loaded:', error);
            allShowtimes = [];
            filteredShowtimes = [];
            showEmptyShowtimes();
        }
    }

    function renderMockShowtimes() {
        const now = new Date();
        const mockShowtimes = [];

        // Generate mock showtimes for next 3 days
        for (let day = 0; day < 3; day++) {
            const baseDate = new Date(now);
            baseDate.setDate(now.getDate() + day);

            // Generate 3-4 showtimes per day
            const showtimesPerDay = 3 + Math.floor(Math.random() * 2);
            for (let i = 0; i < showtimesPerDay; i++) {
                const showtime = new Date(baseDate);
                showtime.setHours(14 + (i * 3), 0, 0, 0); // 14:00, 17:00, 20:00, 23:00

                mockShowtimes.push({
                    id: day * 10 + i + 1,
                    start_time: showtime.toISOString(),
                    hall: {
                        name: `Salon ${i + 1}`,
                        total_seats: 100
                    },
                    price: 45 + (i * 5),
                    sold_seats: Math.floor(Math.random() * 30)
                });
            }
        }

        allShowtimes = mockShowtimes;
        filteredShowtimes = [...mockShowtimes];

        initializeDateFilter();
        updateShowtimeCount();
        renderShowtimes(filteredShowtimes);
    }

    async function selectShowtimeForTicket(showtimeId, startTime, hallName, price) {
        // Normalize and format startTime for display. Accepts ISO or already-formatted strings.
        let displayStartTime = startTime;
        let isoStart = startTime;
        try {
            const parsed = new Date(startTime);
            if (!isNaN(parsed.getTime())) {
                const dateStr = parsed.toLocaleDateString('tr-TR', { day: '2-digit', month: '2-digit', year: 'numeric' });
                const hours = String(parsed.getUTCHours()).padStart(2, '0');
                const minutes = String(parsed.getUTCMinutes()).padStart(2, '0');
                displayStartTime = `${dateStr} ${hours}:${minutes}`;
                isoStart = parsed.toISOString();
            }
        } catch (e) {
            // fallback: use raw string
            console.error('Failed to parse showtime startTime:', e);
        }

        selectedShowtime = {
            id: showtimeId,
            startTimeISO: isoStart,
            startTime: displayStartTime,
            hall: hallName,
            price: price
        };

        currentTicketStep = 4;
        updateTicketSteps();

        // Show selected showtime info (use formatted startTime)
        document.getElementById('selectedShowtimeInfo').innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-film text-yellow-400 text-lg"></i>
                    <div>
                        <h6 class="text-white font-medium text-sm">Film</h6>
                        <p class="text-purple-300 text-xs">${selectedMovie.title}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <i class="fas fa-building text-blue-400 text-lg"></i>
                    <div>
                        <h6 class="text-white font-medium text-sm">Cinema</h6>
                        <p class="text-blue-300 text-xs">${selectedCinema.name}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <i class="fas fa-clock text-purple-400 text-lg"></i>
                    <div>
                        <h6 class="text-white font-medium text-sm">Showtime</h6>
                        <p class="text-emerald-400 text-xs">${displayStartTime} - ${hallName}</p>
                    </div>
                </div>
            </div>
        `;

        // Set showtime in seat map and load seats
        window.seatMap.setShowtime(selectedShowtime);
        await window.seatMap.loadSeats(showtimeId);

        // Ensure ticket types/prices are loaded for this showtime so service fees can be calculated
        try {
            if (window.paymentForm && typeof window.paymentForm.loadTicketTypes === 'function') {
                await window.paymentForm.loadTicketTypes(showtimeId);
                // Recalculate totals after loading ticket types
                await window.paymentForm.updateTotalPrice();
            }
        } catch (e) {
            console.error('Failed to load ticket types after selecting showtime:', e);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // SeatMap'i initialize et
        window.seatMap = new SeatMap();

        // Genel cleanup
        axios.post('/api/seats/auto-cleanup')
            .then(response => {
                if (response.data.cleaned_seats > 0) {
                    console.log(`🧹 Page load cleanup: ${response.data.cleaned_seats} seats cleaned`);
                }
            })
            .catch(error => console.error('Page load cleanup failed:', error));
    });
    setInterval(async () => {
        try {
            const response = await axios.post('/api/seats/auto-cleanup');
            if (response.data.cleaned_seats > 0) {
                console.log(`🧹 Periodic cleanup: ${response.data.cleaned_seats} seats cleaned`);

                // If user is on the seat step, refresh the map
                if (window.seatMap && window.seatMap.selectedShowtime) {
                    await window.seatMap.loadSeats(window.seatMap.selectedShowtime.id);
                }
            }
        } catch (error) {
            console.error('Periodic cleanup failed:', error);
        }
    }, 2 * 60 * 1000); // 2 dakika

    // Cleanup when the page regains focus
    document.addEventListener('visibilitychange', async function () {
        if (!document.hidden && window.seatMap) {
            try {
                const response = await axios.post('/api/seats/auto-cleanup');
                if (response.data.cleaned_seats > 0) {
                    console.log(`🧹 Focus cleanup: ${response.data.cleaned_seats} seats cleaned`);

                    // Refresh the seat map
                    if (window.seatMap.selectedShowtime) {
                        await window.seatMap.loadSeats(window.seatMap.selectedShowtime.id);
                    }
                }
            } catch (error) {
                console.error('Focus cleanup failed:', error);
            }
        }
    });

</script>