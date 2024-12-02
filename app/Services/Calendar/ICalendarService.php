<?php

namespace App\Services\Calendar;

use App\Models\Meeting;

class ICalendarService
{
    public function generateICalFile(Meeting $meeting)
    {
        $ical = "BEGIN:VCALENDAR\r\n";
        $ical .= "VERSION:2.0\r\n";
        $ical .= "PRODID:-//MAS ERP//Meeting Calendar//EN\r\n";
        $ical .= "METHOD:REQUEST\r\n";
        $ical .= "BEGIN:VEVENT\r\n";
        $ical .= "UID:" . $meeting->id . "@" . config('app.url') . "\r\n";
        $ical .= "DTSTAMP:" . now()->format('Ymd\THis\Z') . "\r\n";
        $ical .= "DTSTART:" . $meeting->start_time->format('Ymd\THis\Z') . "\r\n";
        $ical .= "DTEND:" . $meeting->end_time->format('Ymd\THis\Z') . "\r\n";
        $ical .= "SUMMARY:" . $meeting->title . "\r\n";
        $ical .= "DESCRIPTION:" . $this->formatDescription($meeting) . "\r\n";
        $ical .= "ORGANIZER;CN=\"" . $meeting->organizer->name . "\":mailto:" . $meeting->organizer->email . "\r\n";
        
        foreach ($meeting->participants as $participant) {
            $ical .= "ATTENDEE;CN=\"" . $participant->name . "\";ROLE=REQ-PARTICIPANT:mailto:" . $participant->email . "\r\n";
        }

        $ical .= "END:VEVENT\r\n";
        $ical .= "END:VCALENDAR";

        return $ical;
    }

    protected function formatDescription(Meeting $meeting)
    {
        $description = $meeting->description;
        $description .= "\n\nMeeting Link: " . route('meetings.join', $meeting);
        
        if ($meeting->location) {
            $description .= "\nLocation: " . $meeting->location;
        }

        // Replace any characters that might break the iCal format
        return preg_replace('/[\r\n]+/', '\n', $description);
    }
}