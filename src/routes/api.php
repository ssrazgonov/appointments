<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Client\AuthController as ClientAuthController;
use App\Http\Controllers\Api\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\MasterProfileController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\Public\BookingController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SubscriptionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

// Public Booking API
Route::prefix('public')->group(function () {
    Route::get('master/{slug}', [BookingController::class, 'getMasterBySlug']);
    Route::get('services/{userId}', [BookingController::class, 'getServices']);
    Route::get('available-slots', [BookingController::class, 'getAvailableSlots']);
    Route::post('book', [BookingController::class, 'createBooking']);
});

// Robokassa webhooks (public, signature verified in controller)
Route::prefix('payments')->group(function () {
    Route::post('robokassa/result', [PaymentController::class, 'robokassaResult']);
    Route::post('robokassa/success', [PaymentController::class, 'robokassaSuccess']);
    Route::post('robokassa/fail', [PaymentController::class, 'robokassaFail']);
});

// Protected routes
Route::middleware('auth:api')->group(function () {
    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
    });

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index']);

    // Master Profile
    Route::prefix('master-profile')->group(function () {
        Route::get('/', [MasterProfileController::class, 'show']);
        Route::post('/', [MasterProfileController::class, 'store']);
        Route::put('/', [MasterProfileController::class, 'update']);
        
        Route::prefix('working-hours')->group(function () {
            Route::get('/', [MasterProfileController::class, 'getWorkingHours']);
            Route::put('/', [MasterProfileController::class, 'updateWorkingHours']);
        });
        
        Route::prefix('services')->group(function () {
            Route::get('/', [MasterProfileController::class, 'getServices']);
            Route::post('/', [MasterProfileController::class, 'storeService']);
            Route::put('/{id}', [MasterProfileController::class, 'updateService']);
            Route::delete('/{id}', [MasterProfileController::class, 'deleteService']);
        });
    });

    // Clients
    Route::apiResource('clients', ClientController::class);

    // Appointments
    Route::prefix('appointments')->group(function () {
        Route::get('/', [AppointmentController::class, 'index']);
        Route::get('/today', [AppointmentController::class, 'today']);
        Route::get('/upcoming', [AppointmentController::class, 'upcoming']);
        Route::post('/', [AppointmentController::class, 'store']);
        Route::get('/{id}', [AppointmentController::class, 'show']);
        Route::put('/{id}', [AppointmentController::class, 'update']);
        Route::delete('/{id}', [AppointmentController::class, 'destroy']);
        Route::post('/{id}/cancel', [AppointmentController::class, 'cancel']);
        Route::post('/{id}/complete', [AppointmentController::class, 'complete']);
    });

    // Subscriptions
    Route::prefix('subscriptions')->group(function () {
        Route::get('/plans', [SubscriptionController::class, 'plans']);
        Route::get('/current', [SubscriptionController::class, 'current']);
        Route::get('/has-active', [SubscriptionController::class, 'hasActive']);
    });

    // Payments
    Route::prefix('payments')->group(function () {
        Route::get('/', [PaymentController::class, 'index']);
        Route::post('/create', [PaymentController::class, 'create']);
        Route::get('/{id}', [PaymentController::class, 'show']);
    });

    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/monthly', [ReportController::class, 'monthly']);
        Route::get('/yearly', [ReportController::class, 'yearly']);
        Route::get('/appointments', [ReportController::class, 'appointments']);
    });
});

// Client Cabinet API (JWT authenticated)
Route::prefix('client')->group(function () {
    // Public auth routes
    Route::post('register', [ClientAuthController::class, 'register']);
    Route::post('login', [ClientAuthController::class, 'login']);
    Route::post('send-verification-code', [ClientAuthController::class, 'sendVerificationCode']);

    // Protected routes
    Route::middleware('auth:client')->group(function () {
        Route::get('me', [ClientAuthController::class, 'me']);
        Route::post('logout', [ClientAuthController::class, 'logout']);

        // Dashboard
        Route::get('dashboard', [ClientDashboardController::class, 'index']);
        Route::get('masters', [ClientDashboardController::class, 'masters']);
        Route::get('appointments', [ClientDashboardController::class, 'appointmentHistory']);
    });
});
