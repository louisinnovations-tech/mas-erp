<?php

namespace App\Services\Calendar;

use App\Models\Meeting;
use App\Models\User;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;

class OutlookCalendarService implements CalendarServiceInterface
{
    protected $graph;

    public function __construct()
    {
        $this->graph = new Graph();
    }

    public function createEvent(Meeting $meeting, User $user)
    {
        if (!$user->outlook_token) {
            throw new \Exception('User not connected to Outlook Calendar');
        }

        $this->graph->setAccessToken($user->outlook_token);

        $event = new Model\Event([
            'subject' => $meeting->title,
            'body' => [
                'contentType' => 'HTML',
                'content' => $meeting->description
            ],
            'start' => [
                'dateTime' => $meeting->start_time->format('Y-m-d\TH:i:s'),
                'timeZone' => config('app.timezone')
            ],
            'end' => [
                'dateTime' => $meeting->end_time->format('Y-m-d\TH:i:s'),
                'timeZone' => config('app.timezone')
            ],
            'attendees' => $this->formatAttendees($meeting->participants)
        ]);

        $response = $this->graph->createRequest('POST', '/me/calendar/events')
            ->attachBody($event)
            ->execute();

        return $response->getBody()['id'];
    }

    protected function formatAttendees($participants)
    {
        return $participants->map(function($participant) {
            return [
                'emailAddress' => [
                    'address' => $participant->email,
                    'name' => $participant->name
                ],
                'type' => 'required'
            ];
        })->toArray();
    }

    public function updateEvent(Meeting $meeting, User $user, string $eventId)
    {
        $this->graph->setAccessToken($user->outlook_token);

        $event = [
            'subject' => $meeting->title,
            'body' => [
                'contentType' => 'HTML',
                'content' => $meeting->description
            ],
            'start' => [
                'dateTime' => $meeting->start_time->format('Y-m-d\TH:i:s'),
                'timeZone' => config('app.timezone')
            ],
            'end' => [
                'dateTime' => $meeting->end_time->format('Y-m-d\TH:i:s'),
                'timeZone' => config('app.timezone')
            ],
            'attendees' => $this->formatAttendees($meeting->participants)
        ];

        $this->graph->createRequest('PATCH', '/me/calendar/events/' . $eventId)
            ->attachBody($event)
            ->execute();
    }
}