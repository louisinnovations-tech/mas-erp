<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;


class CaseUpdateMail extends Mailable
{
    use Queueable, SerializesModels;
    public $updatedCaseFields;
    public $name;
    /**
     * Create a new message instance.
     */
    public function __construct($updatedCaseFields, $name)
    {
        $this->updatedCaseFields = $updatedCaseFields;
        $this->name = $name;
    }


    public function build()
    {
        return $this->view('email.case-update')->subject('Case Update')->with(['updatedCaseFields', $this->updatedCaseFields, 'name' => $this->name]);
    }
}
