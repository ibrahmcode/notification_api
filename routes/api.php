<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Test endpoint
Route::get('/test', function () {
    return response()->json(['status' => 'API is working!', 'timestamp' => now()]);
});

// FCM Notification Routes
Route::prefix('notifications')->group(function () {
    
    // Send notification to single device
    Route::post('/send-to-device', [NotificationController::class, 'sendToDevice']);
    
    // Send notification to multiple devices
    Route::post('/send-to-multiple', [NotificationController::class, 'sendToMultipleDevices']);
    
    // Send notification to a topic
    Route::post('/send-to-topic', [NotificationController::class, 'sendToTopic']);
    
    // Send notification to a specific user
    Route::post('/send-to-user', [NotificationController::class, 'sendToUser']);
    
    // Send notification to all users
    Route::post('/send-to-all', [NotificationController::class, 'sendToAllUsers']);
    
    // Subscribe to topic
    Route::post('/subscribe-topic', [NotificationController::class, 'subscribeToTopic']);
    
    // Unsubscribe from topic
    Route::post('/unsubscribe-topic', [NotificationController::class, 'unsubscribeFromTopic']);
    
    // Save FCM token for authenticated user
    Route::post('/save-token', [NotificationController::class, 'saveFcmToken'])
        ->middleware('auth:sanctum');
    
    // Development/Testing endpointsNotificationController
    Route::post('/generate-test-token', [NotificationController::class, 'generateTestToken']);
    Route::get('/users-with-tokens', [NotificationController::class, 'getUsersWithTokens']);
    Route::post('/set-user-token', [NotificationController::class, 'setUserToken']);
});
