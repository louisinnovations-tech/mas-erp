<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('meeting.{meetingId}', function ($user, $meetingId) {
    $meeting = App\Models\Meeting::find($meetingId);
    return $meeting && $meeting->participants->contains($user->id);
});

Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});