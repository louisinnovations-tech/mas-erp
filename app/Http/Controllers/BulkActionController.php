<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\Request;
use App\Services\Calendar\GoogleCalendarService;
use App\Services\Calendar\OutlookCalendarService;

class BulkActionController extends Controller
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

    public function process(Request $request)
    {
        $request->validate([
            'action' => 'required|string',
            'items' => 'required|array',
            'items.*' => 'required|integer'
        ]);

        $action = $request->action;
        $items = $request->items;

        switch ($action) {
            case 'delete':
                return $this->bulkDelete($items);

            case 'sync_google':
                return $this->bulkSyncGoogle($items);

            case 'sync_outlook':
                return $this->bulkSyncOutlook($items);

            case 'export':
                return $this->bulkExport($items);

            default:
                return response()->json([
                    'message' => 'Invalid action specified'
                ], 400);
        }
    }

    protected function bulkDelete(array $items)
    {
        $meetings = Meeting::whereIn('id', $items)->get();
        
        foreach ($meetings as $meeting) {
            if (auth()->user()->can('delete', $meeting)) {
                $meeting->delete();
            }
        }

        return response()->json([
            'message' => 'Selected meetings deleted successfully'
        ]);
    }

    protected function bulkSyncGoogle(array $items)
    {
        $meetings = Meeting::whereIn('id', $items)->get();
        $results = [];

        foreach ($meetings as $meeting) {
            try {
                $eventId = $this->googleCalendar->createEvent($meeting, auth()->user());
                $results[$meeting->id] = ['status' => 'success', 'event_id' => $eventId];
            } catch (\Exception $e) {
                $results[$meeting->id] = ['status' => 'error', 'message' => $e->getMessage()];
            }
        }

        return response()->json($results);
    }

    protected function bulkSyncOutlook(array $items)
    {
        $meetings = Meeting::whereIn('id', $items)->get();
        $results = [];

        foreach ($meetings as $meeting) {
            try {
                $eventId = $this->outlookCalendar->createEvent($meeting, auth()->user());
                $results[$meeting->id] = ['status' => 'success', 'event_id' => $eventId];
            } catch (\Exception $e) {
                $results[$meeting->id] = ['status' => 'error', 'message' => $e->getMessage()];
            }
        }

        return response()->json($results);
    }
}