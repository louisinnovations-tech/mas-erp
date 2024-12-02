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
            'attendees' => $meeting->participants->map(function($participant) {
                return [
                    'emailAddress' => [
                        'address' => $participant->email,
                        'name' => $participant->name
                    ],
                    'type' => 'required'
                ];
            })->toArray()
        ]);

        $response = $this->graph->createRequest('POST', '/me/events')
            ->attachBody($event)
            ->execute();

        return $response->getBody()['id'];
    }

    public function updateEvent(Meeting $meeting, User $user)
    {
        if (!$meeting->outlook_event_id) {
            return $this->createEvent($meeting, $user);
        }

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
            ]
        ];

        $this->graph->createRequest('PATCH', '/me/events/' . $meeting->outlook_event_id)
            ->attachBody($event)
            ->execute();

        return $meeting->outlook_event_id;
    }
}