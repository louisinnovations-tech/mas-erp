<?php

namespace App\Services\Calendar;

use Google_Client;
use Google_Service_Calendar;

class GoogleCalendarService {
    protected $client;
    protected $service;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->addScope(Google_Service_Calendar::CALENDAR);
    }
}