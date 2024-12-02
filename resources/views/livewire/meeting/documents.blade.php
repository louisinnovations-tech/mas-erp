<div class="bg-white shadow rounded-lg">
    <div class="p-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">
                Documents
            </h3>
            @can('update', $meeting)
                <x-button 
                    wire:click="$emit('openModal', 'meeting.upload-document', {{ json_encode(['meeting' => $meeting->id]) }})" 
                    size="sm"
                >
                    Upload
                </x-button>
            @endcan
        </div>
    </div>
    <div class="p-4">
        <div class="space-y-4">
            @forelse($documents as $document)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <x-icon name="document" class="h-8 w-8 text-gray-400"/>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">
                                {{ $document->name }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $document->size_formatted }} • 
                                Added {{ $document->created_at->diffForHumans() }} by {{ $document->user->name }}
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <x-button size="sm" wire:click="downloadDocument({{ $document->id }})">
                            Download
                        </x-button>
                        @can('delete', $document)
                            <x-button 
                                size="sm" 
                                variant="danger" 
                                wire:click="deleteDocument({{ $document->id }})" 
                                wire:loading.attr="disabled"
                            >
                                Delete
                            </x-button>
                        @endcan
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500 py-4">
                    No documents uploaded yet
                </div>
            @endforelse
        </div>
    </div>
</div>