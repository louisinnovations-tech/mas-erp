<div>
    <div class="border-t pt-4">
        <h3 class="text-sm font-medium text-gray-500">Calendar Integration</h3>
        <div class="mt-2 space-y-3">
            <!-- Google Calendar -->
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <span class="p-2 bg-white rounded-lg">
                        <x-icon name="calendar" class="w-5 h-5 text-gray-400" />
                    </span>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Google Calendar</p>
                        <p class="text-xs text-gray-500">
                            {{ $meeting->google_event_id ? 'Synced' : 'Not synced' }}
                        </p>
                    </div>
                </div>
                <button 
                    wire:click="syncWithGoogle"
                    wire:loading.attr="disabled"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    <span wire:loading.remove wire:target="syncWithGoogle">
                        {{ $meeting->google_event_id ? 'Update' : 'Sync' }}
                    </span>
                    <span wire:loading wire:target="syncWithGoogle">Syncing...</span>
                </button>
            </div>

            <!-- Outlook Calendar -->
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <span class="p-2 bg-white rounded-lg">
                        <x-icon name="calendar" class="w-5 h-5 text-gray-400" />
                    </span>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Outlook Calendar</p>
                        <p class="text-xs text-gray-500">
                            {{ $meeting->outlook_event_id ? 'Synced' : 'Not synced' }}
                        </p>
                    </div>
                </div>
                <button 
                    wire:click="syncWithOutlook"
                    wire:loading.attr="disabled"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    <span wire:loading.remove wire:target="syncWithOutlook">
                        {{ $meeting->outlook_event_id ? 'Update' : 'Sync' }}
                    </span>
                    <span wire:loading wire:target="syncWithOutlook">Syncing...</span>
                </button>
            </div>

            <!-- Download ICS -->
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <span class="p-2 bg-white rounded-lg">
                        <x-icon name="download" class="w-5 h-5 text-gray-400" />
                    </span>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Download Calendar File</p>
                        <p class="text-xs text-gray-500">Save as .ics file</p>
                    </div>
                </div>
                <a 
                    href="{{ route('meetings.download.ics', $meeting) }}"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    Download
                </a>
            </div>
        </div>
    </div>
</div>