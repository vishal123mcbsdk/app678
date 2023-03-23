@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 bg-title-left">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> @lang($pageTitle)</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12 text-right bg-title-right">

{{--            <span id="filter-result" class="p-t-15 m-r-5"></span> &nbsp;--}}
            <span>
                <div id="reportrange" class="form-control reportrange m-r-5" >
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span></span> <i class="fa fa-caret-down pull-right"></i>
                </div>

                <input type="hidden" class="form-control" id="start-date" placeholder="@lang('app.startDate')"
                       value="{{ $startDate }}"/>
                <input type="hidden" class="form-control" id="end-date" placeholder="@lang('app.endDate')"
                       value="{{ $endDate }}"/>
            </span>
            <a href="javascript:;" id="toggle-filter" class="btn btn-sm btn-inverse btn-outline toggle-filter"><i class="fa fa-sliders"></i> @lang('app.filterResults')</a>
            <a href="{{ route('admin.leads.create') }}"
               class="btn btn-outline btn-info btn-sm">@lang('modules.lead.addNewLead') <i class="fa fa-plus" aria-hidden="true"></i></a>
            <a href="javascript:;" id="create-status" class="btn btn-success btn-outline btn-sm"><i class="fa fa-plus"></i> @lang('modules.tasks.addBoardColumn')</a>
            <a href="{{ route('admin.leads.index') }}" class="btn btn-outline btn-primary btn-sm">@lang('modules.leaves.tableView') </a>
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">@lang($pageTitle)</li>
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
    .reportrange {
        display: table-cell; padding-bottom: 8px !important;
    }
</style>
@endpush

@section('content')

    <div class="row">
        <div class="white-box">


            <div class="row" style="display: none;" id="ticket-filters">


                <form action="" id="filter-form">
                    <div class="col-md-3">
                        <h5>@lang('app.selectDateRange')</h5>
                        <div class="form-group">
                            <div id="reportrange" class="form-control reportrange">
                                <i class="fa fa-calendar"></i>&nbsp;
                                <span></span> <i class="fa fa-caret-down pull-right"></i>
                            </div>

                            <input type="hidden" class="form-control" id="start-date" placeholder="@lang('app.startDate')"
                                   value="{{ \Carbon\Carbon::now()->timezone($global->timezone)->subDays(6)->format($global->date_format) }}"/>
                            <input type="hidden" class="form-control" id="end-date" placeholder="@lang('app.endDate')"
                                   value="{{ \Carbon\Carbon::now()->timezone($global->timezone)->addDays(7)->format($global->date_format) }}"/>
                        </div>
                    </div>
                  
                    <div class="col-md-3">
                        <h5>@lang('modules.tickets.chooseAgents')</h5>

                        <div class="form-group">
                            <select class="select2 form-control" id="assignedTo">
                                <option value="all">@lang('app.all')</option>
                                @foreach($leadAgents as $emp)
                                    <option value="{{ $emp->id }}">{{ ucwords($emp->user->name) }} @if($emp->user->id == $user->id)
                                            (YOU) @endif</option>
                                @endforeach
                            </select>
                        </div>
                      
                    </div>
                   
                    <div class="col-md-4">
                        <div class="form-group m-t-10">
                            <label class="control-label col-xs-12">&nbsp;</label>
                            <button type="button" id="apply-filters" class="btn btn-success btn-sm"><i class="fa fa-check"></i> @lang('app.apply')</button>
                            <button type="button" id="reset-filters" class="btn btn-inverse btn-sm"><i class="fa fa-refresh"></i> @lang('app.reset')</button>
                            <button type="button" class="btn btn-default btn-sm toggle-filter"><i class="fa fa-close"></i> @lang('app.close')</button>
                        </div>
                    </div>
                </form>
            </div>

            {!! Form::open(['id'=>'addColumn','class'=>'ajax-form','method'=>'POST']) !!}


            <div class="row" id="add-column-form" style="display: none;">
                <div class="col-xs-12">
                    <hr>
                    <div class="form-group">
                        <label class="control-label required">@lang("modules.tasks.columnName")</label>
                        <input type="text" name="column_name" class="form-control">
                    </div>
                </div>
                <!--/span-->

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="required">@lang("modules.tasks.labelColor")</label><br>
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
        <button id="toggle_fullscreen" class="btn btn-default btn-outline btn-sm pull-right visible-lg"><i class="icon-size-fullscreen"></i></button>
        

        <div class="row container-row">
        </div>
    <!-- .row -->
    </div>


    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="leadStatusModal" role="dialog" aria-labelledby="myModalLabel"
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
    <script src="{{ asset('plugins/bower_components/lobipanel/dist/js/lobipanel.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/jquery-asColorPicker-master/libs/jquery-asColor.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/jquery-asColorPicker-master/libs/jquery-asGradient.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/jquery-asColorPicker-master/dist/jquery-asColorPicker.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

    <script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>

    <!--slimscroll JavaScript -->
    <script src="{{ asset('js/jquery.slimscroll.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/moment/moment.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/daterange-picker/daterangepicker.js') }}"></script>
    <script>
        $(function() {
            var dateformat = '{{ $global->moment_format }}';

            var startDate = '{{ \Carbon\Carbon::now()->timezone($global->timezone)->subDays(6)->format($global->date_format) }}';
            var start = moment(startDate, dateformat);

            var endDate = '{{ \Carbon\Carbon::now()->timezone($global->timezone)->addDays(7)->format($global->date_format) }}';
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
            $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
                loadData();
            });
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

            var assignedTo = $('#assignedTo').val();

            var url = '{{route('admin.leads.kanbanboard')}}?startDate=' + encodeURIComponent(startDate) + '&endDate=' + encodeURIComponent(endDate) +'&assignedTo='+ assignedTo;

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

        $('body').on('click', '.edit-type', function () {
            var typeId = $(this).data('type-id');
            var url = '{{ route("admin.lead-status-settings.edit", ":id")}}';
            url = url.replace(':id', typeId);

            $('#modelHeading').html("{{  __('app.edit')." ".__('modules.lead.leadStatus') }}");
            $.ajaxModal('#leadStatusModal', url);
        })


        $('#create-status').click(function () {
            var url = '{{ route("admin.leadSetting.createModal")}}';

            $('#modelHeading').html("{{  __('app.add')." ".__('modules.lead.leadStatus') }}");
            $.ajaxModal('#leadStatusModal', url);
        })

       

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
                $('.fullscreen-hide').show();
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
            $('.fullscreen-hide').hide();
        }
        });
    </script>

@endpush
