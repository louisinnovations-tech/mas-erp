{{Form::model($practiceArea,array('route' => array('practice-area.update', $practiceArea->id), 'method' => 'PUT')) }}
<div class="card-body p-0">
<div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{Form::label('name',__('Name') ,['class' => 'col-form-label'])}}
                {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Practice Area Name')))}}
                @error('name')
                <span class="invalid-name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
    </div>
</div>
<div class="modal-footer pr-0">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
    {{Form::submit(__('Update'),array('class'=>'btn  btn-primary'))}}
</div>
{{Form::close()}}
