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
                <li><a href="{{ route('admin.dashboard') }}">@lang("app.menu.home")</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">

<link rel="stylesheet" href="{{ asset('plugins/bower_components/morrisjs/morris.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/daterange-picker/daterangepicker.css') }}" />

<style>
    #payments-table_wrapper .dt-buttons{
        display: none !important;
    }
</style>
@endpush

@section('content')



    @section('filter-section')
        <div class="row">
            {!! Form::open(['id'=>'filter-form','class'=>'ajax-form','method'=>'POST']) !!}
            <div class="col-xs-12">
                <div class="example">
                    <h5 class="box-title m-t-30">@lang('app.selectDateRange')</h5>
                    <div class="form-group">
                        <div id="reportrange" class="form-control reportrange">
                            <i class="fa fa-calendar"></i>&nbsp;
                            <span></span> <i class="fa fa-caret-down pull-right"></i>
                        </div>

                        <input type="hidden" class="form-control" id="start-date" placeholder="@lang('app.startDate')"
                               value="{{ $fromDate->format($global->date_format) }}"/>
                        <input type="hidden" class="form-control" id="end-date" placeholder="@lang('app.endDate')"
                               value="{{ $toDate->format($global->date_format) }}"/>
                    </div>

                </div>
            </div>


            <div class="col-xs-12">
                <h5 >@lang('app.project')</h5>
                <div class="form-group">
                    <select class="form-control select2" name="project" id="project" data-style="form-control">
                        <option value="all">@lang('modules.client.all')</option>
                        @forelse($projects as $project)
                            <option value="{{$project->id}}">{{ $project->project_name }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
            </div>
            <div class="col-xs-12">
                <div class="form-group">
                    <h5 >@lang('app.client')</h5>
                    <select class="form-control select2" name="client" id="client" data-style="form-control">
                        <option value="all">@lang('modules.client.all')</option>
                        @forelse($clients as $client)
                            <option
                            value="{{ $client->id }}">{{ ucwords($client->name) }}{{ ($client->company_name != '') ? " [".$client->company_name."]" : "" }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
            </div>

            <div class="col-md-12 m-t-20">
                <div class="form-group">
                    <button type="button" class="btn btn-success col-md-6" id="filter-results"><i class="fa fa-check"></i> @lang('app.apply') </button>
                    <button type="button" id="reset-filters" class="btn btn-inverse col-md-5 col-md-offset-1"><i class="fa fa-refresh"></i> @lang('app.reset')</button>
                </div>
            </div>
            {!! Form::close() !!}

        </div>
    @endsection


    <div class="row">
        <div class="col-lg-12">
            <div class="white-box">
                <h3 class="box-title">@lang('modules.financeReport.chartTitle')</h3>
                <div id="morris-bar-chart"></div>
                <h6><span class="text-danger">@lang('app.note'):</span> @lang('modules.financeReport.noteText')
            </div>
        </div>

    </div>

    <div class="row  m-t-30">
        <div class="white-box">
            <div class="table-responsive">
                {!! $dataTable->table(['class' => 'table table-bordered table-hover toggle-circle default footable-loaded footable']) !!}
            </div>
        </div>
    </div>

@endsection

@push('footer-script')


<script src="{{ asset('plugins/bower_components/raphael/raphael-min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/morrisjs/morris.js') }}"></script>

<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>

<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('js/datatables/buttons.server-side.js') }}"></script>
<script src="{{ asset('plugins/bower_components/moment/moment.js') }}"></script>
<script type="text/javascript" src="{{ asset('plugins/daterange-picker/daterangepicker.js') }}"></script>
{!! $dataTable->scripts() !!}

<script>
    $(function() {
        var dateformat = '{{ $global->moment_format }}';

        var startDate = '{{ $fromDate->format($global->date_format) }}';
        var start = moment(startDate, dateformat);

        var endDate = '{{ $toDate->format($global->date_format) }}';
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
            linkedCalendars: false,
            ranges: dateRangePickerCustom
        }, cb);

        cb(start, end);

    });

    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    $('#payments-table').on('preXhr.dt', function (e, settings, data) {
        var startDate = $('#start-date').val();

        if (startDate == '') {
            startDate = null;
        }

        var endDate = $('#end-date').val();

        if (endDate == '') {
            endDate = null;
        }

        var status = $('#status').val();
        var project = $('#project').val();
        var client = $('#client').val();

        data['startDate'] = startDate;
        data['endDate'] = endDate;
        data['status'] = status;
        data['project'] = project;
        data['client'] = client;
    });

    $('#filter-results').click(function () {
        var token = '{{ csrf_token() }}';
        var url = '{{ route('admin.finance-report.store') }}';

        var startDate = $('#start-date').val();

        if (startDate == '') {
            startDate = null;
        }

        var endDate = $('#end-date').val();

        if (endDate == '') {
            endDate = null;
        }

        var currencyId = $('#currency_id').val();

        var project = $('#project').val();
        var client = $('#client').val();

        $.easyAjax({
            type: 'POST',
            url: url,
            data: {_token: token, startDate: startDate, endDate: endDate, currencyId: currencyId, project: project, client: client},
            success: function (response) {
                if(response.status == 'success'){
                    chartData = $.parseJSON(response.chartData);
                    $('#morris-bar-chart').html('');
                    barChart();
                    window.LaravelDataTables["payments-table"].draw();
                }
            }
        });
    })
    $('#reset-filters').click(function () {
        $('#filter-form')[0].reset();
        $('#status').val('all');
        $('.select2').val('all');
        $('#filter-form').find('select').select2();
        $('#start-date').val('{{ $fromDate->format($global->date_format) }}');
        $('#end-date').val('{{ $toDate->format($global->date_format) }}');
        $('#reportrange span').html('{{ $fromDate->format($global->date_format) }}' + ' - ' + '{{ $toDate->format($global->date_format) }}');
        $('#filter-results').trigger("click");
    })

    function loadTable(){
        window.LaravelDataTables["payments-table"].draw();
    }

</script>

<script>
    var chartData = {!!  $chartData !!};
    function barChart() {

        Morris.Bar({
            element: 'morris-bar-chart',
            data: chartData,
            xkey: 'date',
            ykeys: ['total'],
            labels: ['Earning'],
            barColors:['#00c292'],
            hideHover: 'auto',
            gridLineColor: '#eef0f2',
            resize: true
        });

    }

    barChart();
    $(document).ready(function(){
        loadTable();
    });

</script>
@endpush
