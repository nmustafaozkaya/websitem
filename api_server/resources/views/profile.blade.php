@extends('layout')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Profile Header -->
    <div class="glass-effect p-8 rounded-2xl mb-8 text-center fade-in">
        <div class="w-32 h-32 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-2xl">
            <i class="fas fa-user text-white text-5xl"></i>
        </div>
        <h2 class="text-3xl font-bold text-white mb-2" id="userName">Loading...</h2>
        <p class="text-gray-300 text-lg" id="userEmail">Loading...</p>
    </div>

    <!-- Account Settings -->
    <div class="glass-effect p-6 rounded-2xl mb-6 fade-in">
        <div class="flex items-center mb-4">
            <i class="fas fa-cog text-emerald-400 text-xl mr-3"></i>
            <h3 class="text-xl font-bold text-white">Account Settings</h3>
        </div>
        <div class="h-px bg-emerald-500/30 mb-4"></div>
        
        <!-- Edit Details -->
        <div class="bg-white/5 hover:bg-white/10 rounded-xl p-4 mb-3 cursor-pointer transition-all group" onclick="openEditDetailsModal()">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-emerald-500/20 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-edit text-emerald-400 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-white font-semibold">Edit Details</h4>
                        <p class="text-gray-400 text-sm">Update your profile information</p>
                    </div>
                </div>
                <i class="fas fa-chevron-right text-gray-400 group-hover:text-emerald-400 transition-colors"></i>
            </div>
        </div>

        <!-- Change Password -->
        <div class="bg-white/5 hover:bg-white/10 rounded-xl p-4 mb-3 cursor-pointer transition-all group" onclick="openChangePasswordModal()">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-lock text-blue-400 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-white font-semibold">Change Password</h4>
                        <p class="text-gray-400 text-sm">Update your account password</p>
                    </div>
                </div>
                <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-400 transition-colors"></i>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="bg-white/5 hover:bg-white/10 rounded-xl p-4 cursor-pointer transition-all group" onclick="showComingSoon('Payment Methods')">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-credit-card text-purple-400 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-white font-semibold">Payment Methods</h4>
                        <p class="text-gray-400 text-sm">Manage your payment options</p>
                    </div>
                </div>
                <i class="fas fa-chevron-right text-gray-400 group-hover:text-purple-400 transition-colors"></i>
            </div>
        </div>
    </div>

    <!-- My Movies -->
    <div class="glass-effect p-6 rounded-2xl mb-6 fade-in">
        <div class="flex items-center mb-4">
            <i class="fas fa-film text-red-400 text-xl mr-3"></i>
            <h3 class="text-xl font-bold text-white">My Movies</h3>
        </div>
        <div class="h-px bg-red-500/30 mb-4"></div>
        
        <!-- Favorite Movies -->
        <div class="bg-white/5 hover:bg-white/10 rounded-xl p-4 cursor-pointer transition-all group" onclick="showComingSoon('Favorite Movies')">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-red-500/20 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-heart text-red-400 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-white font-semibold">Favorite Movies</h4>
                        <p class="text-gray-400 text-sm">View your favorite movies</p>
                    </div>
                </div>
                <i class="fas fa-chevron-right text-gray-400 group-hover:text-red-400 transition-colors"></i>
            </div>
        </div>
    </div>

    <!-- My Tickets -->
    <div class="glass-effect p-6 rounded-2xl mb-6 fade-in">
        <div class="flex items-center mb-4">
            <i class="fas fa-ticket-alt text-yellow-400 text-xl mr-3"></i>
            <h3 class="text-xl font-bold text-white">My Tickets</h3>
        </div>
        <div class="h-px bg-yellow-500/30 mb-4"></div>
        
        <!-- My Tickets -->
        <a href="/my-tickets" class="block bg-white/5 hover:bg-white/10 rounded-xl p-4 cursor-pointer transition-all group">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-yellow-500/20 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-ticket-alt text-yellow-400 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-white font-semibold">My Tickets</h4>
                        <p class="text-gray-400 text-sm">View your purchased tickets</p>
                    </div>
                </div>
                <i class="fas fa-chevron-right text-gray-400 group-hover:text-yellow-400 transition-colors"></i>
            </div>
        </a>
    </div>

    <!-- Logout -->
    <div class="glass-effect p-6 rounded-2xl fade-in">
        <button onclick="handleLogout()" class="w-full bg-red-500/20 hover:bg-red-500/30 border-2 border-red-500/50 rounded-xl p-4 transition-all group">
            <div class="flex items-center justify-center">
                <div class="w-12 h-12 bg-red-500/30 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-sign-out-alt text-red-400 text-xl"></i>
                </div>
                <h4 class="text-red-400 font-bold text-lg">Log Out</h4>
            </div>
        </button>
    </div>
</div>

<!-- Edit Details Modal -->
<div id="editDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
    <div class="bg-slate-800 rounded-2xl p-6 max-w-md w-full">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-bold text-white">Edit Details</h3>
            <button onclick="closeEditDetailsModal()" class="text-gray-400 hover:text-white transition-colors">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        <form id="editDetailsForm" class="space-y-4 max-h-[70vh] overflow-y-auto pr-2">
            <div>
                <label class="block text-white text-sm font-medium mb-2">Name</label>
                <input type="text" id="editName" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:border-emerald-500" placeholder="Your name" required>
            </div>
            <div>
                <label class="block text-white text-sm font-medium mb-2">Email</label>
                <input type="email" id="editEmail" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:border-emerald-500" placeholder="your@email.com" required>
            </div>
            <div>
                <label class="block text-white text-sm font-medium mb-2">Phone</label>
                <input type="tel" id="editPhone" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:border-emerald-500" placeholder="05XXXXXXXXX">
            </div>
            <div>
                <label class="block text-white text-sm font-medium mb-2">Date of Birth</label>
                <input type="date" id="editBirthDate" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:border-emerald-500">
            </div>
            <div>
                <label class="block text-white text-sm font-medium mb-2">Gender</label>
                <select id="editGender" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:border-emerald-500">
                    <option value="">Select Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="flex gap-3 mt-6 sticky bottom-0 bg-slate-800 pt-4">
                <button type="button" onclick="closeEditDetailsModal()" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-3 rounded-lg font-medium transition-colors">
                    Cancel
                </button>
                <button type="submit" class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white py-3 rounded-lg font-medium transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Change Password Modal -->
<div id="changePasswordModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
    <div class="bg-slate-800 rounded-2xl p-6 max-w-md w-full">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-bold text-white">Change Password</h3>
            <button onclick="closeChangePasswordModal()" class="text-gray-400 hover:text-white transition-colors">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        <form id="changePasswordForm" class="space-y-4">
            <div>
                <label class="block text-white text-sm font-medium mb-2">Current Password</label>
                <input type="password" id="currentPassword" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:border-blue-500" placeholder="Enter current password">
            </div>
            <div>
                <label class="block text-white text-sm font-medium mb-2">New Password</label>
                <input type="password" id="newPassword" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:border-blue-500" placeholder="Enter new password">
            </div>
            <div>
                <label class="block text-white text-sm font-medium mb-2">Confirm New Password</label>
                <input type="password" id="confirmPassword" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:border-blue-500" placeholder="Confirm new password">
            </div>
            <div class="flex gap-3 mt-6">
                <button type="button" onclick="closeChangePasswordModal()" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-3 rounded-lg font-medium transition-colors">
                    Cancel
                </button>
                <button type="submit" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-3 rounded-lg font-medium transition-colors">
                    Change Password
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Load user data
    document.addEventListener('DOMContentLoaded', function() {
        loadUserData();
    });

    async function loadUserData() {
        try {
            const token = localStorage.getItem('token');
            if (!token) {
                window.location.href = '/login';
                return;
            }

            const response = await axios.get('/api/me', {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            if (response.data && response.data.success) {
                const user = response.data.data;
                document.getElementById('userName').textContent = user.name;
                document.getElementById('userEmail').textContent = user.email;
                
                // Pre-fill edit form
                document.getElementById('editName').value = user.name;
                document.getElementById('editEmail').value = user.email;
                document.getElementById('editPhone').value = user.phone || '';
                document.getElementById('editBirthDate').value = user.birth_date || '';
                document.getElementById('editGender').value = user.gender || '';
            }
        } catch (error) {
            console.error('Failed to load user data:', error);
            if (error.response?.status === 401) {
                localStorage.removeItem('token');
                localStorage.removeItem('user');
                window.location.href = '/login';
            }
        }
    }

    // Edit Details Modal
    function openEditDetailsModal() {
        document.getElementById('editDetailsModal').classList.remove('hidden');
    }

    function closeEditDetailsModal() {
        document.getElementById('editDetailsModal').classList.add('hidden');
    }

    // Handle Edit Details Form Submit
    document.getElementById('editDetailsForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const name = document.getElementById('editName').value;
        const email = document.getElementById('editEmail').value;
        const phone = document.getElementById('editPhone').value;
        const birthDate = document.getElementById('editBirthDate').value;
        const gender = document.getElementById('editGender').value;

        try {
            showLoading();
            const token = localStorage.getItem('token');
            
            const payload = {
                name: name,
                email: email,
                phone: phone || null,
                birth_date: birthDate || null,
                gender: gender || null
            };
            
            const response = await axios.put('/api/profile', payload, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            hideLoading();

            if (response.data && response.data.success) {
                closeEditDetailsModal();
                showSuccessModal('Profile updated successfully!');
                loadUserData();
            }
        } catch (error) {
            hideLoading();
            console.error('Failed to update profile:', error);
            alert(error.response?.data?.message || 'Failed to update profile. Please try again.');
        }
    });

    // Change Password Modal
    function openChangePasswordModal() {
        document.getElementById('changePasswordModal').classList.remove('hidden');
    }

    function closeChangePasswordModal() {
        document.getElementById('changePasswordModal').classList.add('hidden');
        // Clear password fields
        document.getElementById('currentPassword').value = '';
        document.getElementById('newPassword').value = '';
        document.getElementById('confirmPassword').value = '';
    }

    // Handle Change Password Form Submit
    document.getElementById('changePasswordForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const currentPassword = document.getElementById('currentPassword').value;
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        // Validate passwords
        if (newPassword !== confirmPassword) {
            alert('New passwords do not match!');
            return;
        }

        if (newPassword.length < 5) {
            alert('New password must be at least 5 characters long!');
            return;
        }

        try {
            showLoading();
            const token = localStorage.getItem('token');
            
            const response = await axios.post('/api/change-password', {
                current_password: currentPassword,
                new_password: newPassword,
                new_password_confirmation: confirmPassword
            }, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            hideLoading();

            if (response.data && response.data.success) {
                closeChangePasswordModal();
                showSuccessModal('Password changed successfully!');
            }
        } catch (error) {
            hideLoading();
            console.error('Failed to change password:', error);
            alert(error.response?.data?.message || 'Failed to change password. Please try again.');
        }
    });

    // Show Coming Soon Message
    function showComingSoon(feature) {
        alert(`${feature} feature is coming soon!`);
    }

    // Handle Logout
    async function handleLogout() {
        if (!confirm('Are you sure you want to log out?')) {
            return;
        }

        try {
            const token = localStorage.getItem('token');
            
            if (token) {
                await axios.post('/api/logout', {}, {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });
            }
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            // Clear local storage and redirect
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = '/login';
        }
    }
</script>
@endsection

