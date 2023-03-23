@extends('layouts.member-app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 bg-title-left">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12 bg-title-right">
            <ol class="breadcrumb">
                <li><a href="{{ route('member.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('member.all-tasks.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.edit')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.css') }}">
<style>
    .panel-black .panel-heading a, .panel-inverse .panel-heading a {
     color: #171717 !important;
 }
     </style>
@endpush

@section('content')

    <div class="row">
        <div class="col-xs-12">

            <div class="panel panel-inverse">
                <div class="panel-heading"> @lang('modules.tasks.updateTask')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        {!! Form::open(['id'=>'updateTask','class'=>'ajax-form','method'=>'PUT']) !!}

                        @if(!$user->cans('add_tasks'))
                            <input type="hidden" name="user_id" value="{{ $user->id }}" />
                        @endif

                        <div class="form-body">
                            <div class="row">
                                @if(in_array('projects', $modules))
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">@lang('app.project')</label>
                                            <select class="select2 form-control" data-placeholder="@lang("app.selectProject")" id="project_id" name="project_id">
                                                <option value="">--</option>
                                                @foreach($projects as $project)
                                                    <option
                                                            @if($project->id == $task->project_id) selected @endif
                                                            value="{{ $project->id }}">{{ ucwords($project->project_name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.tasks.taskCategory')
                                        </label>
                                        <select class="selectpicker form-control" name="category_id" id="category_id"
                                                data-style="form-control">
                                            @forelse($categories as $category)
                                                <option value="{{ $category->id }}"
                                                        @if($task->task_category_id == $category->id)
                                                        selected
                                                        @endif
                                                >{{ ucwords($category->category_name) }}</option>
                                            @empty
                                                <option value="">@lang('messages.noTaskCategoryAdded')</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label required">@lang('app.title')</label>
                                        <input type="text" id="heading" name="title" class="form-control" value="{{ $task->heading }}">
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.description')</label>
                                        <textarea id="description" name="description" class="form-control summernote">{{ $task->description }}</textarea>
                                    </div>
                                </div>



                                <div class="col-md-3">
                                    <div class="form-group">

                                        <div class="checkbox checkbox-info">
                                            <input id="private-task" name="is_private" value="true"
                                            @if ($task->is_private)
                                                checked
                                            @endif
                                                   type="checkbox">
                                            <label for="private-task">@lang('modules.tasks.makePrivate') <a class="mytooltip font-12" href="javascript:void(0)"> <i class="fa fa-info-circle"></i><span class="tooltip-content5"><span class="tooltip-text3"><span class="tooltip-inner2">@lang('modules.tasks.privateInfo')</span></span></span></a></label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">

                                        <div class="checkbox checkbox-info">
                                            <input id="billable-task" name="billable" value="true"
                                            @if ($task->billable)
                                                checked
                                            @endif
                                                   type="checkbox">
                                            <label for="billable-task">@lang('modules.tasks.billable') <a class="mytooltip font-12" href="javascript:void(0)"> <i class="fa fa-info-circle"></i><span class="tooltip-content5"><span class="tooltip-text3"><span class="tooltip-inner2">@lang('modules.tasks.billableInfo')</span></span></span></a></label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <div class="checkbox checkbox-info">
                                            <input id="set-time-estimate"
                                            @if ($task->estimate_hours > 0 || $task->estimate_minutes > 0)
                                                checked
                                            @endif
                                            name="set_time_estimate" value="true" type="checkbox">
                                            <label for="set-time-estimate">@lang('modules.tasks.setTimeEstimate')</label>
                                        </div>
                                    </div>
                                </div>

                                <div id="set-time-estimate-fields" @if ($task->estimate_hours == 0 && $task->estimate_minutes == 0) style="display: none" @endif>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            
                                            <input type="number" min="0" value="{{ $task->estimate_hours }}" class="w-50 p-5 p-10" name="estimate_hours" > @lang('app.hrs')
                                            &nbsp;&nbsp;
                                            <input type="number" min="0" value="{{ $task->estimate_minutes }}" name="estimate_minutes" class="w-50 p-5 p-10"> @lang('app.mins')
                                        </div>
                                    </div>
                                   
                                </div>

                                @if($user->cans('view_tasks'))
                                    <div class="col-xs-12">
                                        <div class="form-group">

                                            <div class="checkbox checkbox-info">
                                                <input id="dependent-task" name="dependent" value="yes"
                                                       type="checkbox" @if($task->dependent_task_id != '') checked @endif>
                                                <label for="dependent-task">@lang('modules.tasks.dependent')</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" id="dependent-fields" @if($task->dependent_task_id == null) style="display: none" @endif>
                                        <div class="col-xs-12">
                                            <div class="form-group">
                                                <label class="control-label">@lang('modules.tasks.dependentTask')</label>
                                                <select class="select2 form-control" data-placeholder="@lang('modules.tasks.chooseTask')" name="dependent_task_id" id="dependent_task_id" >
                                                    <option value="">--</option>
                                                    @foreach($allTasks as $allTask)
                                                        <option value="{{ $allTask->id }}" @if($allTask->id == $task->dependent_task_id) selected @endif>{{ $allTask->heading }} (@lang('app.dueDate'): {{ (!is_null($allTask->due_date)) ?$allTask->due_date->format($global->date_format) :'' }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label class="control-label">@lang('app.label')</label>
                                            <select id="multiselect" name="task_labels[]"  multiple="multiple" class="selectpicker form-control">
                                                @foreach($taskLabels as $label)
                                                    <option data-content="<label class='badge' style='background:{{ $label->label_color }};'>{{ $label->label_name }}</label> " value="{{ $label->id }}">{{ $label->label_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class ="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label required">@lang('app.startDate')</label>
                                        <input type="text" name="start_date" autocomplete="off" id="start_date2" class="form-control" autocomplete="off" value="@if($task->start_date != '-0001-11-30 00:00:00' && $task->start_date != null) {{ $task->start_date->format($global->date_format) }} @endif">
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-4"  id="duedateBox">
                                    <div class="form-group">
                                        <label class="control-label required">@lang('app.dueDate')</label>
                                        <input type="text" name="due_date" id="due_date2" autocomplete="off" class="form-control" value="{{ $task->due_date!=''?  $task->due_date->format($global->date_format):'' }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group" style="padding-top: 25px;">
                                        <div class="checkbox checkbox-info">
                                                <input id="without_duedate" @if($task->due_date == null) checked @endif  name="without_duedate" value="true"
                                                   type="checkbox">
                                            <label for="without_deadline">@lang('modules.tasks.withoutDuedate')</label>
                                        </div>
                                    </div>
                                </div>
                                </div>
                                @if($user->cans('add_tasks'))
                                    <!--/span-->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label required">@lang('modules.tasks.assignTo')</label>
                                            <select class="select2 select2-multiple" multiple="multiple"  data-placeholder="@lang('modules.tasks.chooseAssignee')" name="user_id[]" id="user_id" >
                                                @if(is_null($task->project_id))
                                                    @foreach($employees as $employee)

                                                        @php
                                                            $selected = '';
                                                        @endphp

                                                        @foreach ($task->users as $item)
                                                            @if($item->id == $employee->id)
                                                                @php
                                                                    $selected = 'selected';
                                                                @endphp
                                                            @endif

                                                        @endforeach

                                                        <option {{ $selected }}
                                                                value="{{ $employee->id }}">{{ ucwords($employee->name) }}
                                                        </option>

                                                    @endforeach
                                                @else
                                                    @foreach($task->project->members as $member)
                                                        @php
                                                            $selected = '';
                                                        @endphp

                                                        @foreach ($task->users as $item)
                                                            @if($item->id == $member->user->id)
                                                                @php
                                                                    $selected = 'selected';
                                                                @endphp
                                                            @endif

                                                        @endforeach

                                                        <option {{ $selected }}
                                                            value="{{ $member->user->id }}">{{ $member->user->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <!--/span-->
                                @endif
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('app.status')</label>
                                        <select name="status" id="status" class="form-control">
                                            @foreach($taskBoardColumns as $taskBoardColumn)
                                                <option @if($task->board_column_id == $taskBoardColumn->id) selected @endif value="{{$taskBoardColumn->id}}">{{ $taskBoardColumn->column_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                @if(count($fields) > 0)
                                    <h3 class="box-title">@lang('modules.projects.otherInfo')</h3>
                                        @foreach($fields as $field)
                                            <div class="col-md-3">
                                                <label>{{ ucfirst($field->label) }}</label>
                                                <div class="form-group">
                                                    @if( $field->type == 'text')
                                                        <input type="text" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" placeholder="{{$field->label}}"
                                                               value="{{$task->custom_fields_data['field_'.$field->id] ?? ''}}">                                    @elseif($field->type == 'password')
                                                        <input type="password" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" placeholder="{{$field->label}}"
                                                               value="{{$task->custom_fields_data['field_'.$field->id] ?? ''}}">                                    @elseif($field->type == 'number')
                                                        <input type="number" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" placeholder="{{$field->label}}"
                                                               value="{{$task->custom_fields_data['field_'.$field->id] ?? ''}}">                                    @elseif($field->type == 'textarea')
                                                        <textarea name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" id="{{$field->name}}" cols="3">{{$task->custom_fields_data['field_'.$field->id] ?? ''}}</textarea>                                    @elseif($field->type == 'radio')
                                                        <div class="radio-list">
                                                            @foreach($field->values as $key=>$value)
                                                                <label class="radio-inline @if($key == 0) p-0 @endif">
                                                                    <div class="radio radio-info">
                                                                        <input type="radio" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" id="optionsRadios{{$key.$field->id}}" value="{{$value}}" @if(isset($task) && $task->custom_fields_data['field_'.$field->id] == $value) checked @elseif($key==0) checked @endif>>
                                                                        <label for="optionsRadios{{$key.$field->id}}">{{$value}}</label>
                                                                    </div>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    @elseif($field->type == 'select') {!! Form::select('custom_fields_data['.$field->name.'_'.$field->id.']', $field->values,
                                        isset($task)?$task->custom_fields_data['field_'.$field->id]:'',['class' => 'form-control
                                        gender']) !!}

                                                    @elseif($field->type == 'checkbox')
                                                        <div class="mt-checkbox-inline custom-checkbox checkbox-{{$field->id}}">
                                                            <input type="hidden" name="custom_fields_data[{{$field->name.'_'.$field->id}}]"
                                                                   id="{{$field->name.'_'.$field->id}}" value="{{$task->custom_fields_data['field_'.$field->id]}}">
                                                            @foreach($field->values as $key => $value)
                                                                <label class="mt-checkbox mt-checkbox-outline">
                                                                    <input name="{{$field->name.'_'.$field->id}}[]" class="custom_fields_data[{{$field->name.'_'.$field->id}}]"
                                                                           type="checkbox" value="{{$value}}" onchange="checkboxChange('checkbox-{{$field->id}}', '{{$field->name.'_'.$field->id}}')"
                                                                           @if($task->custom_fields_data['field_'.$field->id] != '' && in_array($value ,explode(', ', $task->custom_fields_data['field_'.$field->id]))) checked @endif > {{$value}}
                                                                    <span></span>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    @elseif($field->type == 'date')
                                                        <input type="text" class="form-control date-picker" size="16" name="custom_fields_data[{{$field->name.'_'.$field->id}}]"
                                                               value="{{ ($task->custom_fields_data['field_'.$field->id] != '') ? \Carbon\Carbon::parse($task->custom_fields_data['field_'.$field->id])->format($global->date_format) : \Carbon\Carbon::now()->format($global->date_format)}}">
                                                    @endif
                                                    <div class="form-control-focus"> </div>
                                                    <span class="help-block"></span>

                                                </div>
                                            </div>
                                        @endforeach
                                @endif
                                <!--/span-->
                                <!--/span-->
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.tasks.priority')</label>

                                        <div class="radio radio-danger">
                                            <input type="radio" name="priority" id="radio13"
                                                   @if($task->priority == 'high') checked @endif
                                                   value="high">
                                            <label for="radio13" class="text-danger">
                                                @lang('modules.tasks.high') </label>
                                        </div>
                                        <div class="radio radio-warning">
                                            <input type="radio" name="priority"
                                                   @if($task->priority == 'medium') checked @endif
                                                   id="radio14" value="medium">
                                            <label for="radio14" class="text-warning">
                                                @lang('modules.tasks.medium') </label>
                                        </div>
                                        <div class="radio radio-success">
                                            <input type="radio" name="priority" id="radio15"
                                                   @if($task->priority == 'low') checked @endif
                                                   value="low">
                                            <label for="radio15" class="text-success">
                                                @lang('modules.tasks.low') </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--/row-->
                            <div class="row m-b-20">
                                <div class="col-xs-12">
                                    @if($upload)
                                        <button type="button" class="btn btn-block btn-outline-info btn-sm col-md-2 select-image-button" style="margin-bottom: 10px;display: none "><i class="fa fa-upload"></i> File Select Or Upload</button>
                                        <div id="file-upload-box" >
                                            <div class="row" id="file-dropzone">
                                                <div class="col-xs-12">
                                                    <div class="dropzone"
                                                         id="file-upload-dropzone">
                                                        {{ csrf_field() }}
                                                        <div class="fallback">
                                                            <input name="file" type="file" multiple/>
                                                        </div>
                                                        <input name="image_url" id="image_url"type="hidden" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="taskID" id="taskID">
                                    @else
                                        <div class="alert alert-danger">@lang('messages.storageLimitExceedContactAdmin')</div>
                                    @endif
                                </div>
                            </div>
                            <div class="row" id="list">
                                <ul class="list-group" id="files-list">
                                    @forelse($task->files as $file)
                                        <li class="list-group-item">
                                            <div class="row">
                                                <div class="col-md-9">
                                                    {{ $file->filename }}
                                                </div>
                                                <div class="col-md-3">

                                                        <a target="_blank" href="{{ asset_url_local_s3('task-files/'.$task->id.'/'.$file->hashname) }}"
                                                           data-toggle="tooltip" data-original-title="View"
                                                           class="btn btn-info btn-circle"><i
                                                                    class="fa fa-search"></i></a>


                                                    @if(is_null($file->external_link))
                                                        &nbsp;&nbsp;
                                                        <a href="{{ route('member.task-files.download', $file->id) }}"
                                                           data-toggle="tooltip" data-original-title="Download"
                                                           class="btn btn-default btn-circle"><i
                                                                    class="fa fa-download"></i></a>
                                                    @endif
                                                    &nbsp;&nbsp;
                                                    <a href="javascript:;" data-toggle="tooltip"
                                                       data-original-title="Delete"
                                                       data-file-id="{{ $file->id }}"
                                                       class="btn btn-danger btn-circle sa-params" data-pk="list"><i
                                                                class="fa fa-times"></i></a>

                                                    <span class="m-l-10">{{ $file->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        </li>
                                    @empty
                                        <li class="list-group-item">
                                            <div class="row">
                                                <div class="col-md-10">
                                                    @lang('messages.noFileUploaded')
                                                </div>
                                            </div>
                                        </li>
                                    @endforelse

                                </ul>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="button" id="update-task" class="btn btn-success"><i class="fa fa-check"></i> @lang('app.save')</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>    <!-- .row -->

@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.js') }}"></script>

<script>
    $('#multiselect').selectpicker();
    @if($labelIds)
        var labelIds = {{ json_encode($labelIds) }};
        $('#multiselect').selectpicker('val', labelIds);
    @endif
    @if($upload)
        Dropzone.autoDiscover = false;
        //Dropzone class
        myDropzone = new Dropzone("div#file-upload-dropzone", {
            url: "{{ route('member.task-files.store') }}",
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            paramName: "file",
            maxFilesize: 10,
            maxFiles: 10,
            acceptedFiles: "image/*,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/docx,application/pdf,text/plain,application/msword,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            autoProcessQueue: false,
            uploadMultiple: true,
            addRemoveLinks:true,
            parallelUploads:10,
            dictDefaultMessage: "@lang('modules.projects.dropFile')",
            init: function () {
                myDropzone = this;
                this.on("success", function (file, response) {
                    if(response.status == 'fail') {
                        $.showToastr(response.message, 'error');
                        return;
                    }
                })
            }
        });

        myDropzone.on('sending', function(file, xhr, formData) {
            console.log(myDropzone.getAddedFiles().length,'sending');
            var ids = '{{ $task->id }}';
            formData.append('task_id', ids);
        });

        myDropzone.on('completemultiple', function () {
            var msgs = "@lang('messages.taskUpdatedSuccessfully')";
            $.showToastr(msgs, 'success');
            window.location.href = '{{ route('member.all-tasks.index') }}'

        });
    @endif
    $('.summernote').summernote({
        height: 200,                 // set editor height
        minHeight: null,             // set minimum height of editor
        maxHeight: null,             // set maximum height of editor
        focus: false,
        toolbar: [
            // [groupName, [list of button]]
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['fontsize', ['fontsize']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link']],
            ["view", ["fullscreen"]]
        ]
    });
    @if($task->due_date == null)
        $('#duedateBox').hide();
    @endif
    $('#without_duedate').click(function () {
        var check = $('#without_duedate').is(":checked") ? true : false;
        if(check == true){
            $('#duedateBox').hide();
        }
        else{
            $('#duedateBox').show();
        }
    });
    //    update task
    $('#update-task').click(function () {
        var status = '{{ $task->board_column->slug }}';
        var currentStatus =  $('#status').val();

        if(status == 'incomplete' && currentStatus == 'completed'){

            $.easyAjax({
                url: '{{route('member.tasks.checkTask', [$task->id])}}',
                type: "GET",
                data: {},
                success: function (data) {
                    console.log(data.taskCount);
                    if(data.taskCount > 0){
                        swal({
                            title: "@lang('messages.sweetAlertTitle')",
                            text: "@lang('messages.confirmation.markTaskComplete')",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "@lang('messages.completeIt')",
                            cancelButtonText: "@lang('messages.confirmNoArchive')",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        }, function (isConfirm) {
                            if (isConfirm) {
                                updateTask();
                            }
                        });
                    }
                    else{
                        updateTask();
                    }

                }
            });
        }
        else{
            updateTask();
        }
    });

    function updateTask(){
        $.easyAjax({
            url: '{{route('member.all-tasks.update', [$task->id])}}',
            container: '#updateTask',
            type: "POST",
            data: $('#updateTask').serialize(),
            success: function(response){
                var dropzone = 0;
                @if($upload)
                    dropzone = myDropzone.getQueuedFiles().length;
                @endif

                if(dropzone > 0){
                    taskID = response.taskID;
                    $('#taskID').val(response.taskID);
                    myDropzone.processQueue();
                }
                else{
                    var msgs = "@lang('messages.taskCreatedSuccessfully')";
                    $.showToastr(msgs, 'success');
                    window.location.href = '{{ route('member.all-tasks.index') }}'
                }
            }
        })
    }

    jQuery('#due_date2, #start_date2').datepicker({
        autoclose: true,
        todayHighlight: true,
        weekStart:'{{ $global->week_start }}',
        format: '{{ $global->date_picker_format }}',
    });

    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    $('body').on('click', '.sa-params', function () {
        var id = $(this).data('file-id');
        var deleteView = $(this).data('pk');
        swal({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.confirmation.deleteFile')",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "@lang('messages.deleteConfirmation')",
            cancelButtonText: "@lang('messages.confirmNoArchive')",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {

                var url = "{{ route('member.task-files.destroy',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token, '_method': 'DELETE', 'view': deleteView},
                    success: function (response) {
                        console.log(response);
                        if (response.status == "success") {
                            $.unblockUI();
                            $('#list ul.list-group').html(response.html);

                        }
                    }
                });
            }
        });
    });

    $('#dependent-task').change(function () {
        if($(this).is(':checked')){
            $('#dependent-fields').show();
        }
        else{
            $('#dependent-fields').hide();
        }
    })

    $('#project_id').change(function () {
        var id = $(this).val();

        // For getting dependent task
        var dependentTaskUrl = '{{route('member.all-tasks.dependent-tasks', [':id', ':taskId'])}}';
        dependentTaskUrl = dependentTaskUrl.replace(':id', id);
        dependentTaskUrl = dependentTaskUrl.replace(':taskId', '{{ $task->id }}');
        $.easyAjax({
            url: dependentTaskUrl,
            type: "GET",
            success: function (data) {
                $('#dependent_task_id').html(data.html);
            }
        })
    });

    $('#set-time-estimate').change(function () {
        if($(this).is(':checked')){
            $('#set-time-estimate-fields').show();
        }
        else{
            $('#set-time-estimate-fields').hide();
        }
    })
</script>
@endpush

