<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MeetingController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\CalendarController;

Route::middleware('auth:sanctum')->group(function () {
    // Meeting Routes
    Route::apiResource('meetings', MeetingController::class);
    Route::post('meetings/{meeting}/join', [MeetingController::class, 'join']);
    Route::post('meetings/{meeting}/documents', [MeetingController::class, 'uploadDocument']);
    Route::delete('meetings/{meeting}/documents/{document}', [MeetingController::class, 'deleteDocument']);
    Route::post('meetings/{meeting}/participants', [MeetingController::class, 'addParticipant']);
    Route::delete('meetings/{meeting}/participants/{user}', [MeetingController::class, 'removeParticipant']);

    // Calendar Routes
    Route::post('calendar/sync/google', [CalendarController::class, 'syncWithGoogle']);
    Route::post('calendar/sync/outlook', [CalendarController::class, 'syncWithOutlook']);
    Route::get('calendar/meetings', [CalendarController::class, 'listMeetings']);

    // Notification Routes
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::post('notifications/mark-read', [NotificationController::class, 'markAsRead']);
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    
    // User Preferences
    Route::get('user/calendar-connections', [CalendarController::class, 'getConnections']);
    Route::delete('user/calendar-connections/{provider}', [CalendarController::class, 'disconnect']);
});