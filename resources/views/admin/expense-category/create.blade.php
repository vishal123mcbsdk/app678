<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">@lang('modules.projects.projectCategory')</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>@lang('modules.projectCategory.categoryName')</th>
                    <th>@lang('app.action')</th>
                </tr>
                </thead>
                <tbody>
                @forelse($categories as $key=>$category)
                    <tr id="cat-{{ $category->id }}">
                        <td>{{ $key+1 }}</td>
                        <td>{{ ucwords($category->category_name) }}</td>
                        <td><a href="javascript:;" data-cat-id="{{ $category->id }}" class="btn btn-sm btn-danger btn-rounded delete-category">@lang("app.remove")</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">@lang('messages.noProjectCategory')</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <hr>
        {!! Form::open(['id'=>'createProjectCategory','class'=>'ajax-form','method'=>'POST']) !!}
        <div class="form-body">
            <div class="row">
                <div class="col-xs-12 ">
                    <div class="form-group">
                        <label>@lang('app.add') @lang('modules.projectCategory.categoryName')</label>
                        <input type="text" name="category_name" id="category_name" class="form-control">
                    </div>
                </div>
                <div class="col-xs-12 ">
                    <div class="form-group">
                        <label class="control-label required">@lang('modules.tasks.assignTo')</label>
                        <a href="javascript:;" id="add-employee" class="btn btn-xs btn-success btn-outline"><i class="fa fa-plus"></i></a>
                        <select class="select2 select2-multiple " multiple="multiple"
                                data-placeholder="@lang('modules.tasks.chooseAssignee')"
                                name="role_id[]" id="user_id">
                            <option value="">--</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ ucwords($role->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" id="save-category" class="btn btn-success"> <i class="fa fa-check"></i> @lang('app.save')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>

<script>
    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    $('#createProjectCategory').submit(function () {
        $.easyAjax({
            url: '{{route('admin.projectCategory.store')}}',
            container: '#createProjectCategory',
            type: "POST",
            data: $('#createProjectCategory').serialize(),
            success: function (response) {
                // if(response.status == 'success'){
                //     window.location.reload();
                // }
                if(response.status == 'success'){
                    var options = [];
                    var rData = [];
                    let listData = "";
                    rData = response.data;
                    $.each(rData, function (index, value) {
                        var selectData = '';
                        selectData = '<option value="' + value.id + '">' + value.category_name + '</option>';
                        options.push(selectData);
                        listData += '<tr id="cat-' + value.id + '">'+
                            '<td>'+(index+1)+'</td>'+
                            '<td>' + value.category_name + '</td>'+
                            '<td><a href="javascript:;" data-cat-id="' + value.id + '" class="btn btn-sm btn-danger btn-rounded delete-category">@lang("app.remove")</a></td>'+
                            '</tr>';
                    });

                    $('.category-table tbody' ).html(listData);

                    $('#category_add').html(options);
                    $('#category_name').val(' ');
                    $('#category_id').html(options);
                }
            }
        })
        return false;
    })

    $('body').on('click', '.delete-category', function() {
        var id = $(this).data('cat-id');
        var url = "{{ route('admin.projectCategory.destroy',':id') }}";
        url = url.replace(':id', id);

        var token = "{{ csrf_token() }}";

        $.easyAjax({
            type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
            success: function (response) {
                if (response.status == "success") {
                    $.unblockUI();
//                                    swal("Deleted!", response.message, "success");
                    $('#cat-'+id).fadeOut();
                }
            }
        });
    });

    $('#createProjectCategory').on('submit', (e) => {
        e.preventDefault();
        $.easyAjax({
            url: '{{route('admin.projectCategory.store')}}',
            container: '#createProjectCategory',
            type: "POST",
            data: $('#createProjectCategory').serialize(),
            success: function (response) {
                if(response.status == 'success'){
                    window.location.reload();
                }
            }
        })
    });
</script>