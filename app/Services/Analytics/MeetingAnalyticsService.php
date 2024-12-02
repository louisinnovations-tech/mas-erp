<?php

namespace App\Services\Analytics;

use App\Models\Meeting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MeetingAnalyticsService
{
    public function getOverallStats($dateRange = null)
    {
        $query = Meeting::query();
        
        if ($dateRange) {
            $query->whereBetween('start_time', [
                Carbon::parse($dateRange['start']),
                Carbon::parse($dateRange['end'])
            ]);
        }

        return [
            'total_meetings' => $query->count(),
            'total_duration' => $query->sum('duration'),
            'total_participants' => $this->getTotalUniqueParticipants($query),
            'average_duration' => $query->avg('duration'),
            'average_participants' => $this->getAverageParticipants($query),
            'completion_rate' => $this->getCompletionRate($query),
            'busiest_days' => $this->getBusiestDays($query),
            'type_distribution' => $this->getTypeDistribution($query)
        ];
    }

    public function getUserParticipationStats($userId, $dateRange = null)
    {
        $query = Meeting::whereHas('participants', function($q) use ($userId) {
            $q->where('users.id', $userId);
        });

        if ($dateRange) {
            $query->whereBetween('start_time', [
                Carbon::parse($dateRange['start']),
                Carbon::parse($dateRange['end'])
            ]);
        }

        return [
            'attended_meetings' => $query->count(),
            'total_time_spent' => $query->sum('duration'),
            'organized_meetings' => $query->where('organizer_id', $userId)->count(),
            'participation_rate' => $this->getUserParticipationRate($userId, $query),
            'contribution_metrics' => $this->getUserContributionMetrics($userId),
            'meeting_preferences' => $this->getUserMeetingPreferences($userId)
        ];
    }

    protected function getTotalUniqueParticipants($query)
    {
        return DB::table('meeting_user')
            ->whereIn('meeting_id', $query->pluck('id'))
            ->distinct()
            ->count('user_id');
    }

    protected function getAverageParticipants($query)
    {
        $meetings = $query->withCount('participants')->get();
        return $meetings->avg('participants_count');
    }

    protected function getCompletionRate($query)
    {
        $total = $query->count();
        if ($total === 0) return 0;

        $completed = $query->where('status', 'completed')->count();
        return ($completed / $total) * 100;
    }

    protected function getBusiestDays($query)
    {
        return $query
            ->select(DB::raw('DAYOFWEEK(start_time) as day, COUNT(*) as count'))
            ->groupBy('day')
            ->orderBy('count', 'desc')
            ->get()
            ->mapWithKeys(function ($item) {
                return [Carbon::getDays()[$item->day - 1] => $item->count];
            });
    }

    protected function getTypeDistribution($query)
    {
        return $query
            ->select('type', DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->get()
            ->pluck('count', 'type');
    }

    protected function getUserParticipationRate($userId, $query)
    {
        $attended = $query->whereHas('attendees', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->count();

        $total = $query->count();
        return $total > 0 ? ($attended / $total) * 100 : 0;
    }

    protected function getUserContributionMetrics($userId)
    {
        return [
            'documents_shared' => Document::where('user_id', $userId)->count(),
            'comments_made' => Comment::where('user_id', $userId)->count(),
            'notes_added' => Note::where('user_id', $userId)->count()
        ];
    }

    protected function getUserMeetingPreferences($userId)
    {
        $meetings = Meeting::whereHas('participants', function($q) use ($userId) {
            $q->where('users.id', $userId);
        })->get();

        return [
            'preferred_time' => $this->calculatePreferredTime($meetings),
            'preferred_duration' => $this->calculatePreferredDuration($meetings),
            'preferred_type' => $this->calculatePreferredType($meetings)
        ];
    }
}