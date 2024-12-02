<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Http\Resources\Mobile\MeetingResource;
use App\Services\Meeting\StatisticsService;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    protected $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    public function index(Request $request)
    {
        $meetings = Meeting::with(['participants', 'documents'])
            ->when($request->start_date, function($query) use ($request) {
                return $query->where('start_time', '>=', $request->start_date);
            })
            ->when($request->end_date, function($query) use ($request) {
                return $query->where('end_time', '<=', $request->end_date);
            })
            ->when($request->status, function($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->orderBy('start_time', 'desc')
            ->paginate();

        return MeetingResource::collection($meetings);
    }

    public function upcoming()
    {
        $meetings = Meeting::with(['participants', 'documents'])
            ->where('start_time', '>', now())
            ->orderBy('start_time')
            ->take(5)
            ->get();

        return MeetingResource::collection($meetings);
    }

    public function show(Meeting $meeting)
    {
        $meeting->load(['participants', 'documents', 'recording']);
        $stats = $this->statisticsService->getMeetingStats($meeting);

        return (new MeetingResource($meeting))
            ->additional(['stats' => $stats]);
    }

    public function join(Meeting $meeting)
    {
        if (!$meeting->canJoin()) {
            return response()->json([
                'message' => 'Meeting is not currently active'
            ], 400);
        }

        return response()->json([
            'join_url' => $meeting->join_url,
            'token' => $meeting->generateJoinToken(auth()->user())
        ]);
    }

    public function submitFeedback(Meeting $meeting, Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        $feedback = $meeting->feedback()->create([
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return response()->json($feedback);
    }
}