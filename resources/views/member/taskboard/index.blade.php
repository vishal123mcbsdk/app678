@extends('layouts.member-app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 bg-title-left">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12 text-right bg-title-right">
            <a href="javascript:;" id="toggle-filter" class="btn btn-outline btn-inverse btn-sm toggle-filter"><i class="fa fa-sliders"></i></a>
            @if($user->cans('add_tasks'))
                <a href="javascript:;" id="add-task" class="btn btn-sm btn-outline btn-info"><i class="fa fa-plus"></i> @lang('app.task')</a>
                <a href="javascript:;" id="add-column" class="btn btn-success btn-sm btn-outline"><i class="fa fa-plus"></i> @lang('modules.tasks.addBoardColumn')</a>
            @endif
            <ol class="breadcrumb">
                <li><a href="{{ route('member.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/lobipanel/dist/css/lobipanel.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/jquery-asColorPicker-master/css/asColorPicker.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/daterange-picker/daterangepicker.css') }}" />

@endpush

@section('content')

    <div class="row">
        <div class="white-box">

            <div class="row" style="display: none; " id="ticket-filters">
            
                <form action="" id="filter-form">
                    <div class="col-md-3">
                        <div class="form-group" >
                            <h5>@lang('app.selectDateRange')</h5>
                            <div id="reportrange" class="form-control reportrange">
                                <i class="fa fa-calendar"></i>&nbsp;
                                <span></span> <i class="fa fa-caret-down pull-right"></i>
                            </div>

                            <input type="hidden" class="form-control" id="start-date" placeholder="@lang('app.startDate')"
                                   value="{{ $startDate }}"/>
                            <input type="hidden" class="form-control" id="end-date" placeholder="@lang('app.endDate')"
                                   value="{{ $endDate }}"/>
                        </div>

                    </div>
                    <div class="col-md-3">
                        <h5>@lang('app.selectProject')</h5>
                        <div class="form-group">
                            <select  onchange="getMilestoneData(this.value)" class="select2 form-control" data-placeholder="@lang('app.selectProject')" id="project_id">
                                <option value="all">@lang('app.all')</option>
                                @foreach($projects as $project)
                                    <option
                                            value="{{ $project->id }}">{{ ucwords($project->project_name) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h5>@lang('app.select') @lang('app.client')</h5>

                        <div class="form-group">
                            <select class="select2 form-control" data-placeholder="@lang('app.client')" id="clientID">
                                <option value="all">@lang('app.all')</option>
                                @foreach($clients as $client)
                                    <option
                                            value="{{ $client->id }}">{{ ucwords($client->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h5>@lang('app.select') @lang('modules.tasks.assignTo')</h5>

                        <div class="form-group">
                            <select class="select2 form-control" data-placeholder="@lang('modules.tasks.assignTo')" id="assignedTo">
                                <option value="all">@lang('app.all')</option>
                                @foreach($employees as $employee)
                                    <option
                                            value="{{ $employee->id }}">{{ ucwords($employee->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h5>@lang('app.select') @lang('modules.tasks.assignBy')</h5>

                        <div class="form-group">
                            <select class="select2 form-control" data-placeholder="@lang('modules.tasks.assignBy')" id="assignedBY">
                                <option value="all">@lang('app.all')</option>
                                @foreach($employees as $employee)
                                    <option
                                            value="{{ $employee->id }}">{{ ucwords($employee->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h5 >@lang('app.select') @lang('app.milestone')</h5>
                
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <select class="select2 form-control" data-placeholder="@lang('app.milestone')" id="milestoneID">
                                        <option value="all">@lang('app.selectProject')</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group m-t-10">
                            <label class="control-label col-xs-12">&nbsp;</label>
                            <button type="button" id="apply-filters" class="btn btn-sm btn-success"><i class="fa fa-check"></i> @lang('app.apply')</button>
                            <button type="button" id="reset-filters" class="btn btn-sm btn-inverse"><i class="fa fa-refresh"></i> @lang('app.reset')</button>
                        </div>
                    </div>
                </form>
            </div>

            {!! Form::open(['id'=>'addColumn','class'=>'ajax-form','method'=>'POST']) !!}


            <div class="row" id="add-column-form" style="display: none;">
                <div class="col-xs-12">
                    <hr>
                    <div class="form-group">
                        <label class="control-label">@lang("modules.tasks.columnName")</label>
                        <input type="text" name="column_name" class="form-control">
                    </div>
                </div>
                <!--/span-->

                <div class="col-md-4">
                    <div class="form-group">
                        <label>@lang("modules.tasks.labelColor")</label><br>
                        <input type="text" class="colorpicker form-control"  name="label_color" value="#ff0000" />
                    </div>
                </div>

                <div class="col-xs-12">
                    <div class="form-group">
                        <button class="btn btn-success" id="save-form" type="submit"><i class="fa fa-check"></i> @lang('app.save')</button>
                    </div>
                </div>
                <!--/span-->

            </div>
            {!! Form::close() !!}


            {!! Form::open(['id'=>'updateColumn','class'=>'ajax-form','method'=>'POST']) !!}
            <div class="row" id="edit-column-form" style="display: none;">



            </div>
            <!--/row-->
            {!! Form::close() !!}
        </div>

    </div>

    <div class="container-scroll white-box">
        <div class ="row">
           <a href="javascript:;" id="toggle_fullscreen" class="btn btn-inverse btn-outline btn-sm pull-right"><i class="icon-size-fullscreen"></i></a>
        </div>
        <div class="row container-row">

        </div>
    <!-- .row -->
    </div>

    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="eventDetailModal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
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
        <!-- /.modal-dialog -->
    </div>
    {{--Ajax Modal Ends--}}

    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in"  id="subTaskModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md" id="modal-data-application">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <span class="caption-subject font-red-sunglo bold uppercase" id="subTaskModelHeading">Sub Task e</span>
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
<script src="{{ asset('plugins/bower_components/lobipanel/dist/js/lobipanel.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/jquery-asColorPicker-master/libs/jquery-asColor.js') }}"></script>
<script src="{{ asset('plugins/bower_components/jquery-asColorPicker-master/libs/jquery-asGradient.js') }}"></script>
<script src="{{ asset('plugins/bower_components/jquery-asColorPicker-master/dist/jquery-asColorPicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('js/jquery.slimscroll.js') }}"></script>
<script src="{{ asset('plugins/bower_components/moment/moment.js') }}"></script>
<script type="text/javascript" src="{{ asset('plugins/daterange-picker/daterangepicker.js') }}"></script>
@section('pusher-event')
    <script>
        // Subscribe to the channel we specified in our Laravel Event
        var channel = pusher.subscribe('task-updated-channel');
        channel.bind('task-updated', function(data) {
            let authId = "{{ $user->id }}";
            console.log([authId, 'authId']);
            if (data.user_id != authId) {
                loadData();
            }
        });
    </script>
@endsection
<script>
    $(function() {
        var dateformat = '{{ $global->moment_format }}';

        var startDate = '{{ $startDate }}';
        var start = moment(startDate, dateformat);

        var endDate = '{{ $endDate }}';
        var end = moment(endDate, dateformat);

        function cb(start, end) {
            $('#start-date').val(start.format(dateformat));
            $('#end-date').val(end.format(dateformat));
            $('#reportrange span').html(start.format(dateformat) + ' - ' + end.format(dateformat));
        }
        moment.locale('{{ $global->locale }}');
        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,

            locale: {
                language: '{{ $global->locale }}',
                format: '{{ $global->moment_format }}',
            },
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Last 90 Days': [moment().subtract(89, 'days'), moment()],
                'Last 6 Month': [moment().subtract(6, 'months'), moment()],
                'Last 1 Year': [moment().subtract(1, 'years'), moment()]
            }
        }, cb);

        cb(start, end);

    });
    $('#add-column').click(function () {
        $('#add-column-form').toggle();
    })
    loadData();

    // Colorpicker

    $(".colorpicker").asColorPicker();


    $('#save-form').click(function () {
        $.easyAjax({
            url: '{{route('member.taskboard.store')}}',
            container: '#addColumn',
            data: $('#addColumn').serialize(),
            type: "POST"
        })
    });

    $('#edit-column-form').on('click', '#update-form', function () {
        var id = $(this).data('column-id');
        var url = '{{route('member.taskboard.update', ':id')}}';
        url = url.replace(':id', id);

        $.easyAjax({
            url: url,
            container: '#updateColumn',
            data: $('#updateColumn').serialize(),
            type: "PUT"
        })
    });

    $('#apply-filters').click(function () {
        loadData();
    });
    $('#reset-filters').click(function () {
        $('.select2').val('all');
        $('#start-date').val('{{ $startDate }}');
        $('#end-date').val('{{ $endDate }}');
        $('#reportrange span').html('{{ $startDate }}' + ' - ' + '{{ $endDate }}');
        loadData();
    })

    $('.toggle-filter').click(function () {
        $('#ticket-filters').slideToggle();
    })

    $('#add-task').click(function () {
        var url = '{{ route('member.projects.ajaxCreate')}}';
        $('#modelHeading').html('Add Task');
        $.ajaxModal('#eventDetailModal', url);
    });
    $('#milestone').html("");
    function getMilestoneData(project_id){
            var url = "{{ route('member.taskboard.getMilestone', ':project_id') }}";
            var token = "{{ csrf_token() }}";
            $.easyAjax({
            url: url,
            type: "POST",
            data: {'_token': token, project_id: project_id},
            success: function (data) {
                var options = [];
                        var rData = [];
                        rData = data.milestones;
                        var selectData = '';
                      
                        if(rData.length == 0){
                            selectData +='<option value="all">@lang('app.selectProject')</option>';
                        }
                        else{
                            selectData +='<option value="all">@lang('app.selectMilestone')</option>';
                        }
                        $.each(rData, function( index, value ) {
                            selectData += '<option value="'+value.id+'">'+value.milestone_title+'</option>';
                        });
                        $('#milestoneID').html(selectData);
                        // $('#milestone').select2();

            }
        })
        }

    function loadData () {
        var startDate = $('#start-date').val();

        if (startDate == '') {
            startDate = null;
        }

        var endDate = $('#end-date').val();

        if (endDate == '') {
            endDate = null;
        }
        var milestoneID = $('#milestoneID').val();

        if(milestoneID == null){
            var milestoneID  = 'all';

        }else{
          var milestoneID = $('#milestoneID').val();
        }
        var projectID = $('#project_id').val();
        var clientID = $('#clientID').val();
        var assignedBY = $('#assignedBY').val();
        var assignedTo = $('#assignedTo').val();


        var url = '{{route('member.taskboard.index')}}?startDate=' + startDate + '&endDate=' + endDate+'&clientID='+clientID +'&assignedBY='+ assignedBY+'&assignedTo='+ assignedTo+'&projectID='+ projectID+'&milestoneID='+ milestoneID;

        $.easyAjax({
            url: url,
            container: '.container-row',
            type: "GET",
            success: function (response) {
                $('.container-row').html(response.view);
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            }

        })
    }
    //    update task
    function storeTask() {
        $.easyAjax({
            url: '{{route('member.all-tasks.store')}}',
            container: '#storeTask',
            type: "POST",
            data: $('#storeTask').serialize(),
            success: function (response) {
                $('#eventDetailModal').modal('hide');
                location.reload();
                loadData ()
            }
        })
    };
</script>

<script>
    $('#toggle_fullscreen').on('click', function(){
    // if already full screen; exit
    // else go fullscreen
    if (
        document.fullscreenElement ||
        document.webkitFullscreenElement ||
        document.mozFullScreenElement ||
        document.msFullscreenElement
    ) {
        if (document.exitFullscreen) {
        document.exitFullscreen();
        } else if (document.mozCancelFullScreen) {
        document.mozCancelFullScreen();
        } else if (document.webkitExitFullscreen) {
        document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) {
        document.msExitFullscreen();
        }

        $('.fullscreen-hide').show();
    } else {
        element = $('.container-scroll').get(0);
        if (element.requestFullscreen) {
        element.requestFullscreen();
        } else if (element.mozRequestFullScreen) {
        element.mozRequestFu+'&milestone='+ milestonelScreen();
        } else if (element.webkitRequestFullscreen) {
        element.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
        } else if (element.msRequestFullscreen) {
        element.msRequestFullscreen();
        }
        $('.fullscreen-hide').hide();
    }
    });
</script>

@endpush