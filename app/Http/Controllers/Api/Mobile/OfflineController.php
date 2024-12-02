<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\Document;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfflineController extends Controller
{
    public function sync(Request $request)
    {
        $request->validate([
            'last_sync' => 'required|date',
            'updates' => 'array'
        ]);

        DB::beginTransaction();

        try {
            // Process local updates from mobile
            if ($request->has('updates')) {
                foreach ($request->updates as $update) {
                    $this->processLocalUpdate($update);
                }
            }

            // Get server updates
            $updates = $this->getServerUpdates($request->last_sync);

            DB::commit();

            return response()->json([
                'updates' => $updates,
                'sync_time' => now()->toIso8601String()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Sync failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    protected function processLocalUpdate($update)
    {
        switch ($update['type']) {
            case 'meeting_note':
                $meeting = Meeting::find($update['meeting_id']);
                if ($meeting) {
                    $meeting->notes()->create([
                        'content' => $update['content'],
                        'user_id' => auth()->id(),
                        'created_at' => $update['created_at']
                    ]);
                }
                break;

            case 'document_comment':
                $document = Document::find($update['document_id']);
                if ($document) {
                    $document->comments()->create([
                        'content' => $update['content'],
                        'user_id' => auth()->id(),
                        'created_at' => $update['created_at']
                    ]);
                }
                break;

            case 'meeting_feedback':
                $meeting = Meeting::find($update['meeting_id']);
                if ($meeting) {
                    $meeting->feedback()->create([
                        'rating' => $update['rating'],
                        'comment' => $update['comment'],
                        'user_id' => auth()->id(),
                        'created_at' => $update['created_at']
                    ]);
                }
                break;
        }
    }

    protected function getServerUpdates($lastSync)
    {
        $userId = auth()->id();

        return [
            'meetings' => Meeting::with(['participants'])
                ->where('updated_at', '>', $lastSync)
                ->whereHas('participants', function($query) use ($userId) {
                    $query->where('users.id', $userId);
                })
                ->get(),

            'documents' => Document::where('updated_at', '>', $lastSync)
                ->whereHas('meeting.participants', function($query) use ($userId) {
                    $query->where('users.id', $userId);
                })
                ->get(),

            'notifications' => auth()->user()
                ->notifications()
                ->where('created_at', '>', $lastSync)
                ->get()
        ];
    }
}