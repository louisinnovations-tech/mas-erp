<?php

namespace App\Http\Livewire\Meeting;

use App\Models\Meeting;
use App\Models\User;
use Livewire\Component;

class Participants extends Component
{
    public $meeting;
    public $participants;

    protected $listeners = [
        'participantAdded' => '$refresh',
        'participantRemoved' => '$refresh'
    ];

    public function mount(Meeting $meeting)
    {
        $this->meeting = $meeting;
        $this->loadParticipants();
    }

    public function loadParticipants()
    {
        $this->participants = $this->meeting->participants;
    }

    public function removeParticipant(User $user)
    {
        $this->authorize('update', $this->meeting);

        if ($user->id === $this->meeting->organizer_id) {
            $this->emit('notify', [
                'type' => 'error',
                'message' => 'Cannot remove meeting organizer'
            ]);
            return;
        }

        $this->meeting->participants()->detach($user->id);
        $this->loadParticipants();

        $this->emit('notify', [
            'type' => 'success',
            'message' => 'Participant removed successfully'
        ]);
    }

    public function render()
    {
        return view('livewire.meeting.participants');
    }
}