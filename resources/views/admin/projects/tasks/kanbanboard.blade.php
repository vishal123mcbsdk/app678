@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-6 col-md-6 col-sm-4 col-xs-12 bg-title-left">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> @lang($pageTitle) #{{ $project->id }} - <span class="font-bold">{{ ucwords($project->project_name) }}</span></h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-6 col-sm-8 col-md-6 col-xs-12 text-right bg-title-right">
            <a href="javascript:;" id="createTaskCategory" class="btn btn-sm btn-outline btn-info"><i class="fa fa-plus"></i> @lang('modules.taskCategory.addTaskCategory')</a>

            <span id="filter-result" class="p-t-15 m-r-5"></span> &nbsp;
            <a href="javascript:;" id="toggle-filter" class="btn btn-sm btn-inverse btn-outline toggle-filter"><i class="fa fa-sliders"></i> @lang('app.filterResults')</a>
            <a href="{{ route('admin.projects.edit', $project->id) }}" class="btn btn-sm btn-success btn-outline" ><i class="icon-note"></i> @lang('app.edit')</a>
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('admin.projects.index') }}">@lang($pageTitle)</a></li>
                <li class="active">@lang('modules.tasks.taskBoard')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/lobipanel/dist/css/lobipanel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/jquery-asColorPicker-master/css/asColorPicker.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/daterange-picker/daterangepicker.css') }}" />

    <style>
      .lobipanel>.panel-body {
          padding: 0 !important;
      }
    </style>
@endpush

@section('content')

    <div class="row">
        <div class="col-xs-12">

            <section>
                <div class="sttabs tabs-style-line">

                    @include('admin.projects.show_project_menu')

                    <div class="content-wrap">
                        <section id="section-line-3" class="show">
                            <div class="row  white-box" style="display: none;" id="ticket-filters">
                                <input type="hidden" name="project_id" id="project_id" value="{{ $project->id }}">
                                <input type="hidden" id="clientID" value="all">

                                <form action="" id="filter-form">
                                    <div class="col-md-3">
                                        <h5>@lang('app.selectDateRange')</h5>
                                        <div class="form-group">
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
                                        <div class="form-group m-t-10">
                                            <label class="control-label col-xs-12">&nbsp;</label>
                                            <button type="button" id="apply-filters" class="btn btn-success btn-sm"><i class="fa fa-check"></i> @lang('app.apply')</button>
                                            <button type="button" id="reset-filters" class="btn btn-inverse btn-sm"><i class="fa fa-refresh"></i> @lang('app.reset')</button>
                                            <button type="button" class="btn btn-default btn-sm toggle-filter"><i class="fa fa-close"></i> @lang('app.close')</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="container-scroll white-box">
                                <button id="toggle_fullscreen" class="btn btn-default btn-outline btn-sm pull-right"><i class="icon-size-fullscreen"></i></button>
                                <button class="btn btn-default btn-outline btn-sm pull-right" id="my-tasks"><i class="fa fa-user"></i> @lang('modules.tasks.myTask')</button>
                                <button class="btn btn-default btn-outline btn-sm pull-right" id="show-all-tasks" style="display: none"><i class="fa fa-users"></i> @lang('modules.tasks.showAll')</button>

                                <div class="row container-row">
                                </div>
                            <!-- .row -->
                            </div>
                        </section>

                    </div><!-- /content -->
                </div><!-- /tabs -->
            </section>
        </div>


    </div>
    <!-- .row -->

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
                    <button type="button" class="btn default" data-dismiss="modal">@lang('app.close')</button>
                    <button type="button" class="btn blue">@lang('app.saveChanges')</button>
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

    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="taskCategoryModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
@endsection

@push('footer-script')
    <script src="{{ asset('plugins/bower_components/lobipanel/dist/js/lobipanel.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/jquery-asColorPicker-master/libs/jquery-asColor.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/jquery-asColorPicker-master/libs/jquery-asGradient.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/jquery-asColorPicker-master/dist/jquery-asColorPicker.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

    <script src="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>

    <!--slimscroll JavaScript -->
    <script src="{{ asset('js/jquery.slimscroll.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/moment/moment.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/daterange-picker/daterangepicker.js') }}"></script>
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
        $(".select2").select2({
            formatNoMatches: function () {
                return "{{ __('messages.noRecordFound') }}";
            }
        });

        loadData();

        // Colorpicker

        $(".colorpicker").asColorPicker();


        $('#save-form').click(function () {
            $.easyAjax({
                url: '{{route('admin.taskboard.store')}}',
                container: '#addColumn',
                data: $('#addColumn').serialize(),
                type: "POST"
            })
        });

        $('body').on('click', '.edit-type', function () {
            var id = $(this).data('column-id');
            var url = '{{ route("admin.taskboard.edit", ':id') }}';
            url = url.replace(':id', id);

            $('#modelHeading').html("{{  __('app.edit')." ".__('modules.lead.leadStatus') }}");
            $.ajaxModal('#eventDetailModal', url);
        })

        $('#createTaskCategory').click(function(){
            var url = '{{ route('admin.taskCategory.create-cat')}}';
            $('#modelHeading').html("@lang('modules.taskCategory.manageTaskCategory')");
            $.ajaxModal('#taskCategoryModal',url);
        });

        {{--$('#edit-column-form').on('click', '#update-form', function () {--}}
            {{--var id = $(this).data('column-id');--}}
            {{--var url = '{{route('admin.taskboard.update', ':id')}}';--}}
            {{--url = url.replace(':id', id);--}}

            {{--$.easyAjax({--}}
                {{--url: url,--}}
                {{--container: '#updateColumn',--}}
                {{--data: $('#updateColumn').serialize(),--}}
                {{--type: "PUT"--}}
            {{--})--}}
        {{--});--}}

        $('#apply-filters').click(function () {
            loadData();
        });
        $('#reset-filters').click(function () {
            $('.select2').val('all');
            $('.select2').trigger('change');

            $('#start-date').val('{{ $startDate }}');
            $('#end-date').val('{{ $endDate }}');
            $('#reportrange span').html('{{ $startDate }}' + ' - ' + '{{ $endDate }}');

            loadData();
        })

        $('.toggle-filter').click(function () {
            $('#ticket-filters').slideToggle();
        })

        function loadData () {
            var startDate = $('#start-date').val();

            if (startDate == '') {
                startDate = null;
            }

            var endDate = $('#end-date').val();

            if (endDate == '') {
                endDate = null;
            }

            var projectID = $('#project_id').val();
            var clientID = $('#clientID').val();
            var assignedBY = $('#assignedBY').val();
            var assignedTo = $('#assignedTo').val();

            var url = '{{route('admin.taskboard.index')}}?startDate=' + encodeURIComponent(startDate) + '&endDate=' + encodeURIComponent(endDate) +'&clientID='+clientID +'&assignedBY='+ assignedBY+'&assignedTo='+ assignedTo+'&projectID='+ projectID;

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
                url: '{{route('admin.all-tasks.store')}}',
                container: '#storeTask',
                type: "POST",
                data: $('#storeTask').serialize(),
                success: function (response) {
//                    if (response.taskID) {
                        window.location.reload();
//                    }
                }
            })
        };

        $('#my-tasks').click(function () {
            $('#assignedTo').val('{{$user->id}}');
            toggleFilter();
        });

        $('#show-all-tasks').click(function () {
            $('#assignedTo').val('all');
            toggleFilter();
        });

        function toggleFilter(){
            $('#assignedTo').select2().trigger('change');
            $('#show-all-tasks').toggle();
            $('#my-tasks').toggle();
            loadData()
        }
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
        } else {
            element = $('.container-scroll').get(0);
            if (element.requestFullscreen) {
            element.requestFullscreen();
            } else if (element.mozRequestFullScreen) {
            element.mozRequestFullScreen();
            } else if (element.webkitRequestFullscreen) {
            element.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
            } else if (element.msRequestFullscreen) {
            element.msRequestFullscreen();
            }
        }
        });
    </script>
    <script>

        $('ul.showProjectTabs .projectTaskBoard').addClass('tab-current');
    </script>

@endpush

@section('pusher-event')
    <script>
    // Subscribe to the channel we specified in our Laravel Event
    var channel = pusher.subscribe('task-updated-channel');
    channel.bind('task-updated', function(data) {
        let authId = "{{ $user->id }}";
        if (data.user_id != authId) {
            loadData();
        }
    });
    </script>
@endsection
