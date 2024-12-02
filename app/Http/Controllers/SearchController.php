<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\Document;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('query');
        $type = $request->get('type', 'all');

        $results = [];

        if ($type === 'all' || $type === 'meetings') {
            $results['meetings'] = Meeting::search($query)
                ->orderBy('start_time', 'desc')
                ->take(5)
                ->get();
        }

        if ($type === 'all' || $type === 'documents') {
            $results['documents'] = Document::search($query)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        }

        return response()->json($results);
    }

    public function advancedSearch(Request $request)
    {
        $query = $request->get('query');
        $filters = $request->get('filters', []);
        $type = $request->get('type', 'all');

        $results = [];

        if ($type === 'all' || $type === 'meetings') {
            $meetings = Meeting::query();
            
            // Apply filters
            if (!empty($filters['date_range'])) {
                $meetings->whereBetween('start_time', [
                    $filters['date_range']['start'],
                    $filters['date_range']['end']
                ]);
            }

            if (!empty($filters['participants'])) {
                $meetings->whereHas('participants', function($query) use ($filters) {
                    $query->whereIn('users.id', $filters['participants']);
                });
            }

            if (!empty($filters['status'])) {
                $meetings->where('status', $filters['status']);
            }

            $results['meetings'] = $meetings->paginate(15);
        }

        return response()->json($results);
    }
}