<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * User login
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Kullanıcı aktif mi kontrol et
        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Account is deactivated'
            ], 403);
        }

        // Eski token'ları temizle
        $user->tokens()->delete();

        // Yeni token oluştur
        $abilities = $this->getUserAbilities($user);
        $token = $user->createToken('api-token', $abilities)->plainTextToken;

        $user->load(['role', 'cinema.city']);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    /**
     * User registration
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'cinema_id' => 'nullable|exists:cinemas,id',
            'role_id' => 'nullable|exists:roles,id'
        ]);

        // Varsayılan rol (customer)
        $defaultRole = Role::where('name', 'customer')->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'cinema_id' => $request->cinema_id,
            'role_id' => $request->role_id ?? $defaultRole->id,
            'is_active' => true
        ]);

        // Token oluştur
        $abilities = $this->getUserAbilities($user);
        $token = $user->createToken('api-token', $abilities)->plainTextToken;

        $user->load(['role', 'cinema.city']);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ], 201);
    }

    /**
     * User logout
     */
    public function logout(Request $request): JsonResponse
{
    // Güvenli silme
    $token = $request->user()->currentAccessToken();
    if ($token) {
        $token->delete();
    }

    return response()->json([
        'success' => true,
        'message' => 'Logout successful'
    ]);
}

    /**
     * User logout from all devices
     */
    public function logoutAll(Request $request): JsonResponse
    {
        // Tüm token'ları sil
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out from all devices'
        ]);
    }

    /**
     * Get current user info
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load(['role.permissions', 'cinema.city']);

        return response()->json([
            'success' => true,
            'message' => 'User info retrieved successfully',
            'data' => $user
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
        ]);

        $updateData = $request->only(['name', 'email', 'phone', 'birth_date', 'gender']);

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);
        $user->load(['role', 'cinema.city']);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $user
        ]);
    }

    /**
     * Refresh token
     */
    public function refresh(Request $request): JsonResponse
{
    $user = $request->user();
    
    // Güvenli silme
    $currentToken = $request->user()->currentAccessToken();
    if ($currentToken) {
        $currentToken->delete();
    }

    // Yeni token oluştur
    $abilities = $this->getUserAbilities($user);
    $token = $user->createToken('api-token', $abilities)->plainTextToken;

    return response()->json([
        'success' => true,
        'message' => 'Token refreshed successfully',
        'data' => [
            'token' => $token,
            'token_type' => 'Bearer'
        ]
    ]);
}

    /**
     * Change password
     */
    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:5|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        // Güvenlik için tüm token'ları sil
        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully. Please login again.'
        ]);
    }

    /**
     * Get user abilities based on role
     */
    private function getUserAbilities(User $user): array
    {
        $user->load(['role.permissions']);

        if (!$user->role) {
            return ['view_movies'];
        }

        $permissions = $user->role->permissions->pluck('name')->toArray();

        // Varsayılan yetkiler ekle
        $defaultAbilities = ['view_movies'];

        return array_unique(array_merge($defaultAbilities, $permissions));
    }

    /**
     * Verify token
     */
    public function verifyToken(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Token is valid',
            'data' => [
                'user_id' => $user->id,
                'abilities' => $user->currentAccessToken()->abilities
            ]
        ]);
    }

    /**
     * Get user permissions
     */
    public function permissions(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load(['role.permissions']);

        $permissions = $user->role ? $user->role->permissions : collect();

        return response()->json([
            'success' => true,
            'message' => 'User permissions retrieved successfully',
            'data' => [
                'role' => $user->role?->name,
                'permissions' => $permissions
            ]
        ]);
    }
}