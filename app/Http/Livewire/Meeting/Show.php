<?php

namespace App\Http\Livewire\Meeting;

use App\Models\Meeting;
use Livewire\Component;

class Show extends Component
{
    public $meeting;
    
    protected $listeners = [
        'meetingUpdated' => '$refresh',
        'documentUploaded' => '$refresh',
        'participantAdded' => '$refresh',
        'participantRemoved' => '$refresh'
    ];

    public function mount(Meeting $meeting)
    {
        $this->meeting = $meeting;
    }

    public function joinMeeting()
    {
        if (!$this->meeting->canJoin()) {
            $this->emit('notify', [
                'type' => 'error',
                'message' => 'Meeting is not currently active'
            ]);
            return;
        }

        return redirect()->to($this->meeting->join_url);
    }

    public function copyJoinLink()
    {
        $this->emit('notify', [
            'type' => 'success',
            'message' => 'Meeting join link copied to clipboard'
        ]);
    }

    public function copyHostLink()
    {
        $this->emit('notify', [
            'type' => 'success',
            'message' => 'Meeting host link copied to clipboard'
        ]);
    }

    public function render()
    {
        return view('livewire.meeting.show');
    }
}