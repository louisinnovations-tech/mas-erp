<?php

namespace App\Services\Meeting;

use App\Models\Meeting;
use Carbon\Carbon;

class StatisticsService
{
    public function getMeetingStats(Meeting $meeting)
    {
        return [
            'duration' => $meeting->actual_duration,
            'participant_count' => $meeting->participants()->count(),
            'document_count' => $meeting->documents()->count(),
            'recording' => $meeting->recording ? [
                'duration' => $meeting->recording->duration,
                'size' => $meeting->recording->size_formatted
            ] : null,
            'participant_stats' => $this->getParticipantStats($meeting),
            'engagement_metrics' => $this->getEngagementMetrics($meeting)
        ];
    }

    public function getParticipantStats(Meeting $meeting)
    {
        return [
            'total' => $meeting->participants()->count(),
            'attended' => $meeting->attendees()->count(),
            'attendance_rate' => $this->calculateAttendanceRate($meeting),
            'average_duration' => $this->calculateAverageDuration($meeting)
        ];
    }

    public function getEngagementMetrics(Meeting $meeting)
    {
        return [
            'chat_messages' => $meeting->chatMessages()->count(),
            'reactions' => $meeting->reactions()->count(),
            'questions_asked' => $meeting->questions()->count(),
            'document_views' => $this->calculateDocumentViews($meeting)
        ];
    }

    protected function calculateAttendanceRate(Meeting $meeting)
    {
        $total = $meeting->participants()->count();
        if ($total === 0) return 0;

        $attended = $meeting->attendees()->count();
        return round(($attended / $total) * 100, 2);
    }

    protected function calculateAverageDuration(Meeting $meeting)
    {
        return $meeting->attendees()
            ->avg('duration') ?? 0;
    }

    protected function calculateDocumentViews(Meeting $meeting)
    {
        return $meeting->documents()
            ->withCount('views')
            ->get()
            ->sum('views_count');
    }
}