<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Calendar\GoogleCalendarService;
use App\Services\Calendar\OutlookCalendarService;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    protected $googleCalendar;
    protected $outlookCalendar;

    public function __construct(
        GoogleCalendarService $googleCalendar,
        OutlookCalendarService $outlookCalendar
    ) {
        $this->googleCalendar = $googleCalendar;
        $this->outlookCalendar = $outlookCalendar;
    }

    public function syncWithGoogle(Request $request)
    {
        try {
            $eventId = $this->googleCalendar->createEvent(
                $request->meeting,
                auth()->user()
            );

            return response()->json([
                'message' => 'Meeting synced with Google Calendar',
                'event_id' => $eventId
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to sync with Google Calendar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function syncWithOutlook(Request $request)
    {
        try {
            $eventId = $this->outlookCalendar->createEvent(
                $request->meeting,
                auth()->user()
            );

            return response()->json([
                'message' => 'Meeting synced with Outlook Calendar',
                'event_id' => $eventId
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to sync with Outlook Calendar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getConnections()
    {
        $user = auth()->user();
        
        return response()->json([
            'google' => !empty($user->google_calendar_token),
            'outlook' => !empty($user->outlook_calendar_token)
        ]);
    }

    public function disconnect($provider)
    {
        $user = auth()->user();
        
        switch ($provider) {
            case 'google':
                $user->update([
                    'google_calendar_token' => null,
                    'google_calendar_refresh_token' => null
                ]);
                break;
                
            case 'outlook':
                $user->update([
                    'outlook_calendar_token' => null,
                    'outlook_calendar_refresh_token' => null
                ]);
                break;
                
            default:
                return response()->json([
                    'message' => 'Invalid provider'
                ], 400);
        }

        return response()->json([
            'message' => 'Successfully disconnected from ' . ucfirst($provider) . ' Calendar'
        ]);
    }
}