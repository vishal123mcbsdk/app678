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
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/daterange-picker/daterangepicker.css') }}" />

@endpush
@section('filter-section')
    <div class="row" id="ticket-filters">
        <form action="" id="filter-form">
            <div class="col-xs-12">
                <div class="form-group">
                    <h5 >@lang('app.selectDateRange')</h5>
                    <div class="form-group">
                        <div id="reportrange" class="form-control reportrange">
                            <i class="fa fa-calendar"></i>&nbsp;
                            <span></span> <i class="fa fa-caret-down pull-right"></i>
                        </div>

                        <input type="hidden" class="form-control" id="start-date" placeholder="@lang('app.startDate')"
                               value=""/>
                        <input type="hidden" class="form-control" id="end-date" placeholder="@lang('app.endDate')"
                               value=""/>
                    </div>
                </div>
            </div>
            <div class="col-xs-12">
                <div class="form-group">
                    <label class="control-label">@lang('app.status')</label>
                    <select class="form-control selectpicker" name="status" id="status" data-style="form-control">
                        <option value="all">@lang('modules.tickets.nofilter')</option>
                        <option selected value="open">@lang('modules.tickets.totalOpenTickets')</option>
                        <option value="pending">@lang('modules.tickets.totalPendingTickets')</option>
                        <option value="resolved">@lang('modules.tickets.totalResolvedTickets')</option>
                        <option value="closed">@lang('modules.tickets.totalClosedTickets')</option>
                    </select>
                </div>
            </div>
            <div class="col-xs-12">
                <div class="form-group">
                    <label class="control-label">@lang('modules.tasks.priority')</label>
                    <select class="form-control selectpicker" name="priority" id="priority" data-style="form-control">
                        <option value="all">@lang('modules.tickets.nofilter')</option>
                        <option value="low">@lang('modules.tasks.low')</option>
                        <option value="medium">@lang('modules.tasks.medium')</option>
                        <option value="high">@lang('modules.tasks.high')</option>
                        <option value="urgent">@lang('modules.tickets.urgent')</option>
                    </select>
                </div>
            </div>
            <div class="col-xs-12">
                <div class="form-group">
                    <label class="control-label col-xs-12">&nbsp;</label>
                    <button type="button" id="apply-filters" class="btn btn-success col-md-6"><i class="fa fa-check"></i> @lang('app.apply')</button>
                    <button type="button" id="reset-filters" class="btn btn-inverse col-md-5 col-md-offset-1"><i class="fa fa-refresh"></i> @lang('app.reset')</button>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('content')

    <div class="row">
        <div class="col-xs-12">
            <div class="white-box">

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <a href="{{ route('member.tickets.create') }}"
                               class="btn btn-info btn-sm">@lang('modules.tickets.requestTicket') <i class="fa fa-plus"
                                                                                                 aria-hidden="true"></i></a>
                            @if($isAgent)
                                <a href="{{ route('member.ticket-agent.index') }}"
                               class="btn btn-inverse btn-sm">@lang('modules.tickets.goToAgentDashboard') <i class="fa fa-arrow-right"
                                                                                                 aria-hidden="true"></i></a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover toggle-circle default footable-loaded footable" id="tickets-table">
                        <thead>
                        <tr>
                            <th>@lang('modules.tickets.ticket') #</th>
                            <th>@lang('modules.tickets.ticketSubject')</th>
                            <th>@lang('modules.tickets.agent')</th>
                            <th>@lang('modules.tickets.requestedOn')</th>
                            <th>@lang('modules.sticky.lastUpdated')</th>
                            <th>@lang('app.status')</th>
                            <th>@lang('app.action')</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- .row -->

@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/moment/moment.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
<script src="{{ asset('plugins/bower_components/moment/moment.js') }}"></script>
<script type="text/javascript" src="{{ asset('plugins/daterange-picker/daterangepicker.js') }}"></script>

<script>
    $(function() {
        var dateformat = '{{ $global->moment_format }}';

        var start = '';
        var end = '';

        function cb(start, end) {
            if(start){
                $('#start-date').val(start.format(dateformat));
                $('#end-date').val(end.format(dateformat));
                $('#reportrange span').html(start.format(dateformat) + ' - ' + end.format(dateformat));
            }

        }
        moment.locale('{{ $global->locale }}');
        $('#reportrange').daterangepicker({
            // startDate: start,
            // endDate: end,
            locale: {
                language: '{{ $global->locale }}',
                format: '{{ $global->moment_format }}',
            },
            linkedCalendars: false,
            ranges: dateRangePickerCustom
        }, cb);

        cb(start, end);

    });
    showTable();

    $('.toggle-filter').click(function () {
        $('#ticket-filters').toggle('slide');
    })
    var table;

    $('#apply-filters').click(function () {
        showTable();
    });

    $('#reset-filters').click(function () {
        $('#filter-form')[0].reset();
        $('#filter-form').find('select').selectpicker('render');
        $('#start-date').val('');
        $('#end-date').val('');
        $('#reportrange span').html('');
        showTable();
    })
    function showTable() {

        var startDate = $('#start-date').val();

        if (startDate == '') {
            startDate = null;
        }

        var endDate = $('#end-date').val();

        if (endDate == '') {
            endDate = null;
        }

        var status = $('#status').val();
        if (status == "") {
            status = 0;
        }

        var priority = $('#priority').val();
        if (priority == "") {
            priority = 0;
        }

        table = $('#tickets-table').dataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            destroy: true,
            ajax: {
                "url": '{!! route('member.tickets.data') !!}',
                "type": "POST",
                data:function (d) {d.startDate = startDate,
                    d.endDate = endDate,
                    d.status = status,
                    d.priority = priority,
                    d._token = '{{ csrf_token() }}' },

            },
            deferRender: true,
            language: {
                "url": "<?php echo __("app.datatable") ?>"
            },
            "fnDrawCallback": function( oSettings ) {
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            },
            "order": [[0, "desc"]],
            columns: [
                {data: 'id', name: 'id'},
                {data: 'subject', name: 'subject', width: '25%'},
                {data: 'agent_id', name: 'agent_id', width: '20%'},
                {data: 'created_at', name: 'created_at'},
                {data: 'updated_at', name: 'updated_at'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', "searchable": false}
            ]
        });
    }

    $(function() {
        $('body').on('click', '.sa-params', function(){
            var id = $(this).data('user-id');
            swal({
                title: "@lang('messages.sweetAlertTitle')",
                text: "@lang('messages.confirmation.recoverProjectTemplate')",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('messages.deleteConfirmation')",
                cancelButtonText: "@lang('messages.confirmNoArchive')",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                    var url = "{{ route('member.projects.destroy',':id') }}";
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
                                $('#totalTickets').html(response.data.totalTickets);
                                $('#closedTickets').html(response.data.closed);
                                $('#openTickets').html(response.data.open);
                                $('#pendingTickets').html(response.data.pending);
                                $('#resolvedTickets').html(response.data.resolved);
                            }
                        }
                    });
                }
            });
        });

    });

</script>
@endpush