<div class="bg-white shadow rounded-lg">
    <div class="p-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">
                Participants ({{ $participants->count() }})
            </h3>
            @can('update', $meeting)
                <x-button 
                    wire:click="$emit('openModal', 'meeting.add-participants', {{ json_encode(['meeting' => $meeting->id]) }})" 
                    size="sm"
                >
                    Add
                </x-button>
            @endcan
        </div>
    </div>
    <div class="p-4">
        <div class="space-y-4">
            @forelse($participants as $participant)
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <img 
                            src="{{ $participant->profile_photo_url }}" 
                            alt="{{ $participant->name }}" 
                            class="h-8 w-8 rounded-full"
                        >
                        <div>
                            <div class="text-sm font-medium text-gray-900">
                                {{ $participant->name }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $participant->email }}
                            </div>
                        </div>
                    </div>
                    @if(auth()->id() !== $participant->id && auth()->user()->can('update', $meeting))
                        <x-button 
                            size="sm" 
                            variant="danger" 
                            wire:click="removeParticipant({{ $participant->id }})" 
                            wire:loading.attr="disabled"
                        >
                            Remove
                        </x-button>
                    @endif
                </div>
            @empty
                <div class="text-center text-gray-500 py-4">
                    No participants added yet
                </div>
            @endforelse
        </div>
    </div>
</div>