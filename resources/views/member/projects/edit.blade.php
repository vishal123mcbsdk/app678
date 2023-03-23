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
                <li><a href="{{ route('member.projects.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.edit')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">

<link rel="stylesheet" href="{{ asset('plugins/bower_components/ion-rangeslider/css/ion.rangeSlider.css') }}">
<link rel="stylesheet"
      href="{{ asset('plugins/bower_components/ion-rangeslider/css/ion.rangeSlider.skinModern.css') }}">
@endpush

@section('content')

    <div class="row">
        <div class="col-xs-12">

            <div class="panel panel-inverse">
                <div class="panel-heading"> @lang('modules.projects.updateTitle')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        {!! Form::open(['id'=>'updateProject','class'=>'ajax-form','method'=>'PUT']) !!}
                        <div class="form-body ">
                            <div class="row">
                                <div class="col-xs-12 ">
                                    <div class="form-group">
                                        <label>@lang('modules.projects.projectName')</label>
                                        <input type="text" name="project_name" id="project_name" class="form-control"
                                               value="{{ $project->project_name }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 ">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.projects.projectCategory')</label>
                                        <select class="select2 form-control" name="category_id" id="category_id"
                                                data-style="form-control">
                                            @forelse($categories as $category)
                                                <option value="{{ $category->id }}"
                                                        @if($project->category_id == $category->id)
                                                        selected
                                                        @endif
                                                >{{ ucwords($category->category_name) }}</option>
                                            @empty
                                                <option value="">@lang('messages.noProjectCategoryAdded')</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 ">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.projects.selectClient')</label>
                                        <select class="select2 form-control" name="client_id" id="client_id"
                                                data-style="form-control">
                                            <option value="null">--</option>
                                            @forelse($clients as $client)
                                                <option value="{{ $client->id }}"
                                                        @if($project->client_id == $client->id)
                                                        selected
                                                        @endif
                                                >{{ ucwords($client->name) }}</option>
                                            @empty
                                                <option value="">@lang('modules.projects.selectClient')</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-md-5">
                                    <div class="form-group">
                                        <div class="checkbox checkbox-info  col-md-10">
                                            <input id="client_view_task" name="client_view_task" value="true" onchange="checkTask()"
                                                   @if($project->client_view_task == "enable") checked @endif
                                                   type="checkbox">
                                            <label for="client_view_task">@lang('modules.projects.clientViewTask')</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-3" id="clientNotification">
                                    <div class="form-group">
                                        <div class="checkbox checkbox-info  col-md-10">
                                            <input id="client_task_notification" name="client_task_notification" value="true"  @if($project->allow_client_notification == "enable") checked @endif
                                            type="checkbox">
                                            <label for="client_task_notification">@lang('modules.projects.clientTaskNotification')</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-md-4">
                                    <div class="form-group">
                                        <div class="checkbox checkbox-info  col-md-10">
                                            <input id="manual_timelog" name="manual_timelog" value="true"
                                                   @if($project->manual_timelog == "enable") checked @endif
                                                   type="checkbox">
                                            <label for="manual_timelog">@lang('modules.projects.manualTimelog')</label>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('modules.projects.startDate')</label>
                                        <input type="text" name="start_date" autocomplete="off" id="start_date" class="form-control"
                                               value="{{ $project->start_date->format($global->date_format) }}">
                                    </div>
                                </div>
                                <!--/span-->

                                <div class="col-md-6" id="deadlineBox">
                                    <div class="form-group">
                                        <label>@lang('modules.projects.deadline')</label>
                                        <input type="text" name="deadline" autocomplete="off" id="deadline" class="form-control"
                                               value="@if($project->deadline){{ $project->deadline->format($global->date_format) }} @else {{ \Carbon\Carbon::now()->format($global->date_format) }} @endif">
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-4">
                                    <div class="form-group" style="padding-top: 25px;">
                                        <div class="checkbox checkbox-info">
                                            <input id="without_deadline" @if($project->deadline == null) checked @endif name="without_deadline" value="true"
                                                   type="checkbox">
                                            <label for="without_deadline">@lang('modules.projects.withoutDeadline')</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--/row-->

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.projects.projectSummary')</label>
                                        <textarea name="project_summary" id="project_summary"
                                                  class="summernote">{{ $project->project_summary }}</textarea>
                                    </div>
                                </div>

                            </div>
                            <!--/span-->

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.projects.note')</label>
                                        <textarea name="notes" id="notes" rows="5"
                                                  class="form-control summernote">{{ $project->notes }}</textarea>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.projects.clientFeedback')</label>
                                        <textarea name="feedback" id="feedback" rows="5"
                                                  class="form-control">{{ $project->feedback }}</textarea>
                                    </div>
                                </div>

                            </div>
                            <!--/span-->

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.projects.projectCompletionStatus')</label>

                                        <div id="range_01"></div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="completion_percent" id="completion_percent"
                                   value="{{ $project->completion_percent }}">

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group last">
                                        <div class="checkbox checkbox-info  col-md-10">
                                            <input id="calculate-task-progress" name="calculate_task_progress" value="true"
                                                   @if($project->calculate_task_progress == "true") checked @endif
                                                   type="checkbox">
                                            <label for="calculate-task-progress">@lang('modules.projects.calculateTasksProgress')</label>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.project') @lang('app.status')</label>
                                        <select name="status" id="" class="form-control">
                                            <option
                                                    @if($project->status == 'not started') selected @endif
                                            value="not started">@lang('app.notStarted')
                                            </option>
                                            <option
                                                    @if($project->status == 'in progress') selected @endif
                                            value="in progress">@lang('app.inProgress')
                                            </option>
                                            <option
                                                    @if($project->status == 'on hold') selected @endif
                                            value="on hold">@lang('app.onHold')
                                            </option>
                                            <option
                                                    @if($project->status == 'canceled') selected @endif
                                            value="canceled">@lang('app.canceled')
                                            </option>
                                            <option
                                                    @if($project->status == 'finished') selected @endif
                                            value="finished">@lang('app.finished')
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!--/span-->
                            <div class="row">
                                @foreach($fields as $field)
                                    <div class="col-md-6">
                                        <label>{{ ucfirst($field->label) }}</label>
                                        <div class="form-group">
                                            @if( $field->type == 'text')
                                                <input type="text" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" placeholder="{{$field->label}}" value="{{$project->custom_fields_data['field_'.$field->id] ?? ''}}">
                                            @elseif($field->type == 'password')
                                                <input type="password" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" placeholder="{{$field->label}}" value="{{$project->custom_fields_data['field_'.$field->id] ?? ''}}">
                                            @elseif($field->type == 'number')
                                                <input type="number" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" placeholder="{{$field->label}}" value="{{$project->custom_fields_data['field_'.$field->id] ?? ''}}">

                                            @elseif($field->type == 'textarea')
                                                <textarea name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" id="{{$field->name}}" cols="3">{{$project->custom_fields_data['field_'.$field->id] ?? ''}}</textarea>

                                            @elseif($field->type == 'radio')
                                                <div class="radio-list">
                                                    @foreach($field->values as $key=>$value)
                                                        <label class="radio-inline @if($key == 0) p-0 @endif">
                                                            <div class="radio radio-info">
                                                                <input type="radio" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" id="optionsRadios{{$key.$field->id}}" value="{{$value}}" @if(isset($project) && $project->custom_fields_data['field_'.$field->id] == $value) checked @elseif($key==0) checked @endif>>
                                                                <label for="optionsRadios{{$key.$field->id}}">{{$value}}</label>
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @elseif($field->type == 'select')
                                                {!! Form::select('custom_fields_data['.$field->name.'_'.$field->id.']',
                                                        $field->values,
                                                         isset($project)?$project->custom_fields_data['field_'.$field->id]:'',['class' => 'form-control gender'])
                                                 !!}

                                            @elseif($field->type == 'checkbox')
                                                <div class="mt-checkbox-inline custom-checkbox checkbox-{{$field->id}}">
                                                    <input type="hidden" name="custom_fields_data[{{$field->name.'_'.$field->id}}]"
                                                           id="{{$field->name.'_'.$field->id}}" value="{{$project->custom_fields_data['field_'.$field->id]}}">
                                                    @foreach($field->values as $key => $value)
                                                        <label class="mt-checkbox mt-checkbox-outline">
                                                            <input name="{{$field->name.'_'.$field->id}}[]" class="custom_fields_data[{{$field->name.'_'.$field->id}}]"
                                                                   type="checkbox" value="{{$value}}" onchange="checkboxChange('checkbox-{{$field->id}}', '{{$field->name.'_'.$field->id}}')"
                                                                   @if($project->custom_fields_data['field_'.$field->id] != '' && in_array($value ,explode(', ', $project->custom_fields_data['field_'.$field->id]))) checked @endif > {{$value}}
                                                            <span></span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @elseif($field->type == 'date')
                                                <input type="text" class="form-control date-picker" size="16" name="custom_fields_data[{{$field->name.'_'.$field->id}}]"
                                                       value="{{ ($project->custom_fields_data['field_'.$field->id] != '') ? \Carbon\Carbon::parse($project->custom_fields_data['field_'.$field->id])->format($global->date_format) : \Carbon\Carbon::now()->format($global->date_format)}}">
                                            @endif
                                            <div class="form-control-focus"> </div>
                                            <span class="help-block"></span>

                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                        <div class="form-actions m-t-15">
                            <button type="submit" id="save-form" class="btn btn-success"><i
                                        class="fa fa-check"></i> @lang('app.update')</button>

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
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>

<script src="{{ asset('plugins/bower_components/ion-rangeslider/js/ion-rangeSlider/ion.rangeSlider.min.js') }}"></script>
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
    
    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });
    checkTask();
    function checkTask()
    {
        var chVal = $('#client_view_task').is(":checked") ? true : false;
        if(chVal == true){
            $('#clientNotification').show();
        }
        else{
            $('#clientNotification').hide();
        }

    }

    @if($project->deadline == null)
        $('#deadlineBox').hide();
    @endif

    $('#without_deadline').click(function () {
        var check = $('#without_deadline').is(":checked") ? true : false;
        if(check == true){
            $('#deadlineBox').hide();
        }
        else{
            $('#deadlineBox').show();
        }
    });

    $("#deadline").datepicker({
        autoclose: true,
        format: '{{ $global->date_picker_format }}',
    })

    $("#start_date").datepicker({
        todayHighlight: true,
        autoclose: true,
        format: '{{ $global->date_picker_format }}',
    }).on('changeDate', function (selected) {
        var minDate = new Date(selected.date.valueOf());
        $('#deadline').datepicker('setStartDate', minDate);
    });



    $('#save-form').click(function () {
        $.easyAjax({
            url: '{{route('member.projects.update', [$project->id])}}',
            container: '#updateProject',
            type: "POST",
            redirect: true,
            data: $('#updateProject').serialize()
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

    var completion = $('#completion_percent').val();

    $("#range_01").ionRangeSlider({
        grid: true,
        min: 0,
        max: 100,
        from: parseInt(completion),
        postfix: "%",
        onFinish: saveRangeData
    });

    var slider = $("#range_01").data("ionRangeSlider");

    $('#calculate-task-progress').change(function () {
        if($(this).is(':checked')){
            console.log('ddd');
            slider.update({"disable": true});
        }
        else{
            slider.update({"disable": false});
        }
    })

    function saveRangeData(data) {
        var percent = data.from;
        $('#completion_percent').val(percent);
    }

    $(':reset').on('click', function(evt) {
        evt.preventDefault()
        $form = $(evt.target).closest('form')
        $form[0].reset()
        $form.find('select').select2()
    });

    @if($project->calculate_task_progress == "true")
        slider.update({"disable": true});
    @endif
</script>


@endpush
