<div>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Meeting Header -->
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    {{ $meeting->title }}
                </h2>
                <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                        <x-icon name="calendar" class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400"/>
                        {{ $meeting->start_time->format('M d, Y h:i A') }}
                    </div>
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                        <x-icon name="clock" class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400"/>
                        {{ $meeting->duration }} minutes
                    </div>
                </div>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
                <x-button wire:click="joinMeeting" class="bg-blue-600 hover:bg-blue-700">
                    <x-icon name="video-camera" class="-ml-1 mr-2 h-5 w-5"/>
                    Join Meeting
                </x-button>
                @can('update', $meeting)
                    <x-button wire:click="$emit('openModal', 'meeting.edit', {{ json_encode(['meeting' => $meeting->id]) }})">
                        <x-icon name="pencil" class="-ml-1 mr-2 h-5 w-5"/>
                        Edit
                    </x-button>
                @endcan
            </div>
        </div>

        <!-- Meeting Content -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Meeting Details -->
                <x-card>
                    <div class="prose max-w-none">
                        {!! $meeting->description !!}
                    </div>
                </x-card>

                <!-- Documents -->
                <livewire:meeting.documents :meeting="$meeting"/>

                <!-- Notes -->
                <livewire:meeting.notes :meeting="$meeting"/>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Participants -->
                <livewire:meeting.participants :meeting="$meeting"/>

                <!-- Calendar Integration -->
                <livewire:meeting.calendar-sync :meeting="$meeting"/>

                <!-- Meeting Links -->
                <x-card>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Meeting Links</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="text-sm text-gray-900">Join URL</div>
                            <x-button size="sm" wire:click="copyJoinLink">
                                Copy Link
                            </x-button>
                        </div>
                        @can('manage', $meeting)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="text-sm text-gray-900">Host URL</div>
                                <x-button size="sm" wire:click="copyHostLink">
                                    Copy Link
                                </x-button>
                            </div>
                        @endcan
                    </div>
                </x-card>
            </div>
        </div>
    </div>
</div>