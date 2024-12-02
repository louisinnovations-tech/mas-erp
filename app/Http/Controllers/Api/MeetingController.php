<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\User;
use App\Http\Requests\MeetingRequest;
use App\Http\Resources\MeetingResource;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function index(Request $request)
    {
        $meetings = Meeting::with(['participants', 'documents'])
            ->when($request->start_date, function ($query) use ($request) {
                return $query->where('start_time', '>=', $request->start_date);
            })
            ->when($request->end_date, function ($query) use ($request) {
                return $query->where('end_time', '<=', $request->end_date);
            })
            ->when($request->status, function ($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->latest()
            ->paginate();

        return MeetingResource::collection($meetings);
    }

    public function store(MeetingRequest $request)
    {
        $meeting = Meeting::create($request->validated());
        $meeting->participants()->attach($request->input('participants', []));

        return new MeetingResource($meeting);
    }

    public function show(Meeting $meeting)
    {
        return new MeetingResource($meeting->load(['participants', 'documents']));
    }

    public function update(MeetingRequest $request, Meeting $meeting)
    {
        $meeting->update($request->validated());
        
        if ($request->has('participants')) {
            $meeting->participants()->sync($request->input('participants'));
        }

        return new MeetingResource($meeting->fresh(['participants', 'documents']));
    }

    public function destroy(Meeting $meeting)
    {
        $meeting->delete();
        return response()->json(['message' => 'Meeting deleted successfully']);
    }

    public function join(Meeting $meeting)
    {
        if (!$meeting->canJoin()) {
            return response()->json([
                'message' => 'Meeting is not currently active'
            ], 400);
        }

        return response()->json([
            'join_url' => $meeting->join_url
        ]);
    }

    public function uploadDocument(Request $request, Meeting $meeting)
    {
        $request->validate([
            'document' => 'required|file|max:10240',
            'name' => 'required|string|max:255'
        ]);

        $document = $meeting->documents()->create([
            'name' => $request->name,
            'path' => $request->file('document')->store('meetings/' . $meeting->id, 'documents'),
            'size' => $request->file('document')->getSize(),
            'mime_type' => $request->file('document')->getMimeType(),
            'uploaded_by' => auth()->id()
        ]);

        return response()->json($document);
    }

    public function deleteDocument(Meeting $meeting, Document $document)
    {
        $this->authorize('delete', $document);
        
        Storage::disk('documents')->delete($document->path);
        $document->delete();

        return response()->json(['message' => 'Document deleted successfully']);
    }
}