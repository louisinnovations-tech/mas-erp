<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;


class CaseJourneyMail extends Mailable
{
    use Queueable, SerializesModels;
    public $journeys;
    public $name;
    /**
     * Create a new message instance.
     */
    public function __construct($journeys, $name)
    {
        $this->journeys = $journeys;
        $this->name = $name;
    }


    public function build()
    {
        return $this->view('email.case-journey')->subject('Case Journey Selected')->with(['journeys', $this->journeys, 'name' => $this->name]);
    }
}
