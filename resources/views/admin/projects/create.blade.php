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
                <li><a href="{{ route('admin.projects.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.addNew')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.css') }}">

<style>
    .panel-black .panel-heading a, .panel-inverse .panel-heading a {
        color: unset!important;
    }
    .bootstrap-select.btn-group .dropdown-menu li a span.text {
        color: #000;
    }
    .panel-black .panel-heading a:hover, .panel-inverse .panel-heading a:hover {
        color: #000 !important;
    }
    .panel-black .panel-heading a, .panel-inverse .panel-heading a {
        color: #000 !important;
    }
    .btn-info.active, .btn-info:active, .open>.dropdown-toggle.btn-info {
        background-color:unset !important; ;
        border-color: #269abc;
    }
    .note-editor{
        border: 1px solid #e4e7ea !important;
    }
    .btn-info.active.focus, .btn-info.active:focus, .btn-info.active:hover, .btn-info.focus, .btn-info.focus:active, .btn-info:active:focus, .btn-info:active:hover, .btn-info:focus, .open>.dropdown-toggle.btn-info.focus, .open>.dropdown-toggle.btn-info:focus, .open>.dropdown-toggle.btn-info:hover {
        background-color: #03a9f3;
        border: 1px solid #03a9f3;
        color: #000;
    }
</style>
@endpush

@section('content')

    <div class="row">
        <div class="col-xs-12">

            <div class="panel panel-inverse">
                <div class="panel-heading"> @lang('modules.projects.createTitle')
                    <div class="pull-right">
                        <div class="btn-group m-r-10">
                            <button aria-expanded="true" data-toggle="dropdown" class="btn btn-sm btn-info waves-effect dropdown-toggle waves-light" type="button">@lang('app.menu.template') <span class="caret"></span></button>
                            <ul role="menu" class="dropdown-menu pull-right">
                                @forelse($templates as $template)
                                <li onclick="setTemplate('{{$template->id}}')" role="presentation"><a href="javascript:void(0)" role="menuitem"><i class="icon wb-reply" aria-hidden="true"></i> {{ ucwords($template->project_name) }}</a></li>
                                @empty
                                <li class="text-dark"><a href="javascript:void(0)" role="menuitem">@lang('messages.noRecordFound')</a></li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        {!! Form::open(['id'=>'createProject','class'=>'ajax-form','method'=>'POST']) !!}
                        <input type ="hidden" id ="projectID" >
                        <div class="form-body">
                            <h3 class="box-title m-b-10">@lang('modules.projects.projectInfo')</h3>
                            <div class="row">
                                <div class="col-xs-12 col-md-6 ">
                                    <div class="form-group">
                                        <label class="required">@lang('modules.projects.projectName')
                                            <a class="mytooltip" href="javascript:void(0)">
                                                <i class="fa fa-info-circle"></i>
                                                <span class="tooltip-content5">
                                                    <span class="tooltip-text3">
                                                        <span class="tooltip-inner2">@lang('help_text.projectName')</span>
                                                    </span>
                                                </span>
                                            </a>
                                        </label>
                                        <input type="text" name="project_name" id="project_name" class="form-control">
                                        <input type="hidden" name="template_id" id="template_id">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-6 ">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.projects.projectCategory')

                                            <a class="mytooltip" href="javascript:void(0)">
                                                <i class="fa fa-info-circle"></i>
                                                <span class="tooltip-content5">
                                                    <span class="tooltip-text3">
                                                        <span class="tooltip-inner2">@lang('help_text.projectCategorySettings')</span>
                                                    </span>
                                                </span>
                                            </a>

                                            <a href="javascript:;" id="addProjectCategory" class="text-info"><i class="ti-settings text-info"></i> </a>
                                        </label>
                                        <select class="selectpicker form-control" name="category_id" id="category_id"
                                                data-style="form-control">
                                            @forelse($categories as $category)
                                                <option value="{{ $category->id }}">{{ ucwords($category->category_name) }}</option>
                                            @empty
                                                <option value="">@lang('messages.noProjectCategoryAdded')</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                                
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="required">@lang('modules.projects.startDate')
                                            <a class="mytooltip" href="javascript:void(0)">
                                                <i class="fa fa-info-circle"></i>
                                                <span class="tooltip-content5">
                                                    <span class="tooltip-text3">
                                                        <span class="tooltip-inner2">@lang('help_text.startDate')</span>
                                                    </span>
                                                </span>
                                            </a>
                                        </label>
                                        <input type="text" name="start_date" id="start_date" autocomplete="off" class="form-control">
                                    </div>
                                </div>
                                <!--/span-->

                                <div class="col-md-4" id="deadlineBox">
                                    <div class="form-group">
                                        <label>@lang('modules.projects.deadline')</label>
                                        <input type="text" name="deadline" id="deadline" autocomplete="off" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group" style="padding-top: 25px;">
                                        <div class="checkbox checkbox-info">
                                            <input id="without_deadline" name="without_deadline" value="true"
                                                   type="checkbox">
                                            <label for="without_deadline">@lang('modules.projects.withoutDeadline')</label>
                                        </div>
                                    </div>
                                </div>

                                <!--/span-->
                            </div>

                            <div class="row">
                                <div class="col-md-4 col-xs-6">
                                    <div class="form-group">
                                        <div class="checkbox checkbox-info ">
                                            <input id="manual_timelog" name="manual_timelog" value="true"
                                                   type="checkbox">
                                            <label for="manual_timelog">@lang('modules.projects.manualTimelog')</label>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!--/row-->

                            <div class="row">
                                <div class="col-md-12 col-xs-12">
                                    <div class="form-group" id="user_id">
                                        <label class="required">@lang('modules.projects.addMemberTitle')</label>
                                        @if(in_array('employees',$modules))
                                            <a href="javascript:;" id="add-employee" class="btn btn-xs btn-success btn-outline"><i class="fa fa-plus"></i></a>
                                        @endif
                                        <select class="select2 m-b-10 select2-multiple " id="selectEmployee" multiple="multiple"
                                                data-placeholder="Choose Members" name="user_id[]">
                                            @foreach($employees as $emp)
                                                <option value="{{ $emp->id }}">{{ ucwords($emp->name) }} @if($emp->id == $user->id)
                                                        (YOU) @endif</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>
                          

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.projects.projectSummary')</label>
                                        <textarea name="project_summary" id="project_summary"
                                                  class="summernote"></textarea>
                                    </div>
                                </div>

                            </div>
                            <!--/span-->

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.projects.note')</label>
                                        <textarea name="notes" id="notes" rows="3" class="form-control summernote"></textarea>
                                    </div>
                                </div>

                            </div>
                            <!--/span-->

                            <h3 class="box-title m-b-10">@lang('modules.projects.clientInfo')</h3>
                            <div class="row">
                                <div class="col-xs-12">
                                    <label>@lang('modules.projects.selectClient')</label>
                                    <a href="javascript:;" id="add-client" class="btn btn-xs btn-success btn-outline"><i class="fa fa-plus"></i></a>
                                </div>
                                <div class="col-md-4 ">
                                    <div class="form-group">
                                        <select class="select2 form-control" name="client_id" id="client_id"
                                                data-style="form-control">
                                                <option value="">@lang('modules.projects.selectClient')</option>
                                            @foreach($clients as $client)
                                                <option value="{{ $client->id }}">{{ ucwords($client->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                

                                <div class="col-xs-12 col-md-4">
                                    <div class="form-group">
                                        <div class="checkbox checkbox-info  col-md-10">
                                            <input id="client_view_task" onchange="checkTask()" name="client_view_task" value="true"
                                                   type="checkbox">
                                            <label for="client_view_task">@lang('modules.projects.clientViewTask')</label>
                                        </div>
                                    </div>
                                </div>
                               
                                <div class="col-xs-12 col-md-4" id="clientNotification">
                                    <div class="form-group">
                                        <div class="checkbox checkbox-info  col-md-10">
                                            <input id="client_task_notification" name="client_task_notification" value="true"
                                                   type="checkbox">
                                            <label for="client_task_notification">@lang('modules.projects.clientTaskNotification')</label>
                                        </div>
                                    </div>
                                </div>
                                 

                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-md-4" id="readOnly">
                                    <div class="form-group">
                                        <div class="checkbox checkbox-info  col-md-10">
                                            <input id="read_only_task" name="read_only" value="true"
                                                type="checkbox">
                                            <label for="read_only_task">@lang('modules.projects.readOnly')</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h3 class="box-title m-b-30">@lang('modules.projects.budgetInfo')</h3>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.projects.projectBudget')</label>
                                        <input type="text" class="form-control" name="project_budget">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.invoices.currency')</label>
                                        <select name="currency_id" id="" class="form-control select2">
                                            @foreach ($currencies as $item)
                                                <option 
                                                @if (company_setting()->currency_id == $item->id)
                                                    selected
                                                @endif
                                                value="{{ $item->id }}">{{ $item->currency_name }} ({{ $item->currency_code }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.projects.hours_allocated')</label>
                                        <input type="text" name="hours_allocated" class="form-control">
                                    </div>
                                </div>


                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.project') @lang('app.status')</label>
                                        <select name="status" id="" class="form-control">
                                            <option
                                                    value="not started">@lang('app.notStarted')
                                            </option>
                                            <option
                                                    value="in progress">@lang('app.inProgress')
                                            </option>
                                            <option
                                                    value="on hold">@lang('app.onHold')
                                            </option>
                                            <option
                                                    value="canceled">@lang('app.canceled')
                                            </option>
                                            <option
                                                    value="finished">@lang('app.finished')
                                            </option>
                                            <option        
                                                    value="under review">@lang('app.underReview')
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

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
                                    <input type="hidden" name="taskIDField" id="taskIDField">
                                    @else
                                        <div class="alert alert-danger">@lang('messages.storageLimitExceed', ['here' => '<a href='.route('admin.billing.packages'). '>Here</a>'])</div>
                                    @endif
                                </div>
                            </div>
                            <!--/span-->

                            <div class="row">
                                @if(isset($fields))
                                    @foreach($fields as $field)
                                        <div class="col-md-6">

                                            <div class="form-group">
                                                <label @if($field->required == 'yes') class="required" @endif>{{ ucfirst($field->label) }}</label>
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
                                @endif

                            </div>

                        </div>
                        <div class="form-actions">
                            <button type="submit" id="save-form" class="btn btn-success"><i class="fa fa-check"></i>
                                @lang('app.save')
                            </button>

                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>    <!-- .row -->

    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="projectCategoryModal" role="dialog" aria-labelledby="myModalLabel"
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
        <!-- /.modal-dialog -->
    </div>
    {{--Ajax Modal Ends--}}
@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.js') }}"></script>

<script>
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
    
    $(".date-picker").datepicker({
        todayHighlight: true,
        autoclose: true,
        weekStart:'{{ $global->week_start }}',
        format: '{{ $global->date_picker_format }}'
    });

    projectID = '';
    Dropzone.autoDiscover = false;
    //Dropzone class
    myDropzone = new Dropzone("div#file-upload-dropzone", {
        url: "{{ route('admin.files.multiple-upload') }}",
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        paramName: "file",
        maxFilesize: 10,
        maxFiles: 10,
        acceptedFiles: "image/*,application/pdf",
        autoProcessQueue: false,
        uploadMultiple: true,
        addRemoveLinks:true,
        parallelUploads:10,
        dictDefaultMessage: "@lang('modules.projects.dropFile')",
        init: function () {
            myDropzone = this;
        }
    });
    myDropzone.on('sending', function(file, xhr, formData) {
        var ids = $('#projectID').val();
        formData.append('project_id', ids);
    });
    myDropzone.on('completemultiple', function () {
        var msgs = "@lang('modules.projects.projectUpdated')";
        $.showToastr(msgs, 'success');
        window.location.href = '{{ route('admin.projects.index') }}'
    });
    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });
    $('#clientNotification').hide();
    $('#readOnly').hide();
    $("#start_date").datepicker({
        todayHighlight: true,
        autoclose: true,
        weekStart:'{{ $global->week_start }}',
        format: '{{ $global->date_picker_format }}',
    }).on('changeDate', function (selected) {
        var minDate = new Date(selected.date.valueOf());
        $('#deadline').datepicker('setStartDate', minDate);
    });

    // check client view task checked
    function checkTask()
    {
        var chVal = $('#client_view_task').is(":checked") ? true : false;
        if(chVal == true){
            $('#clientNotification').show();
            $('#readOnly').show();
        }
        else{
            $('#clientNotification').hide();
            $('#readOnly').hide();
        }
    }

    $('#without_deadline').click(function () {
        var check = $('#without_deadline').is(":checked") ? true : false;
        if(check == true){
            $('#deadlineBox').hide();
        }
        else{
           $('#deadlineBox').show();
        }
    });

    // Set selected Template
    function setTemplate(id){
        var url = "{{ route('admin.projects.template-data',':id') }}";
        url = url.replace(':id', id);
        $.easyAjax({
            url: url,
            container: '#createProject',
            type: "GET",
            success: function(response){
                var selectedTemplate = [];
                if(id != null && id != undefined && id != ""){
                    selectedTemplate = response.templateData;

                    if(response.member){
                        $('#selectEmployee').val(response.member);
                        $('#selectEmployee').trigger('change');
                    }

                    $('#project_name').val(selectedTemplate['project_name']);
                    $('#category_id').selectpicker('val', selectedTemplate['category_id']);
                    $('#project_summary').summernote('code', selectedTemplate['project_summary']);
                    $('#notes').summernote('code', selectedTemplate['notes']);
                    $('#template_id').val(selectedTemplate['id']);

                    if(selectedTemplate['client_view_task'] == 'enable'){
                        $("#client_view_task").prop('checked', true);
                        $('#clientNotification').show();
                        $('#readOnly').show();
                        if(selectedTemplate['allow_client_notification'] == 'enable'){
                            $("#client_task_notification").prop('checked', 'checked');
                        }
                        else{
                            $("#client_task_notification").prop('checked', false);
                        }
                    }
                    else{
                        $("#client_view_task").prop('checked', false);
                        $("#client_task_notification").prop('checked', false);
                        $('#clientNotification').hide();
                        $('#readOnly').hide();
                    }
                    if(selectedTemplate['manual_timelog'] == 'enable'){
                        $("#manual_timelog").prop('checked', true);
                    }
                    else{
                        $("#manual_timelog").prop('checked', false);
                    }
                }
            }
        })

    }


    $("#deadline").datepicker({
                autoclose: true,
        weekStart:'{{ $global->week_start }}',
        format: '{{ $global->date_picker_format }}',
    });

    $('#save-form').click(function () {
        $.easyAjax({
            url: '{{route('admin.projects.store')}}',
            container: '#createProject',
            type: "POST",
            redirect: true,
            data: $('#createProject').serialize(),
            success: function(response){
                var dropzone = 0;
                @if($upload)
                    dropzone = myDropzone.getQueuedFiles().length;
                @endif

                if(dropzone > 0){
                    projectID = response.projectID;
                    $('#projectID').val(response.projectID);
                    myDropzone.processQueue();
                }
                else{
                    var msgs = "@lang('modules.projects.projectUpdated')";
                    $.showToastr(msgs, 'success');
                    window.location.href = '{{ route('admin.projects.index') }}'
                }
            }
        })
    });

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

    $(':reset').on('click', function(evt) {
        evt.preventDefault()
        $form = $(evt.target).closest('form')
        $form[0].reset()
        $form.find('select').selectpicker('render')
    });
</script>

<script>
    $('#createProject').on('click', '#addProjectCategory', function () {
        var url = '{{ route('admin.projectCategory.create-cat')}}';
        $('#modelHeading').html('Manage Project Category');
        $.ajaxModal('#projectCategoryModal', url);
    })

    $('#add-employee').click(function () {
        var url = '{{ route('admin.employees.create')}}';
        $('#modelHeading').html("@lang('app.add') @lang('app.employee')");
        $.ajaxModal('#projectTimerModal', url);
    });

    $('#add-client').click(function () {
        var url = '{{ route('admin.clients.create')}}';
        $('#modelHeading').html("@lang('app.add') @lang('app.client')");
        $.ajaxModal('#projectTimerModal', url);
    });
</script>
@endpush

