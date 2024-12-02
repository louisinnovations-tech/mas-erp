<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">
                {{ $meeting->title }}
            </h2>
            <div class="flex space-x-3">
                @if($meeting->start_time->isFuture())
                    <x-button-link :href="$meeting->zoom_join_url" target="_blank" variant="success">
                        <x-icon name="video" class="w-5 h-5 mr-2" />
                        Join Meeting
                    </x-button-link>
                @endif
                <x-button @click="$dispatch('open-modal', 'edit-meeting')">
                    <x-icon name="pencil" class="w-5 h-5 mr-2" />
                    Edit
                </x-button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Meeting Details -->
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <x-card>
                        <div class="space-y-4">
                            <!-- Status Badge -->
                            <div class="flex items-center">
                                @if($meeting->start_time->isFuture())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        Upcoming
                                    </span>
                                @elseif($meeting->start_time->isPast() && $meeting->end_time->isFuture())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        In Progress
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        Completed
                                    </span>
                                @endif
                            </div>

                            <!-- Time and Duration -->
                            <div class="flex items-center space-x-2">
                                <x-icon name="clock" class="w-5 h-5 text-gray-400" />
                                <div>
                                    <div class="text-sm font-medium text-gray-500">Time</div>
                                    <div class="text-gray-900">
                                        {{ $meeting->start_time->format('M d, Y h:i A') }}
                                        ({{ $meeting->duration }} minutes)
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            @if($meeting->description)
                                <div class="border-t pt-4">
                                    <h3 class="text-sm font-medium text-gray-500">Description</h3>
                                    <div class="mt-2 prose prose-sm max-w-none text-gray-900">
                                        {!! nl2br(e($meeting->description)) !!}
                                    </div>
                                </div>
                            @endif

                            <!-- Meeting Links -->
                            <div class="border-t pt-4">
                                <h3 class="text-sm font-medium text-gray-500">Meeting Links</h3>
                                <div class="mt-2 space-y-2">
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="text-sm text-gray-900">Join URL</div>
                                        <x-button size="sm" wire:click="copyJoinLink">Copy Link</x-button>
                                    </div>
                                    @can('manage', $meeting)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div class="text-sm text-gray-900">Host URL</div>
                                            <x-button size="sm" wire:click="copyHostLink">Copy Link</x-button>
                                        </div>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </x-card>

                    <!-- Documents -->
                    <livewire:meeting.documents :meeting="$meeting" />

                    <!-- Notes -->
                    <livewire:meeting.notes :meeting="$meeting" />
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Participants -->
                    <livewire:meeting.participants :meeting="$meeting" />

                    <!-- Calendar Integration -->
                    <livewire:meeting.calendar-sync :meeting="$meeting" />

                    @if($meeting->related)
                        <x-card>
                            <h3 class="text-lg font-medium text-gray-900">Related To</h3>
                            <div class="mt-4">
                                @if($meeting->related instanceof \App\Models\Case)
                                    <div class="flex items-center space-x-3">
                                        <x-icon name="briefcase" class="w-5 h-5 text-gray-400" />
                                        <div>
                                            <div class="text-sm font-medium text-gray-500">Case</div>
                                            <a href="{{ route('cases.show', $meeting->related) }}"
                                               class="text-sm text-blue-600 hover:text-blue-800">
                                                {{ $meeting->related->title }}
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </x-card>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <x-modal name="edit-meeting">
        <livewire:meeting.edit :meeting="$meeting" />
    </x-modal>
</x-app-layout>