<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">Meetings</h2>
            <x-button @click="$dispatch('open-modal', 'create-meeting')">
                <x-icon name="plus" class="w-5 h-5 mr-2" />
                Create Meeting
            </x-button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:meeting.list />
        </div>
    </div>

    <x-modal name="create-meeting">
        <livewire:meeting.create />
    </x-modal>
</x-app-layout>