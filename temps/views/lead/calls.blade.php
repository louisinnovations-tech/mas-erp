@if (isset($call))
    {{ Form::model($call, ['route' => ['lead.call.update', $lead->id, $call->id], 'method' => 'post']) }}
@else
    {{ Form::open(['route' => ['lead.call.store', $lead->id]]) }}
@endif
<div class="modal-body">
    <div class="row">
        <div>
            <a href="#" data-size="md" data-ajax-popup-over="true" data-url="{{ route('generate', ['lead']) }}"
                data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Generate') }}"
                data-title="{{ __('Generate content with AI') }}" class="btn btn-primary btn-sm float-end">
                <i class="fas fa-robot"></i>
                {{__('Generate with AI')}}
            </a>
        </div>
        <div class="form-group">
            {{ Form::label('subject', __('Subject'), ['class' => 'col-form-label']) }}
            {{ Form::text('subject', null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="form-group">
            {{ Form::label('call_type', __('Call Type'), ['class' => 'col-form-label']) }}
            <select name="call_type" id="call_type" class="form-control multi-select" required>
                <option value="outbound" @if (isset($call->call_type) && $call->call_type == 'outbound') selected @endif>{{ __('Outbound') }}</option>
                <option value="inbound" @if (isset($call->call_type) && $call->call_type == 'inbound') selected @endif>{{ __('Inbound') }}</option>
            </select>
        </div>
        <div class="form-group">
            {{ Form::label('duration', __('Duration'), ['class' => 'col-form-label']) }} <small
                class="font-weight-bold">{{ __(' (Format h:m:s i.e 00:35:20 means 35 Minutes and 20 Sec)') }}</small>
            {{ Form::time('duration', null, ['class' => 'form-control', 'placeholder' => '00:35:20']) }}
        </div>
        <div class="form-group">
            {{ Form::label('user_id', __('Assignee'), ['class' => 'col-form-label']) }}
            <select name="user_id" id="user_id" class="form-control multi-select" required>
                @foreach ($users as $user)
                    <option value="{{ $user->getLeadUser->id }}" @if (isset($call->user_id) && $call->user_id == $user->getLeadUser->id) selected @endif>
                        {{ $user->getLeadUser->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            {{ Form::label('description', __('Description'), ['class' => 'col-form-label']) }}
            {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => '2']) }}
        </div>
        <div class="form-group">
            {{ Form::label('call_result', __('Call Result'), ['class' => 'col-form-label']) }}
            {{ Form::textarea('call_result', null, ['class' => 'form-control', 'rows' => '2']) }}
        </div>
    </div>
    @if (isset($call))
        <div class="modal-footer pr-0">
            <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
            {{ Form::submit(__('Update'), ['class' => 'btn  btn-primary']) }}
        </div>
    @else
        <div class="modal-footer pr-0">
            <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
            {{ Form::submit(__('Create'), ['class' => 'btn  btn-primary']) }}
        </div>
    @endif
</div>

{{ Form::close() }}


<script src="{{ asset('assets/js/plugins/choices.min.js') }}"></script>

<script>
    if ($(".multi-select").length > 0) {
        $($(".multi-select")).each(function(index, element) {
            var id = $(element).attr('id');
            var multipleCancelButton = new Choices(
                '#' + id, {
                    removeItemButton: true,
                }
            );
        });
    }
</script>