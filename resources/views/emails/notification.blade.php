@component('mail::message')
# {{ $notification->title }}

{{ $notification->message }}

@if(isset($notification->data['action_url']))
@component('mail::button', ['url' => $notification->data['action_url']])
{{ $notification->data['action_text'] ?? 'View Details' }}
@endcomponent
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent
