<div class="relative">
    <!-- Notification Bell -->
    <button 
        @click="$wire.showDropdown = !$wire.showDropdown"
        class="relative p-1 text-gray-600 hover:text-gray-800 focus:outline-none"
    >
        <x-icon name="bell" class="w-6 h-6" />
        @if($unreadCount > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Notification Panel -->
    @if($showDropdown)
        <div 
            class="absolute right-0 w-80 mt-2 bg-white rounded-md shadow-lg overflow-hidden z-50"
            @click.away="$wire.showDropdown = false"
        >
            <div class="py-2">
                <div class="px-4 py-2 border-b flex justify-between items-center">
                    <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                    @if($unreadCount > 0)
                        <button 
                            wire:click="markAllAsRead"
                            class="text-sm text-blue-600 hover:text-blue-800"
                        >
                            Mark all as read
                        </button>
                    @endif
                </div>

                <div class="max-h-96 overflow-y-auto">
                    @forelse($notifications as $notification)
                        <div 
                            wire:key="notification-{{ $notification->id }}"
                            class="px-4 py-3 hover:bg-gray-50 {{ $notification->read_at ? 'bg-white' : 'bg-blue-50' }}"
                        >
                            <div class="flex items-start">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $notification->title }}
                                    </p>
                                    <p class="text-sm text-gray-500 truncate">
                                        {{ $notification->message }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-400">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                @unless($notification->read_at)
                                    <button 
                                        wire:click="markAsRead('{{ $notification->id }}')"
                                        class="ml-3 text-xs text-blue-600 hover:text-blue-800"
                                    >
                                        Mark as read
                                    </button>
                                @endunless
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-6 text-center text-gray-500">
                            No notifications
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    <!-- Toast Notification -->
    <div
        x-data="{ show: false, title: '', message: '' }"
        @new-notification.window="
            show = true;
            title = $event.detail.title;
            message = $event.detail.message;
            setTimeout(() => { show = false }, 5000);
        "
        x-show="show"
        x-transition:enter="transform ease-out duration-300 transition"
        x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 flex items-end justify-center px-4 py-6 pointer-events-none sm:p-6 sm:items-start sm:justify-end z-50"
    >
        <div class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <x-icon name="bell" class="h-6 w-6 text-blue-400" />
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-medium text-gray-900" x-text="title"></p>
                        <p class="mt-1 text-sm text-gray-500" x-text="message"></p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button 
                            @click="show = false"
                            class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            <span class="sr-only">Close</span>
                            <x-icon name="x" class="h-5 w-5" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>