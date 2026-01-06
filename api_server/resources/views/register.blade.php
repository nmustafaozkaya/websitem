@extends('layout')

@section('content')
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full">
            <!-- Register Card -->
            <div class="glass-effect p-8 rounded-2xl shadow-2xl">
                <div class="text-center mb-8">
                    <div
                        class="w-20 h-20 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-plus text-white text-2xl"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-white mb-2">Sign Up</h2>
                    <p class="text-gray-300">Create a new account</p>
                </div>

                <form id="registerForm" class="space-y-6">
                    <div>
                        <label class="block text-white text-sm font-medium mb-2">
                            <i class="fas fa-user mr-2"></i>Full Name
                        </label>
                        <input type="text" id="name"
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-300 focus:bg-white/20 focus:border-emerald-400 transition-all"
                            placeholder="Full name" required>
                    </div>

                    <div>
                        <label class="block text-white text-sm font-medium mb-2">
                            <i class="fas fa-envelope mr-2"></i>Email
                        </label>
                        <input type="email" id="email"
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-300 focus:bg-white/20 focus:border-emerald-400 transition-all"
                            placeholder="Email" required>
                    </div>

                    <div>
                        <label class="block text-white text-sm font-medium mb-2">
                            <i class="fas fa-phone mr-2"></i>Phone
                        </label>
                        <input type="tel" id="phone"
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-300 focus:bg-white/20 focus:border-emerald-400 transition-all"
                            placeholder="Phone number">
                    </div>

                    <div>
                        <label class="block text-white text-sm font-medium mb-2">
                            <i class="fas fa-calendar mr-2"></i>Date of Birth
                        </label>
                        <input type="date" id="birth_date"
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-300 focus:bg-white/20 focus:border-emerald-400 transition-all"
                            max="{{ date('Y-m-d', strtotime('-1 day')) }}">
                    </div>

                    <div>
                        <label class="block text-white text-sm font-medium mb-2">
                            <i class="fas fa-venus-mars mr-2"></i>Gender
                        </label>
                        <select id="gender"
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-300 focus:bg-white/20 focus:border-emerald-400 transition-all">
                            <option value="">Select</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-white text-sm font-medium mb-2">
                            <i class="fas fa-lock mr-2"></i>Password
                        </label>
                        <input type="password" id="password"
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-300 focus:bg-white/20 focus:border-emerald-400 transition-all"
                            placeholder="Password" required>
                    </div>

                    <div>
                        <label class="block text-white text-sm font-medium mb-2">
                            <i class="fas fa-lock mr-2"></i>Confirm Password
                        </label>
                        <input type="password" id="password_confirmation"
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-300 focus:bg-white/20 focus:border-emerald-400 transition-all"
                            placeholder="Confirm password" required>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="terms"
                            class="w-4 h-4 text-emerald-600 bg-transparent border-white/20 rounded focus:ring-emerald-500"
                            required>
                        <label for="terms" class="ml-2 text-sm text-gray-300">
                            I accept the <a href="#" class="text-emerald-400 hover:text-emerald-300">Terms of Use</a> and
                            <a href="#" class="text-emerald-400 hover:text-emerald-300">Privacy Policy</a>
                        </label>
                    </div>

                    <button type="submit"
                        class="w-full bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-user-plus mr-2"></i>Sign Up
                    </button>
                </form>

                <div id="registerMessage" class="mt-6"></div>

                <div class="mt-8 text-center">
                    <p class="text-gray-400 text-sm">
                        Already have an account?
                        <a href="/login" class="text-emerald-400 hover:text-emerald-300 font-medium transition-colors">
                            Sign in
                        </a>
                    </p>
                </div>

                <!-- Info Box -->
                <div class="mt-6 p-4 bg-emerald-500/10 rounded-xl border border-emerald-500/20">
                    <h4 class="text-emerald-300 font-medium mb-2 text-center">
                        <i class="fas fa-info-circle mr-2"></i>Sign-up Benefits
                    </h4>
                    <ul class="text-sm text-emerald-200 space-y-1">
                        <li><i class="fas fa-check mr-2"></i>Buy tickets</li>
                        <li><i class="fas fa-check mr-2"></i>View ticket history</li>
                        <li><i class="fas fa-check mr-2"></i>Book in seconds</li>
                        <li><i class="fas fa-check mr-2"></i>Get notified about special offers</li>
                    </ul>
                </div>
            </div>

            <!-- Features -->
            <div class="mt-8 grid grid-cols-3 gap-4">
                <div class="glass-effect p-4 rounded-xl text-center">
                    <i class="fas fa-shield-alt text-emerald-400 text-2xl mb-2"></i>
                    <p class="text-white text-sm font-medium">Secure</p>
                    <p class="text-gray-400 text-xs">256-bit SSL</p>
                </div>
                <div class="glass-effect p-4 rounded-xl text-center">
                    <i class="fas fa-rocket text-emerald-400 text-2xl mb-2"></i>
                    <p class="text-white text-sm font-medium">Fast</p>
                    <p class="text-gray-400 text-xs">Instant sign up</p>
                </div>
                <div class="glass-effect p-4 rounded-xl text-center">
                    <i class="fas fa-gift text-emerald-400 text-2xl mb-2"></i>
                    <p class="text-white text-sm font-medium">Perks</p>
                    <p class="text-gray-400 text-xs">Exclusive offers</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            const birthDate = document.getElementById('birth_date').value;
            const gender = document.getElementById('gender').value;
            const password = document.getElementById('password').value;
            const passwordConfirmation = document.getElementById('password_confirmation').value;
            const terms = document.getElementById('terms').checked;

            // Validation
            if (!name || !email || !password || !passwordConfirmation) {
                showMessage('Please fill in all required fields!', 'error');
                return;
            }

            if (password.length < 8) {
                showMessage('Password must be at least 8 characters!', 'error');
                return;
            }

            if (password !== passwordConfirmation) {
                showMessage('Passwords do not match!', 'error');
                return;
            }

            if (!terms) {
                showMessage('You must accept the terms first!', 'error');
                return;
            }

            showLoading();

            try {
                const registerData = {
                    name: name,
                    email: email,
                    password: password,
                    password_confirmation: passwordConfirmation
                };

                // Opsiyonel alanları ekle
                if (phone) registerData.phone = phone;
                if (birthDate) registerData.birth_date = birthDate;
                if (gender) registerData.gender = gender;

                const response = await axios.post('/api/register', registerData);

                hideLoading();

                if (response.data.success) {
                    // Token'ı kaydet
                    localStorage.setItem('token', response.data.data.token);

                    showMessage('Registration successful! Redirecting...', 'success');

                    // Session login da yap (opsiyonel)
                    setTimeout(async () => {
                        try {
                            await fetch('/login', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    email: email,
                                    password: password
                                })
                            });
                        } catch (sessionError) {
                            console.log('Session login failed, but API token is saved');
                        }

                        window.location.href = '/';
                    }, 1500);
                }

            } catch (error) {
                hideLoading();

                if (error.response?.data?.message) {
                    showMessage(error.response.data.message, 'error');
                } else if (error.response?.data?.errors) {
                    // Laravel validation errors
                    const errors = error.response.data.errors;
                    const firstError = Object.values(errors)[0][0];
                    showMessage(firstError, 'error');
                } else {
                    showMessage('Something went wrong during registration!', 'error');
                }
            }
        });

        function showLoading() {
            const button = document.querySelector('button[type="submit"]');
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Signing you up...';
        }

        function hideLoading() {
            const button = document.querySelector('button[type="submit"]');
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-user-plus mr-2"></i>Sign Up';
        }

        function showMessage(message, type) {
            const messageDiv = document.getElementById('registerMessage');
            const bgColor = type === 'success' ? 'bg-emerald-500/20 border-emerald-500/50 text-emerald-300' : 'bg-red-500/20 border-red-500/50 text-red-300';
            const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';

            messageDiv.innerHTML = `
                    <div class="p-4 rounded-xl border ${bgColor} flex items-center">
                        <i class="${icon} mr-2"></i>
                        <span>${message}</span>
                    </div>
                `;
        }

        // Password strength indicator (opsiyonel)
        document.getElementById('password').addEventListener('input', function () {
            const password = this.value;
            const strength = getPasswordStrength(password);
            // Buraya password strength göstergesi eklenebilir
        });

        function getPasswordStrength(password) {
            let strength = 0;
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            return strength;
        }
    </script>
@endsection