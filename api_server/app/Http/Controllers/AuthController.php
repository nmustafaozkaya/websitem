<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Email veya şifre hatalı'
            ], 401);
        }

        $user = Auth::user();
        
        // Kullanıcı aktif mi kontrol et
        if (!$user->is_active) {
            Auth::logout();
            return response()->json([
                'success' => false,
                'message' => 'Hesabınız devre dışı bırakılmış'
            ], 403);
        }

        // Token oluştur
        $token = $user->createToken('auth_token')->plainTextToken;

        // Kullanıcı bilgilerini role ve cinema ile birlikte al
        $userData = $user->load(['role', 'cinema']);

        return response()->json([
            'success' => true,
            'message' => 'Giriş başarılı',
            'data' => [
                'user' => $userData,
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'cinema_id' => 'nullable|exists:cinemas,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Varsayılan olarak user rolü
        $userRole = Role::where('name', 'user')->first();
        if (!$userRole) {
            return response()->json([
                'success' => false,
                'message' => 'Varsayılan rol bulunamadı'
            ], 500);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $userRole->id,
            'cinema_id' => $request->cinema_id,
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'is_active' => true,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        // Kullanıcı bilgilerini role ve cinema ile birlikte al
        $userData = $user->load(['role', 'cinema']);

        return response()->json([
            'success' => true,
            'message' => 'Kayıt başarılı',
            'data' => [
                'user' => $userData,
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Çıkış başarılı'
        ]);
    }

    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tüm cihazlardan çıkış başarılı'
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load(['role', 'cinema']);

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update($request->only(['name', 'email', 'phone', 'birth_date', 'gender']));

        return response()->json([
            'success' => true,
            'message' => 'Profil güncellendi',
            'data' => $user->load(['role', 'cinema'])
        ]);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Mevcut şifre hatalı'
            ], 400);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Şifre başarıyla değiştirildi'
        ]);
    }

    public function verifyToken(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Token geçerli',
            'data' => $request->user()->load(['role', 'cinema'])
        ]);
    }

    public function permissions(Request $request)
    {
        $user = $request->user()->load('role');
        
        return response()->json([
            'success' => true,
            'data' => [
                'role' => $user->role->name,
                'role_display_name' => $user->role->display_name,
                'permissions' => $user->role->permissions ?? [],
                'can' => [
                    'manage_movies' => $user->canManageMovies(),
                    'sell_tickets' => $user->canSellTickets(),
                    'view_reports' => $user->canViewReports(),
                    'manage_users' => $user->canManageUsers(),
                    'manage_system' => $user->canManageSystem(),
                    'delete_movies' => $user->canDeleteMovies(),
                ]
            ]
        ]);
    }

    public function refresh(Request $request)
    {
        // Mevcut token'ı sil
        $request->user()->currentAccessToken()->delete();
        
        // Yeni token oluştur
        $token = $request->user()->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Token yenilendi',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }
}