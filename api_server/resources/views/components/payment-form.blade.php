<!-- Step 4: Ticket Type Selection -->
<div id="ticketStep4" class="ticket-step hidden">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-2xl font-bold text-white text-center flex-1">
            <i class="fas fa-users mr-2 text-orange-400"></i>Choose Ticket Types
        </h3>
        <button onclick="goBackToStep(3)"
            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Change Showtime
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Ticket Types -->
        <div class="bg-white/10 p-6 rounded-xl">
            <h4 class="text-xl font-semibold text-white mb-4">
                <i class="fas fa-ticket-alt mr-2"></i>Ticket Types
            </h4>

            <!-- Loading State -->
            <div id="ticketTypesLoading" class="text-center py-8">
                <div class="loading w-8 h-8 border-4 border-orange-400 border-t-transparent rounded-full mx-auto mb-4">
                </div>
                <p class="text-white text-sm">Calculating prices...</p>
            </div>

            <!-- Ticket Types Container -->
            <div id="ticketTypesContainer" class="space-y-4 hidden">
                <!-- Ticket types load here -->
            </div>

            <!-- Summary -->
            <div class="mt-6 pt-4 border-t border-white/20">
                <div class="flex justify-between items-center text-lg font-bold text-white">
                    <span>Selected Tickets:</span>
                    <span id="selectedTicketCount" class="text-emerald-400">0</span>
                </div>
                <div id="ticketTypeSummary" class="mt-2 text-sm text-gray-300">No tickets selected</div>
            </div>

            <!-- Continue Button -->
            <button id="continueToSeatSelection" onclick="goToSeatSelection()"
                class="w-full mt-6 bg-emerald-500 hover:bg-emerald-600 text-white py-3 rounded-xl font-bold disabled:bg-gray-600 disabled:cursor-not-allowed transition-all"
                disabled>
                <i class="fas fa-arrow-right mr-2"></i>Continue to Seat Selection
            </button>
        </div>

        <!-- Fiyat Bilgileri -->
        <div class="bg-white/10 p-6 rounded-xl">
            <h4 class="text-xl font-semibold text-white mb-4">
                <i class="fas fa-calculator mr-2"></i>Service Fee
            </h4>
            <!-- Base Prices -->
            <div id="priceInfo" class="space-y-3 text-white mb-4">
                <!-- Pricing info loads here -->
            </div>
            <!-- Tax Calculation -->
            <div id="taxCalculationSection" class="border-t border-white/20 pt-4 hidden">
                <h5 class="text-white font-medium mb-3">
                    <i class="fas fa-receipt mr-1"></i>Service Fee Details
                </h5>

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-white">
                        <span>Ara Toplam:</span>
                        <span id="subtotalAmount">₺0</span>
                    </div>

                    <div id="taxBreakdown" class="space-y-1">
                        <!-- Fee breakdown loads here -->
                    </div>

                    <div class="border-t border-white/20 pt-2 mt-2">
                        <div class="flex justify-between text-white font-bold">
                            <span>Total Service Fee:</span>
                            <span id="totalTaxAmount" class="text-red-400">₺0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grand Total -->
            <div class="mt-6 pt-4 border-t border-white/20">
                <div class="flex justify-between items-center text-xl font-bold text-white">
                    <span>Grand Total:</span>
                    <span id="totalPricePreview" class="text-emerald-400">₺0</span>
                </div>
                <div id="taxSummaryInfo" class="text-xs text-gray-400 mt-1">
                    Service fee not calculated
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Step 6: Customer Info & Payment -->
<div id="ticketStep6" class="ticket-step hidden">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-2xl font-bold text-white text-center flex-1">
            <i class="fas fa-credit-card mr-2 text-yellow-400"></i>Customer Details & Payment
        </h3>
        <button onclick="goBackToStep(5)"
            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Change Seats
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Customer Information -->
        <div class="bg-white/10 p-6 rounded-xl">
            <h4 class="text-xl font-semibold text-white mb-4">
                <i class="fas fa-user mr-2"></i>Customer Information
            </h4>

            <form id="customerForm" class="space-y-4">
                <div>
                    <label class="block text-white text-sm font-medium mb-2">
                        <i class="fas fa-user mr-1"></i>Full Name *
                    </label>
                    <input type="text" id="customerName" placeholder="Enter your full name" required
                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-300 focus:bg-white/20 focus:border-emerald-400 transition-all">
                </div>

                <div>
                    <label class="block text-white text-sm font-medium mb-2">
                        <i class="fas fa-envelope mr-1"></i>Email *
                    </label>
                    <input type="email" id="customerEmail" placeholder="Enter your email address" required
                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-300 focus:bg-white/20 focus:border-emerald-400 transition-all">
                </div>

                <div>
                    <label class="block text-white text-sm font-medium mb-2">
                        <i class="fas fa-phone mr-1"></i>Phone *
                    </label>
                    <input type="tel" id="customerPhone" placeholder="Enter your phone number" required
                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-300 focus:bg-white/20 focus:border-emerald-400 transition-all">
                </div>

                <div>
                    <label class="block text-white text-sm font-medium mb-2">
                        <i class="fas fa-credit-card mr-1"></i>Payment Method
                    </label>
                    <select id="paymentMethod"
                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:bg-white/20 focus:border-emerald-400 transition-all">
                        <option value="card">💳 Credit Card</option>
                        <option value="online">🌐 Online Payment</option>
                    </select>
                </div>

                <!-- Card Details -->
                <div id="cardDetails" class="mt-4 space-y-3 hidden">
                    <div>
                        <label class="block text-white text-sm font-medium mb-1">
                            <i class="fas fa-id-card mr-1"></i>Name on Card
                        </label>
                        <input type="text" id="cardName" placeholder="Name on card"
                            class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:bg-white/20 focus:border-emerald-400 transition-all">
                    </div>
                    <div>
                        <label class="block text-white text-sm font-medium mb-1">
                            <i class="fas fa-credit-card mr-1"></i>Card Number
                        </label>
                        <input type="tel" id="cardNumber" placeholder="XXXX XXXX XXXX XXXX" maxlength="19"
                               inputmode="numeric" pattern="[0-9 ]*"
                               oninput="formatCardNumber(this)"
                            class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:bg-white/20 focus:border-emerald-400 transition-all">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-white text-sm font-medium mb-1">
                                <i class="fas fa-calendar-alt mr-1"></i>Expiry (MM/YY)
                            </label>
                            <input type="tel" id="cardExpiry" placeholder="MM/YY" maxlength="5"
                                   inputmode="numeric" pattern="[0-9/]*"
                                   oninput="formatCardExpiry(this)"
                                class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:bg-white/20 focus:border-emerald-400 transition-all">
                        </div>
                        <div>
                            <label class="block text-white text-sm font-medium mb-1">
                                <i class="fas fa-lock mr-1"></i>CVV
                            </label>
                            <input type="password" id="cardCvv" placeholder="CVV" maxlength="4"
                                   inputmode="numeric" pattern="[0-9]*"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,4);"
                                class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:bg-white/20 focus:border-emerald-400 transition-all">
                        </div>
                    </div>
                </div>

                <!-- Online Payment Providers -->
                <div id="onlineProviders" class="mt-4 space-y-2 hidden">
                    <p class="text-sm text-gray-200 mb-2">Choose an online payment provider:</p>
                    <button type="button"
                        class="w-full flex items-center justify-between px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-white text-sm"
                        data-provider="paypal">
                        <span><i class="fab fa-paypal mr-2 text-blue-400"></i>PayPal</span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </button>
                    <button type="button"
                        class="w-full flex items-center justify-between px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-white text-sm"
                        data-provider="stripe">
                        <span><i class="fas fa-credit-card mr-2 text-purple-400"></i>Stripe</span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </button>
                    <button type="button"
                        class="w-full flex items-center justify-between px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-white text-sm"
                        data-provider="apple_pay">
                        <span><i class="fab fa-apple mr-2 text-gray-200"></i>Apple Pay</span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </button>
                    <button type="button"
                        class="w-full flex items-center justify-between px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-white text-sm"
                        data-provider="google_pay">
                        <span><i class="fab fa-google-pay mr-2 text-green-400"></i>Google Pay</span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </button>
                </div>

                <div class="flex items-center mt-4">
                    <input type="checkbox" id="termsAccepted" required
                        class="w-4 h-4 text-emerald-600 bg-transparent border-white/20 rounded focus:ring-emerald-500">
                    <label for="termsAccepted" class="ml-2 text-sm text-gray-300">
                        I accept the <a href="#" class="text-emerald-400 hover:text-emerald-300">Terms of Use</a>
                    </label>
                </div>
            </form>
        </div>

        <!-- Order Summary -->
        <div class="bg-white/10 p-6 rounded-xl">
            <h4 class="text-xl font-semibold text-white mb-4">
                <i class="fas fa-receipt mr-2"></i>Order Summary
            </h4>

            <!-- Genel Bilgiler -->
            <div id="orderSummary" class="space-y-3 text-white mb-6">
                <!-- Order summary will be loaded here -->
            </div>

            <!-- Price Details -->
            <div class="bg-white/5 p-4 rounded-lg mb-4">
                <h5 class="text-white font-medium mb-3 border-b border-white/20 pb-2">
                    <i class="fas fa-calculator mr-1"></i>Price Breakdown
                </h5>

                <div id="finalPriceBreakdown" class="space-y-2 text-sm">
                    <!-- Detailed price breakdown loads here -->
                </div>
            </div>

            <!-- Final Total -->
            <div class="border-t border-white/20 pt-4">
                <div class="flex justify-between items-center text-xl font-bold text-white mb-2">
                    <span>Amount Due:</span>
                    <span id="totalPrice" class="text-emerald-400">₺0</span>
                </div>
                <div id="ticketCountInfo" class="text-sm text-gray-400">
                    Service fee not calculated
                </div>
            </div>

            <!-- Complete Sale Button -->
            <button id="completeSaleBtn" onclick="completeSale()"
                class="w-full mt-6 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white py-4 rounded-xl font-bold text-lg transition-all transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-check-circle mr-2"></i>Complete Payment
            </button>
            <!-- Security Note -->
            <div class="mt-4 text-center">
                <p class="text-xs text-gray-400">
                    <i class="fas fa-shield-alt mr-1"></i>
                    Your payment details are securely encrypted
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    function formatCardNumber(input) {
        let digits = input.value.replace(/[^0-9]/g, '').slice(0, 16);
        const groups = digits.match(/.{1,4}/g) || [];
        input.value = groups.join(' ');
    }

    function formatCardExpiry(input) {
        let digits = input.value.replace(/[^0-9]/g, '').slice(0, 4);
        if (digits.length >= 3) {
            input.value = digits.slice(0, 2) + '/' + digits.slice(2);
        } else {
            input.value = digits;
        }
    }
    // Payment Form JavaScript (refresh of the previous implementation)
    class PaymentForm {
        constructor() {
            this.selectedTicketTypes = {};
            this.ticketPrices = {};
            this.customerTypes = [];
            this.taxes = [];
            this.taxCalculation = null;

            // DOM Elements
            this.loadingElement = document.getElementById('ticketTypesLoading');
            this.typesContainer = document.getElementById('ticketTypesContainer');
            this.priceInfoElement = document.getElementById('priceInfo');
            this.countElement = document.getElementById('selectedTicketCount');
            this.summaryElement = document.getElementById('ticketTypeSummary');
            this.continueButton = document.getElementById('continueToSeatSelection');
            this.completeSaleBtn = document.getElementById('completeSaleBtn');

            // Tax Elements
            this.taxSectionElement = document.getElementById('taxCalculationSection');
            this.subtotalElement = document.getElementById('subtotalAmount');
            this.taxBreakdownElement = document.getElementById('taxBreakdown');
            this.totalTaxElement = document.getElementById('totalTaxAmount');
            this.taxSummaryElement = document.getElementById('taxSummaryInfo');

            // Load service fee definitions
            this.loadTaxes();
        }

        async loadTaxes() {
            try {
                const response = await axios.get('/api/taxes');
                this.taxes = response.data.data || [];
                console.log('Service fee list loaded:', this.taxes);
            } catch (error) {
                console.log('Service fee could not be loaded, falling back to defaults:', error);
                this.taxes = [
                    {
                        name: 'Service Fee',
                        type: 'fixed',
                        rate: '2.00',
                        status: 'active',
                        formatted_name: 'Service Fee (₺2)'
                    }
                ];
            }
        }

        async calculateTotalWithTaxes() {
            const subtotal = Object.entries(this.selectedTicketTypes).reduce((sum, [type, count]) => {
                return sum + (this.ticketPrices[type] * count);
            }, 0);

            const totalTickets = Object.values(this.selectedTicketTypes).reduce((sum, count) => sum + count, 0);

            if (totalTickets === 0) {
                this.taxCalculation = null;
                this.hideTaxSection();
                return;
            }

            try {
                const response = await axios.post('/api/taxes/calculate', {
                    subtotal: subtotal,
                    ticket_count: totalTickets
                });

                this.taxCalculation = response.data.data;
                this.showTaxSection();

            } catch (error) {
                console.log('Service fee calculation failed, computing manually:', error);

                const serviceFee = this.taxes.find(tax => tax.name === 'Service Fee');
                const feeRate = parseFloat(serviceFee?.rate ?? 2);
                const taxAmount = feeRate * totalTickets;

                this.taxCalculation = {
                    subtotal: subtotal,
                    taxes: [{
                        name: 'Service Fee',
                        type: 'fixed',
                        rate: feeRate,
                        amount: taxAmount,
                        formatted_name: `Service Fee (${feeRate} ₺ x ${totalTickets} tickets)`
                    }],
                    total_tax_amount: taxAmount,
                    total: subtotal + taxAmount,
                    ticket_count: totalTickets
                };

                this.showTaxSection();
            }
        }

        showTaxSection() {
            if (!this.taxCalculation) return;

            this.taxSectionElement.classList.remove('hidden');

            // Ara toplam
            this.subtotalElement.textContent = `₺${this.taxCalculation.subtotal.toFixed(2)}`;

            // Service fee details
            let taxHTML = '';
            this.taxCalculation.taxes.forEach(tax => {
                taxHTML += `
                <div class="flex justify-between text-gray-300">
                    <span>${tax.formatted_name || tax.name}:</span>
                    <span>₺${tax.amount.toFixed(2)}</span>
                </div>
            `;
            });
            this.taxBreakdownElement.innerHTML = taxHTML;

            // Toplam hizmet bedeli
            this.totalTaxElement.textContent = `₺${this.taxCalculation.total_tax_amount.toFixed(2)}`;
        }

        hideTaxSection() {
            this.taxSectionElement.classList.add('hidden');
        }

        async updateTotalPrice() {
            await this.calculateTotalWithTaxes();

            if (!this.taxCalculation) {
                document.getElementById('totalPricePreview').textContent = `₺0`;
                this.taxSummaryElement.textContent = 'Service fee not calculated';
                return;
            }

            document.getElementById('totalPricePreview').textContent = `₺${this.taxCalculation.total.toFixed(2)}`;

            // Service fee summary
            const taxInfo = this.taxCalculation.taxes.map(tax =>
                tax.formatted_name || `${tax.name}`
            ).join(' + ');

            this.taxSummaryElement.innerHTML = `
            <div>${taxInfo} dahil</div>
            <div class="text-xs">
                Ara Toplam: ₺${this.taxCalculation.subtotal.toFixed(2)} + 
                Service Fee: ₺${this.taxCalculation.total_tax_amount.toFixed(2)}
            </div>
        `;

            // Sync total price on the payment step as well
            const totalPriceElement = document.getElementById('totalPrice');
            if (totalPriceElement) {
                totalPriceElement.textContent = `₺${this.taxCalculation.total.toFixed(2)}`;
            }

            // Update ticket count
            const selectedSeats = window.seatMap?.getSelectedSeats() || [];
            const ticketCountElement = document.getElementById('ticketCountInfo');
            if (ticketCountElement && this.taxCalculation) {
                ticketCountElement.innerHTML = `
                <div>${taxInfo} dahil • ${selectedSeats.length} bilet</div>
                <div class="text-xs text-gray-500">
                    Ara Toplam: ₺${this.taxCalculation.subtotal.toFixed(2)} + 
                Service Fee: ₺${this.taxCalculation.total_tax_amount.toFixed(2)}
                </div>
            `;
            }
        }

        async generateOrderSummary() {
            const selectedSeats = window.seatMap?.getSelectedSeats() || [];
            await this.calculateTotalWithTaxes();

            // Genel bilgiler
            let html = `
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span>Film:</span>
                    <span class="font-medium">${selectedMovie?.title || 'N/A'}</span>
                </div>
                <div class="flex justify-between">
                    <span>Sinema:</span>
                    <span class="font-medium">${selectedCinema?.name || 'N/A'}</span>
                </div>
                <div class="flex justify-between">
                    <span>Seans:</span>
                    <span class="font-medium">${selectedShowtime?.startTime || 'N/A'}</span>
                </div>
                <div class="flex justify-between">
                    <span>Salon:</span>
                    <span class="font-medium">${selectedShowtime?.hall || 'N/A'}</span>
                </div>
                <div class="flex justify-between">
                    <span>Koltuklar:</span>
                    <span class="font-medium">${selectedSeats.map(s => s.code).join(', ')}</span>
                </div>
            </div>
        `;

            document.getElementById('orderSummary').innerHTML = html;

            // Price details
            if (this.taxCalculation) {
                let priceHTML = `
                <div class="space-y-2">
                    <h6 class="font-medium text-white border-b border-white/20 pb-1">Ticket Details:</h6>
            `;

                Object.entries(this.selectedTicketTypes)
                    .filter(([type, count]) => count > 0)
                    .forEach(([type, count]) => {
                        const typeObj = this.customerTypes.find(t => t.code === type);
                        priceHTML += `
                        <div class="flex justify-between text-sm">
                            <span>${typeObj?.name || type} × ${count}:</span>
                            <span>₺${(this.ticketPrices[type] * count).toFixed(2)}</span>
                        </div>
                    `;
                    });

                priceHTML += `
                    <div class="border-t border-white/20 pt-2 mt-2">
                        <div class="flex justify-between text-sm">
                            <span>Ara Toplam:</span>
                            <span class="font-medium">₺${this.taxCalculation.subtotal.toFixed(2)}</span>
                        </div>
            `;

                this.taxCalculation.taxes.forEach(tax => {
                    priceHTML += `
                    <div class="flex justify-between text-xs text-gray-400">
                        <span>${tax.formatted_name || tax.name}:</span>
                        <span>₺${tax.amount.toFixed(2)}</span>
                    </div>
                `;
                });

                priceHTML += `
                        <div class="flex justify-between font-bold text-emerald-400 border-t border-white/20 pt-1 mt-1">
                            <span>Genel Toplam:</span>
                            <span>₺${this.taxCalculation.total.toFixed(2)}</span>
                        </div>
                    </div>
                </div>
            `;

                document.getElementById('finalPriceBreakdown').innerHTML = priceHTML;
                document.getElementById('totalPrice').textContent = `₺${this.taxCalculation.total.toFixed(2)}`;

                // Update ticket count
                const taxInfo = this.taxCalculation.taxes.map(tax =>
                    tax.formatted_name || `${tax.name}`
                ).join(' + ');

                const ticketCountElement = document.getElementById('ticketCountInfo');
                if (ticketCountElement) {
                    ticketCountElement.innerHTML = `
                    <div>${taxInfo} dahil • ${selectedSeats.length} bilet</div>
                    <div class="text-xs text-gray-500">
                        Ara Toplam: ₺${this.taxCalculation.subtotal.toFixed(2)} + 
                    Service Fee: ₺${this.taxCalculation.total_tax_amount.toFixed(2)}
                    </div>
                `;
                }
            }
        }

        async loadTicketTypes(showtimeId) {
            try {
                this.showLoading();
                
                console.log(`[PaymentForm] Loading ticket types for showtime: ${showtimeId}`);
                console.log(`[PaymentForm] selectedShowtime object:`, selectedShowtime);

                if (!showtimeId) {
                    console.error('[PaymentForm] ERROR: showtimeId is empty or undefined!');
                    throw new Error('Showtime ID is required');
                }

                const url = `/api/tickets/prices/${showtimeId}`;
                console.log(`[PaymentForm] Fetching: ${url}`);
                
                const startTime = Date.now();
                const response = await axios.get(url);
                const elapsed = Date.now() - startTime;
                
                console.log(`[PaymentForm] API call took ${elapsed}ms`);
                console.log(`[PaymentForm] Status: ${response.status}`);
                console.log(`[PaymentForm] Full API Response:`, response.data);

                const payload = response.data?.data || {};
                console.log(`[PaymentForm] Extracted payload:`, payload);
                
                let types = payload.types || [];
                const apiPrices = payload.prices || null;

                console.log(`[PaymentForm] Types from API:`, types);
                console.log(`[PaymentForm] Prices from API:`, apiPrices);

                // If server didn't supply types but provided prices, derive types from price keys
                if ((Array.isArray(types) && types.length === 0) && apiPrices) {
                    console.warn('[PaymentForm] Ticket types missing from API, deriving from prices keys');
                    types = Object.keys(apiPrices).map(code => ({
                        code: code,
                        name: code.charAt(0).toUpperCase() + code.slice(1),
                        icon: 'fa-ticket-alt',
                        description: ''
                    }));
                    console.log('[PaymentForm] Derived types:', types);
                }

                // If still no types and no prices, fallback to mock data
                if (!Array.isArray(types) || types.length === 0) {
                    console.error('[PaymentForm] No ticket types available from API. Falling back to defaults.');
                    // Show a visible message in the loading area so users know what's happening
                    try {
                        const loadingText = this.loadingElement.querySelector('p');
                        if (loadingText) loadingText.textContent = 'No ticket types received — using default prices.';
                    } catch (e) { /* ignore DOM errors */ }

                    this.loadMockData();
                    console.log('[PaymentForm] Mock data loaded. Types:', this.customerTypes);
                    // Ensure totals are recalculated if necessary
                    await this.updateTotalPrice();
                    return;
                }

                // Assign derived or provided types
                this.customerTypes = types;
                console.log('[PaymentForm] Customer types assigned:', this.customerTypes);

                // Process prices; if prices missing, attempt to use selectedShowtime price or fallback value
                this.ticketPrices = {};
                if (apiPrices) {
                    this.customerTypes.forEach(type => {
                        const raw = apiPrices[type.code];
                        this.ticketPrices[type.code] = Number(raw) || Number(selectedShowtime?.price) || 0;
                    });
                } else {
                    // No prices provided by API — use selectedShowtime.price as base
                    const base = Number(selectedShowtime?.price) || 45;
                    this.customerTypes.forEach(type => {
                        this.ticketPrices[type.code] = base;
                    });
                }

                console.log('[PaymentForm] Final ticket prices:', this.ticketPrices);

                this.renderTicketTypes();
                console.log('[PaymentForm] Ticket types rendered');
                
                this.renderPriceInfo();
                console.log('[PaymentForm] Price info rendered');
                
                this.showTicketTypes();
                console.log('[PaymentForm] Ticket types shown');

                // Ensure totals/taxes are recalculated in case there are preselected counts
                await this.updateTotalPrice();
                console.log('[PaymentForm] Total price updated');

            } catch (error) {
                console.error('[PaymentForm] ERROR in loadTicketTypes:', error);
                console.error('[PaymentForm] Error status:', error.response?.status);
                console.error('[PaymentForm] Error data:', error.response?.data);
                console.error('[PaymentForm] Error message:', error.message);
                
                console.log('[PaymentForm] Falling back to mock data due to error');
                this.loadMockData();
            }
        }

        loadMockData() {
            const basePrice = selectedShowtime?.price || 45;

            this.customerTypes = [
                { code: 'adult', name: 'Adult', icon: 'fa-user', description: 'Full ticket' },
                { code: 'student', name: 'Student', icon: 'fa-graduation-cap', description: '20% discount' },
                { code: 'senior', name: 'Retired', icon: 'fa-user-tie', description: '15% discount' },
                { code: 'child', name: 'Child', icon: 'fa-child', description: '25% discount' }
            ];

            this.ticketPrices = {
                adult: basePrice,
                student: basePrice * 0.8,
                senior: basePrice * 0.85,
                child: basePrice * 0.75
            };

            this.renderTicketTypes();
            this.renderPriceInfo();
            this.showTicketTypes();
        }

        renderTicketTypes() {
            let html = '';

            this.customerTypes.forEach(type => {
                html += `
                <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 transition-all">
                    <div class="flex items-center space-x-3">
                        <i class="fas ${type.icon} text-2xl text-emerald-400"></i>
                        <div>
                            <h5 class="text-white font-medium">${type.name}</h5>
                            <p class="text-gray-400 text-sm">${type.description}</p>
                            <p class="text-emerald-400 font-bold">₺${this.ticketPrices[type.code].toFixed(2)}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button onclick="window.paymentForm.changeTicketCount('${type.code}', -1)" 
                                class="w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-full font-bold transition-all">-</button>
                        <span id="count_${type.code}" class="text-white font-bold w-8 text-center">0</span>
                        <button onclick="window.paymentForm.changeTicketCount('${type.code}', 1)" 
                                class="w-8 h-8 bg-emerald-500 hover:bg-emerald-600 text-white rounded-full font-bold transition-all">+</button>
                    </div>
                </div>
            `;
            });

            this.typesContainer.innerHTML = html;
        }

        renderPriceInfo() {
            let html = '';

            this.customerTypes.forEach(type => {
                html += `
                <div class="flex justify-between items-center py-2">
                    <span class="flex items-center">
                        <i class="fas ${type.icon} text-emerald-400 mr-2"></i>
                        ${type.name}:
                    </span>
                    <span class="font-bold text-emerald-400">₺${this.ticketPrices[type.code].toFixed(2)}</span>
                </div>
            `;
            });

            this.priceInfoElement.innerHTML = html;
        }

        updateSummary() {
            const totalCount = this.getTotalTicketCount();
            this.countElement.textContent = totalCount;

            if (totalCount === 0) {
                this.summaryElement.textContent = 'No tickets selected';
                this.summaryElement.className = 'mt-2 text-sm text-gray-300';
                this.continueButton.disabled = true;
            } else {
                const summary = Object.entries(this.selectedTicketTypes)
                    .filter(([type, count]) => count > 0)
                    .map(([type, count]) => {
                        const typeObj = this.customerTypes.find(t => t.code === type);
                        return `${count} ${typeObj?.name || type}`;
                    })
                    .join(', ');

                this.summaryElement.textContent = summary;
                this.summaryElement.className = 'mt-2 text-sm text-gray-300';
                this.continueButton.disabled = false;
            }
        }

        validateForm() {
            const name = document.getElementById('customerName').value.trim();
            const email = document.getElementById('customerEmail').value.trim();
            const phone = document.getElementById('customerPhone').value.trim();
            const terms = document.getElementById('termsAccepted').checked;

            if (!name || !email || !phone) {
                alert('Please complete all customer details!');
                return false;
            }

            if (!terms) {
                alert('Please accept the terms of use first!');
                return false;
            }

            const totalTickets = Object.values(this.selectedTicketTypes).reduce((sum, count) => sum + count, 0);
            const selectedSeats = window.seatMap?.getSelectedSeats() || [];

            if (totalTickets === 0) {
                alert('Please pick at least one ticket type!');
                return false;
            }

            if (totalTickets !== selectedSeats.length) {
                alert('Seat and ticket counts do not match!');
                return false;
            }

            return true;
        }


        // PaymentForm - processPayment() implementation:

        async processPayment() {
            if (!this.validateForm()) return;

            // Prepare tax calculation
            await this.calculateTotalWithTaxes();

            if (!this.taxCalculation) {
                alert('Service fee could not be calculated. Please try again.');
                return;
            }

            // Authentication & token validation
            console.log('Current token:', localStorage.getItem('token'));
            console.log('Authorization header:', axios.defaults.headers.common['Authorization']);

            const token = localStorage.getItem('token');
            if (token && !axios.defaults.headers.common['Authorization']) {
                axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
                console.log('Token manually set');
            }

            // Auth test
            try {
                const authTest = await axios.get('/api/me');
                console.log('Auth test passed:', authTest.data);
            } catch (error) {
                console.log('Auth test failed:', error);
                alert('Session issue detected! Please sign in again.');
                return;
            }

            this.completeSaleBtn.disabled = true;
            this.completeSaleBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing your order...';

            try {
                const selectedSeats = window.seatMap?.getSelectedSeats() || [];

                // Prepare ticket types
                const ticketTypesArray = [];
                selectedSeats.forEach((seat, index) => {
                    const customerType = this.getCustomerTypeForSeat(index);
                    ticketTypesArray.push({
                        seat_id: seat.id,
                        customer_type: customerType
                    });
                });

                console.log('Sending data:');
                console.log('- Tickets:', ticketTypesArray);
                console.log('- Tax calculation:', this.taxCalculation);

                // Determine selected payment method details
                const paymentMethod = document.getElementById('paymentMethod').value;
                let paymentDetails = {};

                if (paymentMethod === 'card') {
                    paymentDetails = {
                        type: 'card',
                        card_name: document.getElementById('cardName').value || null,
                        last4: (document.getElementById('cardNumber').value || '').slice(-4),
                        expiry: document.getElementById('cardExpiry').value || null,
                    };
                } else if (paymentMethod === 'online') {
                    const providerButton = document.querySelector('#onlineProviders button.selected');
                    const provider = providerButton ? providerButton.getAttribute('data-provider') : null;
                    paymentDetails = {
                        type: 'online',
                        provider: provider
                    };
                }

                // API request
                const response = await axios.post('/api/tickets', {
                    showtime_id: selectedShowtime.id,
                    tickets: ticketTypesArray,
                    customer_name: document.getElementById('customerName').value,
                    customer_email: document.getElementById('customerEmail').value,
                    customer_phone: document.getElementById('customerPhone').value,
                    payment_method: paymentMethod,
                    payment_details: paymentDetails,
                    tax_calculation: this.taxCalculation
                });

                console.log('Payment successful:', response.data);
                this.showSuccessMessage();

            } catch (error) {
                console.log('Full error:', error);
                console.log('Response:', error.response);
                console.log('Status:', error.response?.status);
                console.log('Message:', error.response?.data);

                if (error.response?.status === 401) {
                    localStorage.removeItem('token');
                    alert('Your session has expired. Please sign in again.');
                    window.location.href = '/login';
                } else {
                    const errorMessage = error.response?.data?.message || 'Purchase failed!';
                    alert(errorMessage);
                }

                this.completeSaleBtn.disabled = false;
                this.completeSaleBtn.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Complete Payment';
            }
        }

        // Determine customer type using seat index
        getCustomerTypeForSeat(seatIndex) {
            const selectedTypes = Object.entries(this.selectedTicketTypes)
                .filter(([type, count]) => count > 0);

            let currentIndex = 0;
            for (let [type, count] of selectedTypes) {
                if (seatIndex < currentIndex + count) {
                    return type;
                }
                currentIndex += count;
            }

            return 'adult'; // default
        }

        showSuccessMessage() {
            if (!this.taxCalculation) return;

            const ticketSummary = Object.entries(this.selectedTicketTypes)
                .filter(([type, count]) => count > 0)
                .map(([type, count]) => {
                    const typeObj = this.customerTypes.find(t => t.code === type);
                    return `${count} ${typeObj?.name || type}`;
                })
                .join(', ');

            const selectedSeats = window.seatMap?.getSelectedSeats() || [];

            // Eski alert yerine modern, ortalanmış bir başarı modali göster
            const existingModal = document.getElementById('purchaseSuccessModal');
            if (existingModal) {
                existingModal.remove();
            }

            const totalText = `₺${this.taxCalculation.total.toFixed(2)}`;
            const seatsText = selectedSeats.map(s => s.code).join(', ');

            const modalHtml = `
                <div id="purchaseSuccessModal"
                     class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 px-4">
                    <div class="bg-gray-900 rounded-2xl shadow-2xl max-w-md w-full p-8 border border-emerald-500/40">
                        <div class="flex flex-col items-center text-center space-y-4">
                            <div
                                class="w-16 h-16 rounded-full bg-emerald-500/10 border border-emerald-400 flex items-center justify-center mb-2">
                                <i class="fas fa-check text-3xl text-emerald-400"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-white">Ticket purchase successful!</h3>
                            <p class="text-gray-300 text-sm max-w-xs">
                                Your tickets have been created successfully. You can view all details on the
                                <span class="font-semibold text-emerald-300">My Tickets</span> page.
                            </p>

                            <div class="w-full bg-gray-800/80 rounded-xl p-4 space-y-2 text-sm">
                                <div class="flex justify-between text-gray-300">
                                    <span>Total</span>
                                    <span class="font-semibold text-emerald-400">${totalText}</span>
                                </div>
                                <div class="flex justify-between text-gray-300">
                                    <span>Tickets</span>
                                    <span class="font-semibold">${ticketSummary}</span>
                                </div>
                                <div class="flex justify-between text-gray-300">
                                    <span>Seats</span>
                                    <span class="font-semibold">${seatsText}</span>
                                </div>
                            </div>

                            <button id="viewMyTicketsBtn"
                                    class="mt-2 w-full bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-3 px-4 rounded-xl transition-all flex items-center justify-center gap-2">
                                <i class="fas fa-ticket-alt"></i>
                                <span>Go to My Tickets</span>
                            </button>

                            <button id="closeSuccessModalBtn"
                                    class="text-xs text-gray-400 hover:text-gray-200 mt-1">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            `;

            document.body.insertAdjacentHTML('beforeend', modalHtml);

            const modal = document.getElementById('purchaseSuccessModal');
            const viewBtn = document.getElementById('viewMyTicketsBtn');
            const closeBtn = document.getElementById('closeSuccessModalBtn');

            // Sadece butona tıklarsa My Tickets sayfasına gitsin, otomatik redirect yok
            if (viewBtn) {
                viewBtn.addEventListener('click', () => {
                    window.location.href = '/my-tickets';
                });
            }
            if (closeBtn) {
                closeBtn.addEventListener('click', () => {
                    if (modal) modal.remove();
                });
            }
        }

        showLoading() {
            this.loadingElement.classList.remove('hidden');
            this.typesContainer.classList.add('hidden');
        }

        showTicketTypes() {
            this.loadingElement.classList.add('hidden');
            this.typesContainer.classList.remove('hidden');
        }

        getSelectedTicketTypes() {
            return this.selectedTicketTypes;
        }

        getTotalTicketCount() {
            return Object.values(this.selectedTicketTypes).reduce((sum, count) => sum + count, 0);
        }

        reset() {
            this.selectedTicketTypes = {};
            this.customerTypes = [];
            this.ticketPrices = {};
            this.taxCalculation = null;
            this.updateSummary();
            this.hideTaxSection();
            if (this.continueButton) {
                this.continueButton.disabled = true;
            }
        }

        async changeTicketCount(ticketType, change) {
            if (!this.selectedTicketTypes[ticketType]) {
                this.selectedTicketTypes[ticketType] = 0;
            }

            const newCount = this.selectedTicketTypes[ticketType] + change;

            if (newCount < 0) return;

            // Artık MAX_TICKETS_PER_ORDER sınırı yok, sadece negatif olmayan sayıları kabul et
            this.selectedTicketTypes[ticketType] = newCount;
            document.getElementById(`count_${ticketType}`).textContent = newCount;

            this.updateSummary();
            await this.updateTotalPrice(); // async yap
        }
    }

    // Initialize payment form
    document.addEventListener('DOMContentLoaded', function () {
        window.paymentForm = new PaymentForm();
        prefillCustomerInfo();

        // Payment method UI behavior
        const paymentSelect = document.getElementById('paymentMethod');
        const cardDetails = document.getElementById('cardDetails');
        const onlineProviders = document.getElementById('onlineProviders');

        function updatePaymentMethodUI() {
            const value = paymentSelect.value;
            cardDetails.classList.add('hidden');
            onlineProviders.classList.add('hidden');

            if (value === 'card') {
                cardDetails.classList.remove('hidden');
            } else if (value === 'online') {
                onlineProviders.classList.remove('hidden');
            }
        }

        if (paymentSelect) {
            paymentSelect.addEventListener('change', updatePaymentMethodUI);
            updatePaymentMethodUI();
        }

        // Mark selected online provider
        document.querySelectorAll('#onlineProviders button').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('#onlineProviders button').forEach(b => b.classList.remove('ring-2', 'ring-emerald-400'));
                this.classList.add('ring-2', 'ring-emerald-400');
            });
        });
    });

    // Global functions
    async function goToSeatSelection() {
        if (!selectedShowtime) {
            alert('Please select a showtime first!');
            return;
        }

        if (!window.paymentForm) {
            alert('Ticket types failed to load. Please refresh the page.');
            return;
        }

        const totalTickets = window.paymentForm.getTotalTicketCount();
        if (totalTickets === 0) {
            alert('Please select at least one ticket to continue.');
            return;
        }

        if (!window.seatMap) {
            alert('Seat map is not ready yet. Please try again shortly.');
            return;
        }

        await window.seatMap.setSeatLimit(totalTickets);
        window.seatMap.setShowtime(selectedShowtime);

        currentTicketStep = 5;
        updateTicketSteps();
        await window.seatMap.loadSeats(selectedShowtime.id);
    }

    function goToPayment() {
        if (!window.paymentForm || !window.seatMap) {
            alert('Please complete seat and ticket selections first.');
            return;
        }

        const totalTickets = window.paymentForm.getTotalTicketCount();
        if (totalTickets === 0) {
            alert('Choose tickets before moving to payment.');
            return;
        }

        const selectedSeats = window.seatMap.getSelectedSeats() || [];
        if (selectedSeats.length !== totalTickets) {
            alert('Seat and ticket counts do not match!');
            return;
        }

        currentTicketStep = 6;
        updateTicketSteps();
        window.paymentForm.generateOrderSummary();
        prefillCustomerInfo();
    }

    function completeSale() {
        window.paymentForm.processPayment();
    }

    function prefillCustomerInfo() {
        if (!window.userPermissions || !window.userPermissions.isLoggedIn) {
            return;
        }

        const { userName, email, phone } = window.userPermissions;

        const nameInput = document.getElementById('customerName');
        const emailInput = document.getElementById('customerEmail');
        const phoneInput = document.getElementById('customerPhone');

        if (nameInput && userName && !nameInput.value) {
            nameInput.value = userName;
        }

        if (emailInput && email && !emailInput.value) {
            emailInput.value = email;
        }

        if (phoneInput && phone && !phoneInput.value) {
            phoneInput.value = phone;
        }
    }
</script>