<?php

namespace App\Services\Calendar;

use App\Models\Meeting;
use App\Models\User;

interface CalendarServiceInterface
{
    public function createEvent(Meeting $meeting, User $user);
    public function updateEvent(Meeting $meeting, User $user, string $eventId);
}