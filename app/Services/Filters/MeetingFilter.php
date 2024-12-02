<?php

namespace App\Services\Filters;

use App\Models\Meeting;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MeetingFilter
{
    protected $query;
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->query = Meeting::query();
    }

    public function apply()
    {
        $this->applyDateFilter()
             ->applyStatusFilter()
             ->applyTypeFilter()
             ->applyParticipantFilter()
             ->applyDurationFilter()
             ->applySearchFilter()
             ->applySortOrder();

        return $this->query;
    }

    protected function applyDateFilter()
    {
        if ($this->request->has('date_range')) {
            $range = $this->request->date_range;
            $this->query->whereBetween('start_time', [
                Carbon::parse($range['start']),
                Carbon::parse($range['end'])
            ]);
        } elseif ($this->request->has('period')) {
            switch ($this->request->period) {
                case 'today':
                    $this->query->whereDate('start_time', Carbon::today());
                    break;
                case 'week':
                    $this->query->whereBetween('start_time', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    break;
                case 'month':
                    $this->query->whereMonth('start_time', Carbon::now()->month)
                               ->whereYear('start_time', Carbon::now()->year);
                    break;
            }
        }

        return $this;
    }

    protected function applyStatusFilter()
    {
        if ($this->request->has('status')) {
            $this->query->where('status', $this->request->status);
        }

        return $this;
    }

    protected function applyTypeFilter()
    {
        if ($this->request->has('type')) {
            $this->query->where('type', $this->request->type);
        }

        return $this;
    }

    protected function applyParticipantFilter()
    {
        if ($this->request->has('participant')) {
            $this->query->whereHas('participants', function($query) {
                $query->where('users.id', $this->request->participant);
            });
        }

        return $this;
    }

    protected function applyDurationFilter()
    {
        if ($this->request->has('duration')) {
            switch ($this->request->duration) {
                case 'short':
                    $this->query->where('duration', '<=', 30);
                    break;
                case 'medium':
                    $this->query->whereBetween('duration', [31, 60]);
                    break;
                case 'long':
                    $this->query->where('duration', '>', 60);
                    break;
            }
        }

        return $this;
    }

    protected function applySearchFilter()
    {
        if ($this->request->has('search')) {
            $search = $this->request->search;
            $this->query->where(function($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhereHas('participants', function($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                      });
            });
        }

        return $this;
    }

    protected function applySortOrder()
    {
        $sortField = $this->request->get('sort_by', 'start_time');
        $sortOrder = $this->request->get('sort_order', 'desc');

        $this->query->orderBy($sortField, $sortOrder);

        return $this;
    }
}