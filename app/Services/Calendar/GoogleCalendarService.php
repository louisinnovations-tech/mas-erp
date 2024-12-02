<?php

namespace App\Services\Calendar;

use App\Models\Meeting;
use App\Models\User;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;

class GoogleCalendarService implements CalendarServiceInterface
{
    protected $client;
    protected $service;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect_uri'));
        $this->client->addScope(Google_Service_Calendar::CALENDAR);
    }

    public function createEvent(Meeting $meeting, User $user)
    {
        if (!$user->google_token) {
            throw new \Exception('User not connected to Google Calendar');
        }

        $this->client->setAccessToken($user->google_token);

        if ($this->client->isAccessTokenExpired()) {
            $this->refreshToken($user);
        }

        $this->service = new Google_Service_Calendar($this->client);

        $event = new Google_Service_Calendar_Event([
            'summary' => $meeting->title,
            'description' => $meeting->description,
            'start' => [
                'dateTime' => $meeting->start_time->toRfc3339String(),
                'timeZone' => config('app.timezone'),
            ],
            'end' => [
                'dateTime' => $meeting->end_time->toRfc3339String(),
                'timeZone' => config('app.timezone'),
            ],
            'attendees' => $meeting->participants->map(function($participant) {
                return ['email' => $participant->email];
            })->toArray(),
            'reminders' => [
                'useDefault' => false,
                'overrides' => [
                    ['method' => 'email', 'minutes' => 24 * 60],
                    ['method' => 'popup', 'minutes' => 30],
                ],
            ],
        ]);

        $createdEvent = $this->service->events->insert('primary', $event);

        return $createdEvent->id;
    }

    public function updateEvent(Meeting $meeting, User $user)
    {
        if (!$meeting->google_event_id) {
            return $this->createEvent($meeting, $user);
        }

        $this->client->setAccessToken($user->google_token);
        $this->service = new Google_Service_Calendar($this->client);

        $event = $this->service->events->get('primary', $meeting->google_event_id);
        
        $event->setSummary($meeting->title);
        $event->setDescription($meeting->description);
        $event->setStart([
            'dateTime' => $meeting->start_time->toRfc3339String(),
            'timeZone' => config('app.timezone'),
        ]);
        $event->setEnd([
            'dateTime' => $meeting->end_time->toRfc3339String(),
            'timeZone' => config('app.timezone'),
        ]);

        $this->service->events->update('primary', $meeting->google_event_id, $event);

        return $meeting->google_event_id;
    }

    protected function refreshToken(User $user)
    {
        $this->client->refreshToken($user->google_refresh_token);
        $token = $this->client->getAccessToken();
        
        $user->update([
            'google_token' => $token['access_token'],
            'google_token_expires' => now()->addSeconds($token['expires_in'])
        ]);
    }
}