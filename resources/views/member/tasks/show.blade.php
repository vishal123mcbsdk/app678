@extends('layouts.member-app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12 bg-title-left">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }} #{{ $project->id }} - <span class="font-bold">{{ ucwords($project->project_name) }}</span></h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-6 col-sm-8 col-md-8 col-xs-12 bg-title-right">
            <ol class="breadcrumb">
                <li><a href="{{ route('member.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('member.projects.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.menu.tasks')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/icheck/skins/all.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/multiselect/css/multi-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">

@endpush

@section('content')

    <div class="row">
        <div class="col-xs-12">

            <section>
                <div class="sttabs tabs-style-line">
                    @include('member.projects.show_project_menu')

                    <div class="content-wrap">
                        <section id="section-line-3" class="show">
                            {{-- <div class="row"> --}}
                                <div class="col-xs-12" id="task-list-panel">
                                    {{--<div class="white-box">--}}
                                    <div class="row m-b-10">
                                        <div class="col-md-12 hide" id="new-task-panel">
                                            <div class="panel panel-default">
                                                <div class="panel-heading "><i class="ti-plus"></i> @lang('modules.tasks.newTask')
                                                    <div class="panel-action">
                                                        <a href="javascript:;" id="hide-new-task-panel"><i class="ti-close"></i></a>
                                                    </div>
                                                </div>
                                                <div class="panel-wrapper collapse in">
                                                    <div class="panel-body">
                                                        {!! Form::open(['id'=>'createTask','class'=>'ajax-form','method'=>'POST']) !!}

                                                        {!! Form::hidden('project_id', $project->id) !!}

                                                        <div class="form-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label class="control-label required">@lang('app.title')</label>
                                                                        <input type="text" id="heading" name="title"
                                                                               class="form-control">
                                                                    </div>
                                                                </div>
                                                                <!--/span-->
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label class="control-label">@lang('modules.tasks.taskCategory') </label>
                                                                        <select class="selectpicker form-control" name="category_id" id="category_id"
                                                                                data-style="form-control">
                                                                            @forelse($categories as $category)
                                                                                <option value="{{ $category->id }}">{{ ucwords($category->category_name) }}</option>
                                                                            @empty
                                                                                <option value="">@lang('messages.noTaskCategoryAdded')</option>
                                                                            @endforelse
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <div class="col-xs-12">
                                                                    <div class="form-group">
                                                                        <label class="control-label">@lang('app.description')</label>
                                                                        <textarea id="description" name="description"
                                                                                  class="form-control summernote"></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label class="control-label required">@lang('app.startDate')</label>
                                                                        <input type="text" name="start_date" id="start_date" autocomplete="false" class="form-control" value="">
                                                                    </div>
                                                                </div>
                                                                <!--/span-->
                                                                <div class="col-md-4" id="duedateBox">
                                                                    <div class="form-group">
                                                                        <label class="control-label required">@lang('app.dueDate')</label>
                                                                        <input type="text" name="due_date" id="due_date" class="form-control"
                                                                               autocomplete="off">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group" style="padding-top: 25px;">
                                                                        <div class="checkbox checkbox-info">
                                                                            <input id="without_duedate" name="without_duedate" value="true"
                                                                                   type="checkbox">
                                                                            <label for="without_deadline">@lang('modules.tasks.withoutDuedate') </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="clearfix"></div>
                                                                <!--/span-->
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                
                                                                        <div class="checkbox checkbox-info">
                                                                            <input id="private-task" name="is_private" value="true"
                                                                                   type="checkbox">
                                                                            <label for="private-task">@lang('modules.tasks.makePrivate') <a class="mytooltip font-12" href="javascript:void(0)"> <i class="fa fa-info-circle"></i><span class="tooltip-content5"><span class="tooltip-text3"><span class="tooltip-inner2">@lang('modules.tasks.privateInfo')</span></span></span></a></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                
                                                                        <div class="checkbox checkbox-info">
                                                                            <input id="billable-task" checked name="billable" value="true" type="checkbox">
                                                                            <label for="billable-task">@lang('modules.tasks.billable') 
                                                                                <a class="mytooltip font-12" href="javascript:void(0)"> <i
                                                                                class="fa fa-info-circle"></i>
                                                                                    <span class="tooltip-content5">
                                                                                        <span class="tooltip-text3">
                                                                                            <span class="tooltip-inner2">
                                                                                                @lang('modules.tasks.billableInfo')
                                                                                            </span>
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
                                                                @if ($user->cans('view_projects'))
                                                                    <div class="col-md-12">
                                                                        <label class="control-label">@lang('modules.projects.milestones')</label>
                                                                        <div class="form-group">
                                                                            <select class="selectpicker form-control" name="milestone_id" id="milestone_id"
                                                                                    data-style="form-control">
                                                                                <option value="">--</option>
                                                                                @foreach($project->milestones as $milestone)
                                                                                    <option value="{{ $milestone->id }}">{{ $milestone->milestone_title }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                                <div class="col-xs-12">
                                                                    <div class="form-group">
                                                                        <label class="control-label required">@lang('modules.tasks.assignTo')</label>
                                                                        <select class="select2 select2-multiple " multiple="multiple" data-placeholder="@lang('modules.tasks.chooseAssignee')"  name="user_id[]" id="user_id">
                                                                            <option value="">--</option>
                                                                            @foreach($project->members as $member)
                                                                                <option value="{{ $member->user->id }}">{{ $member->user->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
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
                                                                            <input type="radio" name="priority" checked
                                                                                   id="radio14" value="medium">
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
                                                            </div>
                                                            <!--/row-->

                                                        </div>
                                                        <div class="form-actions">
                                                            <button type="submit" id="save-task" class="btn btn-success"><i
                                                                        class="fa fa-check"></i> @lang('app.save')
                                                            </button>
                                                        </div>
                                                        {!! Form::close() !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12 hide" id="edit-task-panel">
                                        </div>
                                    </div>
                                    {{--</div>--}}
                                    <div class="white-box">
                                        <h2>@lang('app.menu.tasks')</h2>

                                        <div class="row m-b-10">
                                            <div class="col-md-6">
                                                <a href="javascript:;" id="show-new-task-panel" class="btn btn-success btn-outline btn-sm">
                                                    <i class="fa fa-plus"></i>
                                                    @lang('modules.tasks.newTask')
                                                </a>
                                            </div>
                                        </div>


                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover toggle-circle default footable-loaded footable"
                                                   id="tasks-table">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>@lang('app.task')</th>
                                                        <th>@lang('app.client')</th>
                                                        <th>@lang('modules.tasks.assignTo')</th>
                                                        <th>@lang('modules.tasks.assignBy')</th>
                                                        <th>@lang('app.dueDate')</th>
                                                        <th>@lang('app.status')</th>
                                                        <th>@lang('app.action')</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            {{-- </div> --}}
                        </section>

                    </div><!-- /content -->
                </div><!-- /tabs -->
            </section>
        </div>


    </div>
    <!-- .row -->

@endsection

@push('footer-script')
<script src="{{ asset('js/cbpFWTabs.js') }}"></script>
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>

<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">
    var newTaskpanel = $('#new-task-panel');
    var taskListPanel = $('#task-list-panel');
    var editTaskPanel = $('#edit-task-panel');

    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
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
            ["view", ["fullscreen"]]
        ]
    });

    var table = '';

    function showTable() {
        var url = '{!!  route('member.tasks.data', [':projectId']) !!}?_token={{ csrf_token() }}';

        url = url.replace(':projectId', '{{ $project->id }}');

        table = $('#tasks-table').dataTable({
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                "url": url,
                "type": "POST"
            },
            deferRender: true,
            language: {
                "url": "<?php echo __("app.datatable") ?>"
            },
            "fnDrawCallback": function (oSettings) {
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            },
            "fnRowCallback" : function(nRow, aData, iDisplayIndex){
                $("td:first", nRow).html(iDisplayIndex +1);
                return nRow;
                },
            "order": [[0, "desc"]],
            columns: [
                {data: 'id', name: 'id'},
                {data: 'heading', name: 'heading'},
                {data: 'clientName', name: 'client.name', bSort: false},
                {data: 'username', name: 'users.name', searchable: false},
                {data: 'created_by', name: 'creator_user.name'},
                {data: 'due_date', name: 'due_date'},
                {data: 'board_column', name: 'board_column', searchable: false},
                {data: 'action', name: 'action', "searchable": false}
            ]
        });
    }
    $('body').on('click', '.sa-params', function () {
        var id = $(this).data('task-id');

        var buttons = {
            cancel: "@lang('messages.confirmNoArchive')",
            confirm: {
                text: "@lang('messages.deleteConfirmation')",
                value: 'confirm',
                visible: true,
                className: "danger",
            }
        };

        swal({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.confirmation.recoverDeletedTask')",
            dangerMode: true,
            icon: 'warning',
            buttons: buttons
        }).then(function (isConfirm) {
            if (isConfirm == 'confirm') {

                var url = "{{ route('member.all-tasks.destroy',':id') }}";
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
                            table._fnDraw();
                        }
                    }
                });
            }
        });
    });

    $('#tasks-table').on('click', '.show-task-detail', function () {
        $(".right-sidebar").slideDown(50).addClass("shw-rside");

        var id = $(this).data('task-id');
        var url = "{{ route('member.all-tasks.show',':id') }}";
        url = url.replace(':id', id);

        $.easyAjax({
            type: 'GET',
            url: url,
            success: function (response) {
                if (response.status == "success") {
                    $('#right-sidebar-content').html(response.view);
                }
            }
        });
    })

    jQuery('#start_date').datepicker({
        format: '{{ $global->date_picker_format }}',
        autoclose: true,
        todayHighlight: true
    }).on('changeDate', function (selected) {
        $('#due_date').datepicker({
            format: '{{ $global->date_picker_format }}',
            autoclose: true,
            todayHighlight: true
        });
        var minDate = new Date(selected.date.valueOf());
        $('#due_date').datepicker("update", minDate);
        $('#due_date').datepicker('setStartDate', minDate);
    });
    showTable();

    //    save new task
    $('#save-task').click(function () {
        $.easyAjax({
            url: '{{route('member.tasks.store')}}',
            container: '#section-line-3',
            type: "POST",
            data: $('#createTask').serialize(),
            success: function (data) {
                $('#createTask').trigger("reset");
                $('.summernote').summernote('code', '');
                $('#task-list-panel ul.list-group').html(data.html);
                newTaskpanel.switchClass("show", "hide", 300, "easeInOutQuad");
                showTable();
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
                window.location.reload();
            }
        })
    });

    //    save new task
    taskListPanel.on('click', '.edit-task', function () {
        var id = $(this).data('task-id');
        var url = "{{route('member.tasks.edit', ':id')}}";
        url = url.replace(':id', id);

        $.easyAjax({
            url: url,
            type: "GET",
            container: '#task-list-panel',
            data: {taskId: id},
            success: function (data) {
                editTaskPanel.html(data.html);
                newTaskpanel.addClass('hide').removeClass('show');
                editTaskPanel.switchClass("hide", "show", 300, "easeInOutQuad");
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });

                $('html, body').animate({
                    scrollTop: $("#task-list-panel").offset().top
                }, 1000);
            }
        })
    });

    //    change task status
    taskListPanel.on('click', '.task-check', function () {
        if ($(this).is(':checked')) {
            var status = 'completed';
        }else{
            var status = 'incomplete';
        }

        var sortBy = $('#sort-task').val();

        var id = $(this).data('task-id');

        if(status == 'completed'){
            var checkUrl = '{{route('member.tasks.checkTask', ':id')}}';
            checkUrl = checkUrl.replace(':id', id);
            $.easyAjax({
                url: checkUrl,
                type: "GET",
                container: '#task-list-panel',
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
                                updateTask(id,status,sortBy)
                            }
                        });
                    }
                    else{
                        updateTask(id,status,sortBy)
                    }

                }
            });
        }
        else{
            updateTask(id,status,sortBy)
        }


    });

    // Update Task
    function updateTask(id,status,sortBy){
        var url = "{{route('member.tasks.changeStatus')}}";
        var token = "{{ csrf_token() }}";
        $.easyAjax({
            url: url,
            type: "POST",
            container: '#section-line-3',
            data: {'_token': token, taskId: id, status: status, sortBy: sortBy},
            success: function (data) {
                $('#task-list-panel ul.list-group').html(data.html);
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            }
        })
    }

    //    save new task
    $('#sort-task, #hide-completed-tasks').change(function() {
        var sortBy = $('#sort-task').val();
        var id = $('#sort-task').data('project-id');

        var url = "{{route('member.tasks.sort')}}";
        var token = "{{ csrf_token() }}";

        if ($('#hide-completed-tasks').is(':checked')) {
            var hideCompleted = '1';
        }else {
            var hideCompleted = '0';
        }

        $.easyAjax({
            url: url,
            type: "POST",
            container: '#task-list-panel',
            data: {'_token': token, projectId: id, sortBy: sortBy, hideCompleted: hideCompleted},
            success: function (data) {
                $('#task-list-panel ul.list-group').html(data.html);
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            }
        })
    });

    $('#show-new-task-panel').click(function () {
        editTaskPanel.addClass('hide').removeClass('show');
        newTaskpanel.switchClass("hide", "show", 300, "easeInOutQuad");

        $('html, body').animate({
            scrollTop: $("#task-list-panel").offset().top
        }, 1000);
    });

    $('#hide-new-task-panel').click(function () {
        newTaskpanel.addClass('hide').removeClass('show');
        taskListPanel.switchClass("col-md-6", "col-md-12", 1000, "easeInOutQuad");
    });

    editTaskPanel.on('click', '#hide-edit-task-panel', function () {
        editTaskPanel.addClass('hide').removeClass('show');
        taskListPanel.switchClass("col-md-6", "col-md-12", 1000, "easeInOutQuad");
    });

    $('#dependent-task').change(function () {
        if($(this).is(':checked')){
            $('#dependent-fields').show();
        }
        else{
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

    $('ul.showProjectTabs .projectTasks').addClass('tab-current');

</script>
@endpush
