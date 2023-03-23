@extends('layouts.app')

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
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('admin.all-tasks.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.addNew')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
    <link rel="stylesheet"
          href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">

    <link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.css') }}">

    <style>
        .panel-black .panel-heading a, .panel-inverse .panel-heading a {
            color: unset !important;
        }
    </style>
@endpush

@section('content')

    <div class="row">
        <div class="col-xs-12">

            <div class="panel panel-inverse">
                <div class="panel-heading"> @lang('modules.tasks.newTask')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        {!! Form::open(['id'=>'storeTask','class'=>'ajax-form','method'=>'POST']) !!}

                        <div class="form-body">
                            <div class="row">
                                <div class="col-xs-12">
                                    <input type ="hidden" name ="task_request_id" id="task_request_id" value= {{ $taskRequests->id ?? ''}}>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label required">@lang('app.title')</label>
                                            <input type="text" id="heading" value="{{ $taskRequests->heading ?? '' }}" name="title" class="form-control">
                                        </div>
                                    </div>
                                    @if(in_array('projects', $modules))
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">@lang('app.project')</label>
                                                <select class="select2 form-control"
                                                        data-placeholder="@lang("app.selectProject")" id="project_id"
                                                        name="project_id">
                                                    <option value="">--</option>
                                                    @foreach($projects as $project)
                                                    @if(isset($taskRequests))
                                                        <option @if($project->id == $taskRequests->project_id) selected @endif
                                                                value="{{ $project->id }}">{{ ucwords($project->project_name) }}</option>
                                                                @else
                                                            <option
                                                                    value="{{ $project->id }}">{{ ucwords($project->project_name) }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">@lang('modules.tasks.taskCategory') <a
                                                        href="javascript:;"
                                                        id="createTaskCategory"
                                                        class="btn btn-xs btn-outline btn-success"><i
                                                            class="fa fa-plus"></i> @lang('modules.taskCategory.addTaskCategory')
                                                </a>
                                            </label>
                                            <select class="select2 form-control" name="category_id"
                                                    id="category_id"
                                                    data-style="form-control">
                                                    @foreach($categories as $category)
                                                        @if(isset($taskRequests))
                                                            <option value="{{ $category->id }}" @if($taskRequests->task_category_id == $category->id) selected @endif
                                                                >{{ ucwords($category->category_name) }}</option>
                                                            @else
                                                            <option value="{{ $category->id }}">{{ ucwords($category->category_name) }}</option>
                                                         @endif
                                                  @endforeach
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <!--/span-->
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label class="control-label">@lang('app.description')</label>
                                            <textarea id="description" name="description" {{$taskRequests->description ?? ''}}
                                                      class="form-control summernote"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="form-group">

                                            <div class="checkbox checkbox-info">
                                                <input id="dependent-task" name="dependent" value="yes"
                                                       type="checkbox" @if(isset($taskRequests)) 
                                                       @if($taskRequests->dependent_task_id != '') checked @endif
                                                        @endif>
                                                <label for="dependent-task">@lang('modules.tasks.dependent')</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" id="dependent-fields" @if(isset($taskRequests)) @if($taskRequests->dependent_task_id == null)style="display: none"  @endif @else style="display: none"  @endif>
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label class="control-label">@lang('modules.tasks.dependentTask')</label>
                                            <select class="select2 form-control"
                                                    data-placeholder="@lang('modules.tasks.chooseTask')"
                                                    name="dependent_task_id" id="dependent_task_id">
                                                <option value="">--</option>
                                                @foreach($allTasks as $allTask)
                                                    <option value="{{ $allTask->id }}" @if(isset($taskRequests))  @if($allTask->id == $taskRequests->dependent_task_id) selected @endif @endif>{{ $allTask->heading }}
                                                        (@lang('app.dueDate'): {{ ($allTask->due_date) ?$allTask->due_date->format($global->date_format):'' }}
                                                        )
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label class="control-label">@lang('app.label')
                                                <a href="javascript:;"
                                                   id="createTaskLabel"
                                                   class="btn btn-xs btn-outline btn-success">
                                                    <i class="fa fa-plus"></i> @lang('app.add') @lang('app.menu.taskLabel')
                                                </a>
                                            </label>
                                            <select id="multiselect" name="task_labels[]"  multiple="multiple" class="selectpicker form-control newclass">
                                                @foreach($taskLabels as $label)
                                                    <option id data-content="<label class='badge b-all' style='background:{{ $label->label_color }};'>{{ $label->label_name }}</label> " value="{{ $label->id }}">{{ $label->label_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <!--/span-->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label required">@lang('app.startDate')</label>
                                            <input type="text" name="start_date" id="start_date2" class="form-control"
                                            value="@if(isset($taskRequests)) {{ $taskRequests->start_date->format($global->date_format) }} @else {{\Carbon\Carbon::now($global->timezone)->format($global->date_format)  }} @endif"
                                                   autocomplete="off">
                                        </div>
                                    </div>
                                    <!--/span-->

                                    <!--/span-->
                                    <div class="col-md-4" id="duedateBox">
                                        <div class="form-group">
                                            <label class="control-label required">@lang('app.dueDate')</label>
                                            <input type="text" name="due_date" id="due_date2" class="form-control"
                                            value="@if(isset($taskRequests)) @if($taskRequests->due_date != ''){{$taskRequests->due_date->format($global->date_format)}} @endif @else{{ ''}} @endif"  autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group" style="padding-top: 25px;">
                                            <div class="checkbox checkbox-info">
                                                <input id="without_duedate" @if(isset($taskRequests)) @if($taskRequests->due_date == null) checked @endif @endif name="without_duedate" value="true"
                                                       type="checkbox">
                                                <label for="without_duedate" >@lang('modules.tasks.withoutDuedate')</label>
                                            </div>
                                        </div>
                                    </div>
                                    <!--/span-->
                                   
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label required">@lang('modules.tasks.assignTo')</label>
                                            <a href="javascript:;" id="add-employee" class="btn btn-xs btn-success btn-outline"><i class="fa fa-plus"></i></a>
                                            <select class="select2 select2-multiple " multiple="multiple"
                                                    data-placeholder="@lang('modules.tasks.chooseAssignee')"
                                                    name="user_id[]" id="user_id">
                                                <option value="">--</option>
                                                @foreach($employees as $employee)
                                                    <option value="{{ $employee->id }}">{{ ucwords($employee->name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">

                                            <div class="checkbox checkbox-info">
                                                <input id="private-task" name="is_private" value="true"
                                                @if(isset($taskRequests))
                                                @if ($taskRequests->is_private)
                                                  checked
                                                @endif
                                                @endif
                                                       type="checkbox">
                                                <label for="private-task">@lang('modules.tasks.makePrivate') <a
                                                            class="mytooltip font-12" href="javascript:void(0)"> <i
                                                                class="fa fa-info-circle"></i><span
                                                                class="tooltip-content5"><span
                                                                    class="tooltip-text3"><span
                                                                        class="tooltip-inner2">@lang('modules.tasks.privateInfo')</span></span></span></a></label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">

                                            <div class="checkbox checkbox-info">
                                                <input id="billable-task" checked name="billable" value="true" type="checkbox">
                                                <label for="billable-task">@lang('modules.tasks.billable')
                                                    <a class="mytooltip font-12" href="javascript:void(0)">
                                                        <i class="fa fa-info-circle"></i>
                                                        <span class="tooltip-content5">
                                                            <span class="tooltip-text3">
                                                                <span class="tooltip-inner2">@lang('modules.tasks.billableInfo')</span>
                                                            </span>
                                                        </span>
                                                    </a>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-info">
                                                <input id="set-time-estimate" name="set_time_estimate" value="true" type="checkbox">
                                                <label for="set-time-estimate">@lang('modules.tasks.setTimeEstimate')</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="set-time-estimate-fields" style="display: none">
                                        <div class="col-md-4">
                                            <div class="form-group">

                                                <input type="number" min="0" value="0" class="w-50 p-5 p-10" name="estimate_hours" > @lang('app.hrs')
                                                &nbsp;&nbsp;
                                                <input type="number" min="0" value="0" name="estimate_minutes" class="w-50 p-5 p-10"> @lang('app.mins')
                                            </div>
                                        </div>

                                    </div>

                                </div>
                                <div class="row">

                                    <div class="col-md-3">
                                        <div class="form-group">

                                            <div class="checkbox checkbox-info">
                                                <input id="repeat-task" name="repeat" value="yes"
                                                       type="checkbox">
                                                <label for="repeat-task">@lang('modules.events.repeat')</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div  id="repeat-fields" style="display: none">
                                        <div class="col-md-3 ">
                                            <div class="form-group">
                                                <label>@lang('modules.events.repeatEvery')</label>
                                                <input type="number" min="1" value="1" name="repeat_count"
                                                       class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <select name="repeat_type" id="" class="form-control">
                                                    <option value="day">@lang('app.day')</option>
                                                    <option value="week">@lang('app.week')</option>
                                                    <option value="month">@lang('app.month')</option>
                                                    <option value="year">@lang('app.year')</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>@lang('modules.events.cycles') <a class="mytooltip"
                                                                                         href="javascript:void(0)"> <i
                                                                class="fa fa-info-circle"></i><span
                                                                class="tooltip-content5"><span
                                                                    class="tooltip-text3"><span
                                                                        class="tooltip-inner2">@lang('modules.tasks.cyclesToolTip')</span></span></span></a></label>
                                                <input type="number" name="repeat_cycles" id="repeat_cycles"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                @if(sizeof($fields) > 0)
                                    <h3 class="box-title">@lang('modules.projects.otherInfo')</h3>
                                        <div class="row">
                                            @foreach($fields as $field)
                                                <div class="col-md-3">
                                                    <label>{{ ucfirst($field->label) }}</label>
                                                    <div class="form-group">
                                                        @if( $field->type == 'text')
                                                            <input type="text" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" placeholder="{{$field->label}}" value="{{$editUser->custom_fields_data['field_'.$field->id] ?? ''}}">
                                                        @elseif($field->type == 'password')
                                                            <input type="password" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" placeholder="{{$field->label}}" value="{{$editUser->custom_fields_data['field_'.$field->id] ?? ''}}">
                                                        @elseif($field->type == 'number')
                                                            <input type="number" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" placeholder="{{$field->label}}" value="{{$editUser->custom_fields_data['field_'.$field->id] ?? ''}}">

                                                        @elseif($field->type == 'textarea')
                                                            <textarea name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" id="{{$field->name}}" cols="3">{{$editUser->custom_fields_data['field_'.$field->id] ?? ''}}</textarea>

                                                        @elseif($field->type == 'radio')
                                                            <div class="radio-list">
                                                                @foreach($field->values as $key=>$value)
                                                                    <label class="radio-inline @if($key == 0) p-0 @endif">
                                                                        <div class="radio radio-info">
                                                                            <input type="radio" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" id="optionsRadios{{$key.$field->id}}" value="{{$value}}" @if(isset($editUser) && $editUser->custom_fields_data['field_'.$field->id] == $value) checked @elseif($key==0) checked @endif>>
                                                                            <label for="optionsRadios{{$key.$field->id}}">{{$value}}</label>
                                                                        </div>
                                                                    </label>
                                                                @endforeach
                                                            </div>
                                                        @elseif($field->type == 'select')
                                                            {!! Form::select('custom_fields_data['.$field->name.'_'.$field->id.']',
                                                                    $field->values,
                                                                    isset($editUser)?$editUser->custom_fields_data['field_'.$field->id]:'',['class' => 'form-control gender'])
                                                            !!}

                                                        @elseif($field->type == 'checkbox')
                                                        <div class="mt-checkbox-inline custom-checkbox checkbox-{{$field->id}}">
                                                            <input type="hidden" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" 
                                                            id="{{$field->name.'_'.$field->id}}" value=" ">
                                                            @foreach($field->values as $key => $value)
                                                                <label class="mt-checkbox mt-checkbox-outline">
                                                                    <input name="{{$field->name.'_'.$field->id}}[]"
                                                                           type="checkbox" onchange="checkboxChange('checkbox-{{$field->id}}', '{{$field->name.'_'.$field->id}}')" value="{{$value}}"> {{$value}}
                                                                    <span></span>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                        @elseif($field->type == 'date')
                                                            <input type="text" class="form-control date-picker" size="16" name="custom_fields_data[{{$field->name.'_'.$field->id}}]"
                                                                value="{{ isset($editUser->dob)?Carbon\Carbon::parse($editUser->dob)->format('Y-m-d'):Carbon\Carbon::now()->format($global->date_format)}}">
                                                        @endif
                                                        <div class="form-control-focus"> </div>
                                                        <span class="help-block"></span>

                                                    </div>
                                                </div>
                                            @endforeach

                                        </div>
                                    @endif

                                <!--/span-->
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.tasks.priority')</label>

                                        <div class="radio radio-danger">
                                            <input type="radio" name="priority" id="radio13"
                                                   value="high">
                                            <label for="radio13" class="text-danger">
                                                @lang('modules.tasks.high') </label>
                                        </div>
                                        <div class="radio radio-warning">
                                            <input type="radio" name="priority"
                                                   id="radio14" checked value="medium">
                                            <label for="radio14" class="text-warning">
                                                @lang('modules.tasks.medium') </label>
                                        </div>
                                        <div class="radio radio-success">
                                            <input type="radio" name="priority" id="radio15"
                                                   value="low">
                                            <label for="radio15" class="text-success">
                                                @lang('modules.tasks.low') </label>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="row m-b-20">
                                    <div class="col-xs-12">
                                        @if($upload)
                                            <button type="button"
                                                    class="btn btn-block btn-outline-info btn-sm col-md-2 select-image-button"
                                                    style="margin-bottom: 10px;display: none "><i class="fa fa-upload"></i>
                                                File Select Or Upload
                                            </button>
                                            <div id="file-upload-box">
                                                <div class="row" id="file-dropzone">
                                                    <div class="col-xs-12">
                                                        <div class="dropzone"
                                                             id="file-upload-dropzone">
                                                            {{ csrf_field() }}
                                                            <div class="fallback">
                                                                <input name="file" type="file" multiple/>
                                                            </div>
                                                            <input name="image_url" id="image_url" type="hidden"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="taskID" id="taskID">
                                        @else
                                            <div class="alert alert-danger">@lang('messages.storageLimitExceed', ['here' => '<a href='.route('admin.billing.packages'). '>Here</a>'])</div>
                                        @endif
                                    </div>
                                </div>
                                @if(isset($taskRequests))
                                <div class="row" id="list">
                                    <ul class="list-group" id="files-list">
                                        @forelse($taskRequests->files as $file)
                                            <li class="list-group-item">
                                                <div class="row">
                                                    <div class="col-md-9">
                                                        {{ $file->filename }}
                                                    </div>
                                                    <div class="col-md-3">
                                                            <a target="_blank" href="{{ $file->file_url }}"
                                                               data-toggle="tooltip" data-original-title="View"
                                                               class="btn btn-info btn-circle"><i
                                                                        class="fa fa-search"></i></a>
                                                        @if(is_null($file->external_link))
                                                            &nbsp;&nbsp;
                                                            <a href="{{ route('admin.task-request.download', $file->id) }}"
                                                               data-toggle="tooltip" data-original-title="Download"
                                                               class="btn btn-inverse btn-circle"><i
                                                                        class="fa fa-download"></i></a>
                                                        @endif
                                                        &nbsp;&nbsp;
                                                        <a href="javascript:;" data-toggle="tooltip"
                                                           data-original-title="Delete"
                                                           data-file-id="{{ $file->id }}"
                                                           class="btn btn-danger btn-circle sa-params .file-delete" data-pk="list"><i
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
                                @endif
                            </div>

                            </div>
                            <!--/row-->

                        </div>
                        <div class="form-actions">
                            <button type="button" id="store-task" class="btn btn-success"><i
                                        class="fa fa-check"></i> @lang('app.save')</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>    <!-- .row -->

    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="taskCategoryModal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-md" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span>
                </div>
                <div class="modal-body">
                    Loading...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn blue">Save changes</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->.
    </div>
    {{--Ajax Modal Ends--}}
    <div class="modal fade bs-modal-lg in" id="taskLabelModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span>
                </div>
                <div class="modal-body">
                    Loading...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn blue">Save changes</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->.
    </div>
    {{--Ajax Modal Ends--}}


@endsection

@push('footer-script')
    <script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.js') }}"></script>
    <script>
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
                var url = "{{ route('admin.task-request.delete-file', ':id') }}";
                url = url.replace(':id', id);
                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token, '_method': 'DELETE', 'view': deleteView},
                    success: function (response) {
                        console.log(response);
                        if (response.status == "success") {
                            $('#task-file-'+id).remove();

                            //$.unblockUI();
                            $('#list ul.list-group').html(response.html);

                        }
                    }
                });
            }
        });
    });
        function checkboxChange(parentClass, id){
            var checkedData = '';
            $('.'+parentClass).find("input[type= 'checkbox']:checked").each(function () {
                if(checkedData !== ''){
                    checkedData = checkedData+', '+$(this).val();
                }
                else{
                    checkedData = $(this).val();
                }
            });
            $('#'+id).val(checkedData);
        }

        $('#multiselect').selectpicker();
        @if($upload)
            Dropzone.autoDiscover = false;
            //Dropzone class
            myDropzone = new Dropzone("div#file-upload-dropzone", {
                url: "{{ route('admin.task-files.store') }}",
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                paramName: "file",
                maxFilesize: 10,
                maxFiles: 10,
                acceptedFiles: "image/*,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/docx,application/pdf,text/plain,application/msword,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                autoProcessQueue: false,
                uploadMultiple: true,
                addRemoveLinks: true,
                parallelUploads: 10,
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

            myDropzone.on('sending', function (file, xhr, formData) {
                console.log(myDropzone.getAddedFiles().length, 'sending');
                var ids = $('#taskID').val();
                var task_request_id = $('#task_request_id').val();
                formData.append('task_id', ids);
                formData.append('task_request_id', task_request_id);
            });

            myDropzone.on('completemultiple', function () {
            var msgs = "@lang('messages.taskCreatedSuccessfully')";
            $.showToastr(msgs, 'success');
            window.location.href = '{{ route('admin.all-tasks.index') }}'

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

        //    update task
        $('#store-task').click(function () {
            $.easyAjax({
                url: '{{route('admin.all-tasks.store')}}',
                container: '#storeTask',
                type: "POST",
                data: $('#storeTask').serialize(),
                success: function (data) {
                    $('#storeTask').trigger("reset");
                    $('.summernote').summernote('code', '');
                    var dropzone = 0;
                    @if($upload)
                        dropzone = myDropzone.getQueuedFiles().length;
                    @endif

                    if(dropzone > 0){
                        taskID = data.taskID;
                        $('#taskID').val(data.taskID);
                        myDropzone.processQueue();
                    } else {
                        var msgs = "@lang('messages.taskCreatedSuccessfully')";
                        $.showToastr(msgs, 'success');
                       window.location.href = '{{ route('admin.all-tasks.index') }}'
                    }
                }
            })
        });
        $('#without_duedate').click(function () {
            var check = $('#without_duedate').is(":checked") ? true : false;
            if(check == true){
                $('#duedateBox').hide();
                $('#due_date2').val('');
            }
            else{
            $('#duedateBox').show();
            }
        });
        $("#due_date2").datepicker({
            autoclose: true,
            weekStart:'{{ $global->week_start }}',
            format: '{{ $global->date_picker_format }}',
        });
        @if(isset($taskRequests))
            @if($taskRequests->due_date == null)
                 $('#duedateBox').hide();
            @endif
        @endif
        var minDate = new Date();
        // $('#due_date2').datepicker("update", minDate);
        $('#due_date2').datepicker('setStartDate', minDate);

        jQuery('#start_date2').datepicker({
            autoclose: true,
            todayHighlight: true,
            weekStart: '{{ $global->week_start }}',
            format: '{{ $global->date_picker_format }}',
        }).on('changeDate', function (selected) {
            var minDate = new Date(selected.date.valueOf());
            $('#due_date2').datepicker('setStartDate', minDate);
        });

        $(".select2").select2({
            formatNoMatches: function () {
                return "{{ __('messages.noRecordFound') }}";
            }
        });

        $('#project_id').change(function () {
            var id = $(this).val();
            var url = '{{route('admin.all-tasks.members', ':id')}}';
            url = url.replace(':id', id);

            $.easyAjax({
                url: url,
                type: "GET",
                redirect: true,
                success: function (data) {
                    $('#user_id').html(data.html);
                }
            })

            // For getting dependent task
            var dependentTaskUrl = '{{route('admin.all-tasks.dependent-tasks', ':id')}}';
            dependentTaskUrl = dependentTaskUrl.replace(':id', id);
            $.easyAjax({
                url: dependentTaskUrl,
                type: "GET",
                success: function (data) {
                    $('#dependent_task_id').html(data.html);
                }
            })
        });

        $('#repeat-task').change(function () {
            if ($(this).is(':checked')) {
                $('#repeat-fields').show();
            } else {
                $('#repeat-fields').hide();
            }
        })

        $('#dependent-task').change(function () {
            if ($(this).is(':checked')) {
                $('#dependent-fields').show();
            } else {
                $('#dependent-fields').hide();
            }
        })

        $('#set-time-estimate').change(function () {
        if($(this).is(':checked')){
            $('#set-time-estimate-fields').show();
        }
        else{
            $('#set-time-estimate-fields').hide();
        }
    })
    </script>
    <script>
        $('#createTaskCategory').click(function () {
            var url = '{{ route('admin.taskCategory.create-cat')}}';
            $('#modelHeading').html("@lang('modules.taskCategory.manageTaskCategory')");
            $.ajaxModal('#taskCategoryModal', url);
        })
        $('#createTaskLabel').click(function(){
            var url = '{{ route('admin.task-label.create-label')}}';
            $('#modelHeading').html("");
            $.ajaxModal('#taskLabelModal', url);
        })

        $('#add-employee').click(function () {
            var url = '{{ route('admin.employees.create')}}';
            $('#modelHeading').html("@lang('app.add') @lang('app.employee')");
            $.ajaxModal('#projectTimerModal', url);
        });

    </script>
@endpush

