<?php

namespace App\Http\Livewire\Meeting;

use App\Models\Meeting;
use App\Models\Document;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;

class Documents extends Component
{
    public $meeting;
    public $documents;

    protected $listeners = ['documentUploaded' => '$refresh'];

    public function mount(Meeting $meeting)
    {
        $this->meeting = $meeting;
        $this->loadDocuments();
    }

    public function loadDocuments()
    {
        $this->documents = $this->meeting->documents()
            ->with('user')
            ->latest()
            ->get();
    }

    public function downloadDocument(Document $document)
    {
        return Storage::disk('documents')->download(
            $document->path,
            $document->original_name
        );
    }

    public function deleteDocument(Document $document)
    {
        $this->authorize('delete', $document);

        Storage::disk('documents')->delete($document->path);
        $document->delete();

        $this->loadDocuments();
        $this->emit('notify', [
            'type' => 'success',
            'message' => 'Document deleted successfully'
        ]);
    }

    public function render()
    {
        return view('livewire.meeting.documents');
    }
}