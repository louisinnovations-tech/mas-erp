{{Form::open(array('url'=>'roles','method'=>'post'))}}
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {{Form::label('name',__('Name'),['class'=>'col-form-label'])}}
                    {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Role Name')))}}
                    @error('name')
                    <span class="invalid-name text-danger text-xs" role="alert">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="">

                    @if(!empty($permissions))
                        <div class="table-border-style">
                            <label for="permissions" class="col-form-label">{{__('Assign Permission to Roles')}}</label>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input align-middle" name="checkall"  id="checkall" >
                                                </div>
                                            </th>
                                            <th class="text-dark">{{__('Module')}}</th>
                                            <th colspan="10" class="text-dark ps-0">{{__('Permissions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($permissions as $module => $permissionItems)
                                            @if(!in_array($module,['role' , 'permission']))
                                                <tr>
                                                    <td>
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input ischeck" name="checkall" data-id="{{str_replace(' ', '', $module)}}">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <label class="ischeck" data-id="{{str_replace(' ', '', $module)}}">{{ $module }}</label>
                                                    </td>
                                                    @foreach ($permissionItems as $permission)
                                                    <td>
                                                        <div class="row">
                                                            <div class="col-md-3 form-check">
                                                                {{Form::checkbox('permissions[]',$permission->id,false, ['class'=>'form-check-input isscheck isscheck_'.str_replace(' ', '', $module),'id' =>'permission'.$permission->id])}}
                                                                {{Form::label('permission'.$permission->id,$permission->name,['class'=>'form-check-label'])}}<br>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    @endforeach
                                                    <td colspan="10"></td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Create')}}" class="btn btn-primary ms-2">
    </div>
{{Form::close()}}

<script>
    $(document).ready(function () {
           $("#checkall").on('click',function(){
                $('input:checkbox').not(this).prop('checked', this.checked);
            });
           $(".ischeck").on('click',function(){
                var ischeck = $(this).data('id');
                $('.isscheck_'+ ischeck).prop('checked', this.checked);
            });
        });
</script>
