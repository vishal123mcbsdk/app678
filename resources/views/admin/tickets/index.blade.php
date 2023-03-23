@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 bg-title-left">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12 text-right bg-title-right">
            <a href="{{ route('admin.ticket-agents.index') }}" class="btn btn-sm btn-inverse btn-outline"><i class="fa fa-gear"></i> @lang('app.menu.ticketSettings')</a>
            <a href="{{ route('admin.tickets.create') }}"
                               class="btn btn-success btn-outline btn-sm">@lang('modules.tickets.addTicket') <i class="fa fa-plus"
                                                                                                 aria-hidden="true"></i></a>
            <a href="{{ route('admin.ticket-form.index') }}" class="btn btn-outline btn-inverse btn-sm">@lang('app.ticketForm') <i class="fa fa-pencil"  aria-hidden="true"></i></a>

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/morrisjs/morris.css') }}">

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/daterange-picker/daterangepicker.css') }}" />
<style>
    .m-t-23
    {
        margin-top: 23px;
    }
</style>
@endpush



@section('filter-section')
<div class="row" id="ticket-filters">

    <form action="" id="filter-form">
        <div class="col-xs-12">
            <div class="form-group">
            <h5 >@lang('app.selectDateRange')</h5>

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
        <div class="col-xs-12">
            <div class="form-group">
                <label class="control-label">@lang('modules.tickets.agent')</label>
                <select class="form-control selectpicker" name="agent_id" id="agent_id" data-style="form-control">
                    <option value="all">@lang('app.all')</option>
                    @forelse($groups as $group)
                        @if(count($group->enabled_agents) > 0)
                            <optgroup label="{{ ucwords($group->group_name) }}">
                                @foreach($group->enabled_agents as $agent)
                                    <option value="{{ $agent->user->id }}">{{ ucwords($agent->user->name).' ['.$agent->user->email.']' }}</option>
                                @endforeach
                            </optgroup>
                        @endif
                    @empty
                        <option value="">@lang('messages.noGroupAdded')</option>
                    @endforelse
                </select>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                <label class="control-label">@lang('app.status')</label>
                <select class="form-control selectpicker" name="status" id="status" data-style="form-control">
                    <option value="all">@lang('app.all')</option>
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
                    <option value="all">@lang('app.all')</option>
                    <option value="low">@lang('modules.tasks.low')</option>
                    <option value="medium">@lang('modules.tasks.medium')</option>
                    <option value="high">@lang('modules.tasks.high')</option>
                    <option value="urgent">@lang('modules.tickets.urgent')</option>
                </select>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                <label class="control-label">@lang('modules.tickets.channelName')</label>
                <select class="form-control selectpicker" name="channel_id" id="channel_id" data-style="form-control">
                    <option value="all">@lang('app.all')</option>
                    @forelse($channels as $channel)
                        <option value="{{ $channel->id }}">{{ ucwords($channel->channel_name) }}</option>
                    @empty
                        <option value="">@lang('messages.noTicketChannelAdded')</option>
                    @endforelse
                </select>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                <label class="control-label">@lang('modules.invoices.type')</label>
                <select class="form-control selectpicker" name="type_id" id="type_id" data-style="form-control">
                    <option value="all">@lang('app.all')</option>
                    @forelse($types as $type)
                        <option value="{{ $type->id }}">{{ ucwords($type->type) }}</option>
                    @empty
                        <option value="">@lang('messages.noTicketTypeAdded')</option>
                    @endforelse
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
   
        {{--<div class="col-xs-12">--}}
            {{--<div class="white-box p-b-0 m-b-0">--}}
                {{--<div class="row">--}}
                    {{--<div class="col-sm-4">--}}
                        {{--<label class="control-label">@lang('app.selectDateRange')</label>--}}

                        {{--<div class="form-group">--}}
                            {{--<input class="form-control input-daterange-datepicker" type="text" name="daterange"--}}
                                   {{--value="{{ $startDate.' - '.$endDate }}"/>--}}
                        {{--</div>--}}
                    {{--</div>--}}

                {{--</div>--}}

            {{--</div>--}}
        {{--</div>--}}

        <div class="col-md-12">
            <div class="white-box">
                <ul class="nav customtab nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#home1" aria-controls="home" role="tab" data-toggle="tab" aria-expanded="true"><i class="ti-ticket"></i> @lang('app.menu.tickets')</a></li>
                    <li role="presentation" class=""><a href="#profile1" aria-controls="profile" role="tab" data-toggle="tab" aria-expanded="false"><i class="icon-graph"></i>  @lang('modules.tickets.ticketTrendGraph')</a></li>
                </ul>
            </div>
        </div>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade active in" id="home1">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row dashboard-stats m-b-20">
                                <div class="col-md-12">
                                    <div class="white-box">
                                        <div class="col-md-4 col-sm-6">
                                            <h4>
                                                <span class="text-dark" id="totalTickets">0</span> <span class="font-12 text-muted m-l-5"> @lang('modules.tickets.totalTickets')</span>
                                                <a class="mytooltip font-12" href="javascript:void(0)"> <i class="fa fa-info-circle"></i><span class="tooltip-content5"><span class="tooltip-text3"><span class="tooltip-inner2">@lang('modules.tickets.totalTicketInfo')</span></span></span></a>
                                            </h4>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <h4>
                                                <span class="text-success" id="closedTickets">0</span> <span class="font-12 text-muted m-l-5"> @lang('modules.tickets.totalClosedTickets')</span>
                                                <a class="mytooltip font-12" href="javascript:void(0)"> <i class="fa fa-info-circle"></i><span class="tooltip-content5"><span class="tooltip-text3"><span class="tooltip-inner2">@lang('modules.tickets.closedTicketInfo')</span></span></span></a>
                                            </h4>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <h4>
                                                <span class="text-danger" id="openTickets">0</span> <span class="font-12 text-muted m-l-5"> @lang('modules.tickets.totalOpenTickets')</span>
                                                <a class="mytooltip font-12" href="javascript:void(0)"> <i class="fa fa-info-circle"></i><span class="tooltip-content5"><span class="tooltip-text3"><span class="tooltip-inner2">@lang('modules.tickets.openTicketInfo')</span></span></span></a>
                                            </h4>
                                        </div>
        
                                        <div class="col-md-4 col-sm-6">
                                            <h4>
                                                <span class="text-warning" id="pendingTickets">0</span> <span class="font-12 text-muted m-l-5"> @lang('modules.tickets.totalPendingTickets')</span>
                                                <a class="mytooltip font-12" href="javascript:void(0)"> <i class="fa fa-info-circle"></i><span class="tooltip-content5"><span class="tooltip-text3"><span class="tooltip-inner2">@lang('modules.tickets.pendingTicketInfo')</span></span></span></a>
                                            </h4>
                                        </div>
        
                                        <div class="col-md-4 col-sm-6">
                                            <h4>
                                                <span class="text-info" id="resolvedTickets">0</span> <span class="font-12 text-muted m-l-5"> @lang('modules.tickets.totalResolvedTickets')</span>
                                                <a class="mytooltip font-12" href="javascript:void(0)"> <i class="fa fa-info-circle"></i><span class="tooltip-content5"><span class="tooltip-text3"><span class="tooltip-inner2">@lang('modules.tickets.resolvedTicketInfo')</span></span></span></a>
                                            </h4>
                                        </div>
        
                                    </div>
                                </div>
        
                            </div>


                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="white-box">
                                        <div class="table-responsive">
                                            {!! $dataTable->table(['class' => 'table table-bordered table-hover toggle-circle default footable-loaded footable']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 status-field">
                                <div class=" col-md-2">
                                    <label class="control-label">@lang('app.change') @lang('app.status')</label>
                                    <select class="form-control selectpicker" name="status" id="ticket_status" data-style="form-control">
                                        <option selected value="open">@lang('modules.tickets.totalOpenTickets')</option>
                                        <option value="pending">@lang('modules.tickets.totalPendingTickets')</option>
                                        <option value="resolved">@lang('modules.tickets.totalResolvedTickets')</option>
                                        <option value="closed">@lang('modules.tickets.totalClosedTickets')</option>
                                    </select>
                                </div>
                                <div class="form-actions col-md-2">
                                    <label class="control-label"></label>
                                    <button type="submit" id="update-status" class="btn btn-sm btn-success update-status m-t-23"><i class="fa fa-check"></i>
                                        @lang('app.apply')</button>
        
                                </div>
                            </div>
                            
        
                        </div>
                        
        
                    </div>
        
                </div>
            </div>

            <div role="tabpanel" class="tab-pane" id="profile1">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="white-box p-t-10 p-b-10">
                                <ul class="list-inline text-right">
                                    <li>
                                        <h5><i class="fa fa-circle m-r-5" style="color: #4c5667;"></i>@lang('modules.invoices.total')</h5>
                                    </li>
                                    <li>
                                        <h5><i class="fa fa-circle m-r-5" style="color: #5475ed;"></i>@lang('app.resolved')</h5>
                                    </li>
                                    <li>
                                        <h5><i class="fa fa-circle m-r-5" style="color: #12ed0b;"></i>@lang('app.closed')</h5>
                                    </li>
                                    <li>
                                        <h5><i class="fa fa-circle m-r-5" style="color: #f11219;"></i>@lang('app.open')</h5>
                                    </li>
                                    <li>
                                        <h5><i class="fa fa-circle m-r-5" style="color: #f1c411;"></i>@lang('app.pending')</h5>
                                    </li>
                                </ul>
                                <div id="morris-area-chart" style="height: 225px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- .row -->

@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/moment/moment.js') }}"></script>

<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>

<script src="{{ asset('plugins/bower_components/raphael/raphael-min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/morrisjs/morris.js') }}"></script>

<script src="{{ asset('plugins/bower_components/waypoints/lib/jquery.waypoints.js') }}"></script>
<script src="{{ asset('plugins/bower_components/counterup/jquery.counterup.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('js/datatables/buttons.server-side.js') }}"></script>
<script src="{{ asset('plugins/bower_components/moment/moment.js') }}"></script>
<script type="text/javascript" src="{{ asset('plugins/daterange-picker/daterangepicker.js') }}"></script>

{!! $dataTable->scripts() !!}
<script>

    $(function() {
        var dateformat = '{{ $global->moment_format }}';
        // $('#update-status').disabled();
        $("#update-status").attr("disabled", true);
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

    $('.toggle-filter').click(function () {
        $('#ticket-filters').toggle('slide');
    })

    var total = '@lang('app.total')';
    var resolved = '@lang('app.resolved')';
    var closed = '@lang('app.closed')';
    var open = '@lang('app.open')';
    var pending = '@lang('app.pending')';

    function ticketChart(chartData){
        Morris.Area({
            element: 'morris-area-chart',
            data: chartData,
            xkey: 'date',
            ykeys: [total.toLowerCase(), resolved.toLowerCase(), 'closed', open.toLowerCase(), pending.toLowerCase()],
            labels: [total, resolved, 'Closed', open, pending],
            pointSize: 3,
            fillOpacity: 0.3,
            pointStrokeColors: ['#4c5667', '#5475ed', '#12ed0b', '#f11219' , '#f1c411'],
            behaveLikeLine: true,
            gridLineColor: '#e0e0e0',
            lineWidth: 3,
            hideHover: 'auto',
            lineColors: ['#4c5667', '#5475ed', '#12ed0b', '#f11219' , '#f1c411'],
            resize: true

        });
    }

    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        var target = $(e.target).attr("href") // activated tab

        switch (target) {
            case "#home1":
            $(window).trigger('resize');
            break;
            case "#profile1":
            $(window).trigger('resize');
            break;
        }
    });

    $('#ticket-table').on('preXhr.dt', function (e, settings, data) {

        var startDate = $('#start-date').val();

        if (startDate == '') {
            startDate = null;
        }

        var endDate = $('#end-date').val();

        if (endDate == '') {
            endDate = null;
        }

        var agentId = $('#agent_id').val();
        if (agentId == "") {
            agentId = 0;
        }

        var status = $('#status').val();
        if (status == "") {
            status = 0;
        }

        var priority = $('#priority').val();
        if (priority == "") {
            priority = 0;
        }

        var channelId = $('#channel_id').val();
        if (channelId == "") {
            channelId = 0;
        }

        var typeId = $('#type_id').val();
        if (typeId == "") {
            typeId = 0;
        }

        var tagId = $('#tag_id').val();
        if (tagId == "") {
            tagId = 0;
        }

        data['startDate'] = startDate;
        data['endDate'] = endDate;
        data['agentId'] = agentId;
        data['priority'] = priority;
        data['channelId'] = channelId;
        data['typeId'] = typeId;
        data['tagId'] = tagId;
        data['status'] = status;
    });

    var table;

    function showTable() {

        var startDate = $('#start-date').val();

        if (startDate == '') {
            startDate = 0;
        }

        var endDate = $('#end-date').val();

        if (endDate == '') {
            endDate = 0;
        }

        var agentId = $('#agent_id').val();
        if (agentId == "") {
            agentId = 0;
        }

        var status = $('#status').val();
        if (status == "") {
            status = 0;
        }

        var priority = $('#priority').val();
        if (priority == "") {
            priority = 0;
        }

        var channelId = $('#channel_id').val();
        if (channelId == "") {
            channelId = 0;
        }

        var typeId = $('#type_id').val();
        if (typeId == "") {
            typeId = 0;
        }

        //refresh counts and chart
        var url = '{!!  route('admin.tickets.refreshCount') !!}';
        var token = '{{ csrf_token() }}';
        $.easyAjax({
            type: 'POST',
            url: url,
            data: {'startDate': startDate, 'endDate':endDate,'agentId':agentId,'status':status,'priority':priority,'channelId':channelId,'typeId':typeId,'_token':token },
            success: function (response) {
                $('#totalTickets').html(response.totalTickets);
                $('#closedTickets').html(response.closedTickets);
                $('#openTickets').html(response.openTickets);
                $('#pendingTickets').html(response.pendingTickets);
                $('#resolvedTickets').html(response.resolvedTickets);
                initConter();
                $('#morris-area-chart').empty();
                ticketChart(JSON.parse(response.chartData));
                if(response.totalTickets == 0){
                     $('.status-field').hide();
                 }else{
                    $('.status-field').show();
                 }
                $("#update-status").attr("disabled", true);
            }
        });

        window.LaravelDataTables["ticket-table"].draw();
    }
    $('.update-status').click(function () {
             var ids = [];
             swal({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.confirmation.updateStatus')",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "@lang('messages.confirmation.updateConfirm')",
            cancelButtonText: "@lang('messages.confirmNoArchive')",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {

             var status = $( "#ticket_status option:selected" ).val();
            $.each($("input[name='id']:checked"), function(){
                ids.push($(this).val());
            });
            var url = "{!! route('admin.tickets.updateStatus') !!}";
            var token = '{{ csrf_token() }}';
            $.easyAjax({
                type: "POST",
                url: url,
                data: {'_token': token, 'ids':ids ,'status':status},
                success: function (response) {
                    if (response.status == "success") {
                            
                            $('#totalTickets').html(response.data.totalTickets);
                            $('#closedTickets').html(response.data.closed);
                            $('#openTickets').html(response.data.open);
                            $('#pendingTickets').html(response.data.pending);
                            $('#resolvedTickets').html(response.data.resolved);
                            if(response.data.open == 0){
                                $('.status-field').hide();
                            }
                            $('.bulk_status').attr('checked', false);
                            $.unblockUI();
                            window.LaravelDataTables["ticket-table"].draw();
                        }
                        
                    }
            })
        }
        });
    });

    $('body').on('click', '.bulk_status', function () {// bulk checked
		var status = this.checked;
        $('#ticket-table tr:has(td)').find('input[type="checkbox"]').prop('checked', status);

        if ($(this).prop("checked")) {
            // checked
            $("#update-status").attr("disabled", false);
        }
        else{
            $("#update-status").attr("disabled", true);
        }
	});

    $('body').on('click', '.ticket-checkbox', function () {// bulk checked
		var status = this.checked;
        if ($(this).prop("checked")) {
            // checked
            $("#update-status").attr("disabled", false);
        }
        else{
            var idsCheck = [];
            var statusNew = $( "#ticket_status option:selected" ).val();
            $.each($("input[name='id']:checked"), function(){
                idsCheck.push($(this).val());
            });

            if(idsCheck.length === 0)
            {
                $("#update-status").attr("disabled", true);
            }
            else{
                $("#update-status").attr("disabled", false);
            }
        }
	});

    $('#apply-filters').click(function () {
        showTable();
    });

    $('#reset-filters').click(function () {
        $('#filter-form')[0].reset();
        $('#filter-form').find('select').selectpicker('render');
        $('#reportrange span').html('');
        
        showTable();
    })


    $('body').on('click', '.sa-params', function () {
        var id = $(this).data('ticket-id');
        swal({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.confirmation.recoverDeleteTicket')",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "@lang('messages.deleteConfirmation')",
            cancelButtonText: "@lang('messages.confirmNoArchive')",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {

                var url = "{{ route('admin.tickets.destroy',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                    success: function (response) {
                        if (response.status == "success") {
                            $.unblockUI();
                            window.LaravelDataTables["ticket-table"].draw();
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
   
    function initConter() {
        $(".counter").counterUp({
            delay: 100,
            time: 1200
        });
    }

    $(document).ready(function(){
        showTable();
    });


    function exportData(){

        var startDate = $('#start-date').val();

        if (startDate == '') {
            startDate = 0;
        }

        var endDate = $('#end-date').val();

        if (endDate == '') {
            endDate = 0;
        }

        var agentId = $('#agent_id').val();
        if (agentId == "") {
            agentId = 0;
        }

        var status = $('#status').val();
        if (status == "") {
            status = 0;
        }

        var priority = $('#priority').val();
        if (priority == "") {
            priority = 0;
        }

        var channelId = $('#channel_id').val();
        if (channelId == "") {
            channelId = 0;
        }

        var typeId = $('#type_id').val();
        if (typeId == "") {
            typeId = 0;
        }


        //refresh counts and chart
        var url = '{!!  route('admin.tickets.export', [':startDate', ':endDate', ':agentId', ':status', ':priority', ':channelId', ':typeId']) !!}';
        url = url.replace(':startDate', startDate);
        url = url.replace(':endDate', endDate);
        url = url.replace(':agentId', agentId);
        url = url.replace(':status', status);
        url = url.replace(':priority', priority);
        url = url.replace(':channelId', channelId);
        url = url.replace(':typeId', typeId);

        window.location.href = url;
    }
    
</script>
@endpush