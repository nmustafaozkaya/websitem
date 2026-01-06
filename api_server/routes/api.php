<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\CinemaController;
use App\Http\Controllers\Api\ShowtimeController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FutureMoviesController;
use App\Http\Controllers\Api\HallController;
use App\Http\Controllers\Api\SeatController;
use App\Http\Controllers\Api\TaxController;

// Auth routes (PUBLIC)
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);


// Public routes - Movies
Route::get('movies', [MovieController::class, 'index']);
Route::get('movies/distributed', [MovieController::class, 'distributed']); // Toplam 100 filmi tarihe göre dağıt
Route::get('movies/{id}', [MovieController::class, 'show']);
Route::get('movies/{movie}/cinemas', [MovieController::class, 'getCinemasForMovie']);
Route::get('movies/{movie}/showtimes', [MovieController::class, 'getShowtimesForMovie']);
Route::get('movies/{movie}/cinemas/{cinema}/showtimes', [MovieController::class, 'getShowtimesForMovieAndCinema']);
Route::get('movies/{movie}/stats', [MovieController::class, 'getMovieStats']);

// Public routes - Cities
Route::get('cities', [CinemaController::class, 'cities']);

// Public routes - Cinemas
Route::get('cinemas', [CinemaController::class, 'index']);
Route::get('cinemas/{id}', [CinemaController::class, 'show']);
Route::get('cinemas/showing/{movieId}', [CinemaController::class, 'showingMovie']);

// Public routes - Halls
Route::get('halls', [HallController::class, 'index']);
Route::get('halls/{id}', [HallController::class, 'show']);

// Public routes - Showtimes
Route::get('showtimes', [ShowtimeController::class, 'index']);
Route::get('showtimes/{id}', [ShowtimeController::class, 'show']);
Route::get('showtimes/{id}/available-seats', [ShowtimeController::class, 'availableSeats']);

// Koltuk yönetimi route'ları (PUBLIC)
Route::post('showtimes/{showtime}/reserve', [ShowtimeController::class, 'reserveSeat']);
Route::post('showtimes/{showtime}/purchase', [ShowtimeController::class, 'purchaseSeat']);
Route::post('showtimes/{showtime}/release-expired', [ShowtimeController::class, 'releaseExpiredSeatsForShowtime']);

// Seat cleanup routes (PUBLIC)
Route::post('seats/{seat}/release', [SeatController::class, 'releaseSeat']);
Route::post('seats/cleanup-expired', [SeatController::class, 'cleanupExpiredSeats']);
Route::post('seats/auto-cleanup', [SeatController::class, 'autoCleanupOnPageLoad']);
Route::post('halls/{hallId}/cleanup-expired', [SeatController::class, 'cleanupExpiredSeatsForHall']);
Route::get('seats/check-pending', [SeatController::class, 'checkPendingSeats']);

// Ticket price information (PUBLIC)
Route::get('tickets/prices/{showtime_id}', [TicketController::class, 'getTicketPrices']);

// Tax routes (PUBLIC)
Route::get('taxes', [TaxController::class, 'index']);
Route::post('taxes/calculate', [TaxController::class, 'calculateTotal']);

// Future Movies routes (PUBLIC)
Route::prefix('future-movies')->group(function () {
    Route::get('/', [FutureMoviesController::class, 'index']);
    Route::get('/coming-soon', [FutureMoviesController::class, 'comingSoon']);
    Route::get('/pre-order', [FutureMoviesController::class, 'preOrder']);
    Route::get('/genres', [FutureMoviesController::class, 'genres']);
    Route::get('/statuses', [FutureMoviesController::class, 'statuses']);
    Route::get('/{id}', [FutureMoviesController::class, 'show']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth routes
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('logout-all', [AuthController::class, 'logoutAll']);
    Route::get('me', [AuthController::class, 'me']);
    Route::put('profile', [AuthController::class, 'updateProfile']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('change-password', [AuthController::class, 'changePassword']);
    Route::get('verify-token', [AuthController::class, 'verifyToken']);
    Route::get('permissions', [AuthController::class, 'permissions']);
    
    Route::get('user', function (Request $request) {
        return $request->user();
    });
    
    // Ticket operations - Customer da bilet alabilir
    Route::post('tickets', [TicketController::class, 'store']); // Herkes bilet alabilir
    Route::get('my-tickets', [TicketController::class, 'myTickets']); // Kendi biletlerini görebilir
    
    // Admin-only operations
    Route::middleware(['admin'])->group(function () {
        
        // Hall management
        Route::post('halls', [HallController::class, 'store']);   
        Route::put('halls/{id}', [HallController::class, 'update']);
        Route::delete('halls/{id}', [HallController::class, 'destroy']);
        
        // Movie management
        Route::post('movies', [MovieController::class, 'store']);
        Route::put('movies/{id}', [MovieController::class, 'update']);
        Route::delete('movies/{id}', [MovieController::class, 'destroy']);
        
        // Showtime management
        Route::post('showtimes', [ShowtimeController::class, 'store']);
        Route::put('showtimes/{id}', [ShowtimeController::class, 'update']);
        Route::delete('showtimes/{id}', [ShowtimeController::class, 'destroy']);
        
        // Admin ticket operations
        Route::get('tickets', [TicketController::class, 'index']); // Tüm biletleri görebilir
        Route::get('tickets/{id}', [TicketController::class, 'show']); // Bilet detayı
        Route::put('tickets/{id}', [TicketController::class, 'update']); // Bilet düzenle
        Route::delete('tickets/{id}', [TicketController::class, 'destroy']); // Bilet sil
    });
});