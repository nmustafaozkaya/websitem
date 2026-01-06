<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\PosterUpdateController;
use App\Http\Controllers\UpdateFutureMoviePosters;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('register', function () {
    return view('register');
})->name('register');

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/movies', function () {
    return view('movies');
})->name('movies');

Route::get('/tickets', function () {
    return view('tickets');
})->name('tickets');

// ✅ Admin sayfasını koruyalım
Route::get('/admin', function () {
    return view('admin');
})->middleware('admin')->name('admin');

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/ticket', function () {
    return view('buy-tiickets');
})->name('ticket');



Route::post('/login', function(Request $request) {
    $credentials = $request->only('email', 'password');
    
    if (Auth::attempt($credentials)) {

        return response()->json(['success' => true]);
    }
    
    return response()->json(['success' => false], 401);
});

Route::get('/logout', function() {
    Auth::logout();
    return redirect('/login');
});

Route::get('/my-tickets', function() {
    return view('my-tickets');
})->middleware('auth');

Route::get('/buy-tickets', function () {
    return view('tickets');  // Aynı sayfayı kullan
})->middleware('auth');

Route::get('/profile', function() {
    return view('profile');
})->middleware('auth');

Route::get('/test-data', function() {
    return response()->json([
        'movies_count' => \App\Models\Movie::count(),
        'halls_count' => \App\Models\Hall::count() ?? 'Hall model yok',
        'cinemas_count' => \App\Models\Cinema::count()
    ]);
});