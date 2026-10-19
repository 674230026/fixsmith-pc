<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RepairController;
use App\Http\Controllers\SmtpController;
use App\Models\Feedback;
use App\Models\Notification;
use App\Models\Repair;
use App\Models\SmtpSetting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // If not logged in, default to Maya Chen (Customer) for immediate live preview or keep as guest
    $currentUser = Auth::user();
    if (!$currentUser) {
        $currentUser = User::where('role', 'customer')->first();
    }

    $initialData = [
        'user' => $currentUser,
        'role' => $currentUser ? $currentUser->role : 'customer',
        'repairs' => Repair::orderBy('created_at', 'desc')->get(),
        'feedback' => Feedback::orderBy('created_at', 'desc')->get(),
        'accounts' => User::orderBy('name')->get(),
        'notifications' => Notification::orderBy('created_at', 'desc')->get(),
        'smtp' => SmtpSetting::current(),
    ];

    return view('app', ['initialData' => $initialData]);
});

// Auth Routes
Route::prefix('api/auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/preview', [AuthController::class, 'previewLogin']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'currentUser']);
});

// Repairs API
Route::prefix('api/repairs')->group(function () {
    Route::get('/', [RepairController::class, 'index']);
    Route::post('/', [RepairController::class, 'store']);
    Route::post('/{id}/status', [RepairController::class, 'updateStatus']);
    Route::post('/{id}/assign', [RepairController::class, 'assignTechnician']);
    Route::post('/{id}/receive', [RepairController::class, 'receivePackage']);
    Route::post('/workload', [RepairController::class, 'updateTeamWorkload']);
});

// Feedback & Complaints API
Route::prefix('api/feedback')->group(function () {
    Route::get('/', [FeedbackController::class, 'index']);
    Route::post('/', [FeedbackController::class, 'store']);
    Route::post('/{id}/reply', [FeedbackController::class, 'reply']);
});

// Accounts & Profile API
Route::prefix('api/accounts')->group(function () {
    Route::get('/', [AccountController::class, 'index']);
    Route::post('/', [AccountController::class, 'store']);
    Route::put('/{id}', [AccountController::class, 'update']);
});
Route::post('/api/profile', [AccountController::class, 'updateProfile']);

// Notifications API
Route::prefix('api/notifications')->group(function () {
    Route::get('/', [NotificationController::class, 'index']);
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
});

// SMTP & Email Testing API
Route::prefix('api/smtp')->group(function () {
    Route::get('/', [SmtpController::class, 'getSettings']);
    Route::post('/', [SmtpController::class, 'updateSettings']);
    Route::post('/test', [SmtpController::class, 'sendTestEmail']);
    Route::get('/logs', [SmtpController::class, 'getLogs']);
});
