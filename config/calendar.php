<?php

return [
    'google' => [
        'client_id' => env('GOOGLE_CALENDAR_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CALENDAR_CLIENT_SECRET'),
        'redirect_uri' => env('GOOGLE_CALENDAR_REDIRECT_URI'),
    ],
    
    'outlook' => [
        'client_id' => env('OUTLOOK_CALENDAR_CLIENT_ID'),
        'client_secret' => env('OUTLOOK_CALENDAR_CLIENT_SECRET'),
        'redirect_uri' => env('OUTLOOK_CALENDAR_REDIRECT_URI'),
    ],
    
    'default_reminder_minutes' => [
        30,  // 30 minutes before
        24 * 60  // 24 hours before
    ],
    
    'sync_interval' => 5, // minutes
];
