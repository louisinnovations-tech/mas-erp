<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\Recording;
use Illuminate\Http\Request;
use App\Services\Meeting\RecordingService;

class RecordingController extends Controller
{
    protected $recordingService;

    public function __construct(RecordingService $recordingService)
    {
        $this->recordingService = $recordingService;
    }

    public function index(Meeting $meeting)
    {
        $this->authorize('viewRecordings', $meeting);

        return response()->json([
            'recordings' => $meeting->recordings()
                ->with('transcription')
                ->orderBy('created_at', 'desc')
                ->get()
        ]);
    }

    public function store(Request $request, Meeting $meeting)
    {
        $this->authorize('manageRecordings', $meeting);

        $request->validate([
            'recording' => 'required|file|max:2048000', // 2GB max
            'type' => 'required|in:video,audio',
            'duration' => 'required|integer',
            'metadata' => 'nullable|array'
        ]);

        $recording = $this->recordingService->store($meeting, [
            'path' => $request->file('recording')->store('recordings', 'private'),
            'type' => $request->type,
            'duration' => $request->duration,
            'metadata' => $request->metadata
        ]);

        // Start async processing
        ProcessRecording::dispatch($recording);

        return response()->json($recording);
    }

    public function show(Meeting $meeting, Recording $recording)
    {
        $this->authorize('viewRecordings', $meeting);

        return response()->json($recording->load('transcription'));
    }

    public function download(Meeting $meeting, Recording $recording)
    {
        $this->authorize('viewRecordings', $meeting);

        return Storage::disk('private')
            ->download(
                $recording->processed_path ?? $recording->path,
                'recording.' . $recording->extension
            );
    }

    public function stream(Meeting $meeting, Recording $recording)
    {
        $this->authorize('viewRecordings', $meeting);
        
        $path = Storage::disk('private')
            ->path($recording->processed_path ?? $recording->path);

        return response()->stream(
            function() use ($path) {
                $stream = fopen($path, 'rb');
                while (!feof($stream)) {
                    echo fread($stream, 8192);
                    flush();
                }
                fclose($stream);
            },
            200,
            [
                'Content-Type' => $recording->mime_type,
                'Content-Length' => Storage::disk('private')->size($recording->processed_path ?? $recording->path),
                'Accept-Ranges' => 'bytes'
            ]
        );
    }

    public function destroy(Meeting $meeting, Recording $recording)
    {
        $this->authorize('manageRecordings', $meeting);

        Storage::disk('private')->delete([
            $recording->path,
            $recording->processed_path
        ]);

        $recording->delete();

        return response()->json([
            'message' => 'Recording deleted successfully'
        ]);
    }
}