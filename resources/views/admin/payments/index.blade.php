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
            <a href="{{ route('admin.payments.create') }}" class="btn btn-outline btn-success btn-sm">@lang('modules.payments.addPayment') <i class="fa fa-plus" aria-hidden="true"></i></a>

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/daterange-picker/daterangepicker.css') }}" />

@endpush

@section('content')

    <div class="row">
        <div class="col-xs-12">
            <div class="white-box">

                @section('filter-section')
                <div class="row" id="ticket-filters">

                    <form action="" id="filter-form">
                        <div class="col-xs-12">
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
                        <div class="col-xs-12">
                            <h5 >@lang('app.status')</h5>
                            <div class="form-group">
                                {{--<label class="control-label">@lang('app.status')</label>--}}
                                <select class="form-control" name="status" id="status" data-style="form-control">
                                    <option value="all">@lang('app.all')</option>
                                    <option value="complete">@lang('app.completed')</option>
                                    <option value="pending">@lang('app.pending')</option>
                                </select>
                            </div>
                        </div>
                        @if(in_array('projects', $modules))
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
                        @endif
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

{{--                <div class="row">--}}
{{--                    <div class="col-md-6 pull-right">--}}
{{--                        {!! Form::open(['id'=>'importExcel','class'=>'ajax-form','method'=>'POST']) !!}--}}
{{--                        <div class="form-group">--}}
{{--                            <div class="col-md-12 text-right">--}}
{{--                                <div class="checkbox checkbox-info">--}}
{{--                                    <input id="calculate-task-progress" name="currency_character" value="true"--}}
{{--                                           type="checkbox">--}}
{{--                                    <label for="calculate-task-progress">@lang('modules.payments.firstCharacter')</label>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="form-group">--}}
{{--                            <div class="col-xs-12">--}}
{{--                                <div class="fileinput fileinput-new input-group" data-provides="fileinput">--}}
{{--                                    <div class="form-control" data-trigger="fileinput">--}}
{{--                                        <i class="glyphicon glyphicon-file fileinput-exists"></i>--}}
{{--                                        <span class="fileinput-filename"></span>--}}
{{--                                    </div>--}}
{{--                                    <span class="input-group-addon btn btn-default btn-file">--}}
{{--                                        <span class="fileinput-new"><i class="fa fa-file-excel-o text-success"></i> @lang('modules.payments.import')</span>--}}
{{--                                            <span class="fileinput-exists">@lang('app.change')</span>--}}
{{--                                            <input type="file" name="import_file" id="import_file">--}}
{{--                                            </span>--}}
{{--                                    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">@lang('app.remove')</a>--}}
{{--                                    <a href="javascript:;" id="import-excel" class="input-group-addon btn btn-success fileinput-exists text-white" data-dismiss="fileinput">@lang('app.submit')</a>--}}
{{--                                </div>--}}

{{--                                <a href="{{ route('admin.payments.downloadSample') }}" class="btn btn-success"><i class="fa fa-download"></i> @lang('app.sampleFile')</a>--}}

{{--                            </div>--}}
{{--                        </div>--}}

{{--                        {!! Form::close() !!}--}}

{{--                    </div>--}}
{{--                </div>--}}
                <div class="table-responsive">
                    {!! $dataTable->table(['class' => 'table table-bordered table-hover toggle-circle default footable-loaded footable']) !!}
                </div>
            </div>
        </div>
    </div>
    <!-- .row -->

    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-lg in" id="paymentDetail" role="dialog" aria-labelledby="myModalLabel"
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

@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('js/datatables/buttons.server-side.js') }}"></script>
<script src="{{ asset('plugins/bower_components/moment/moment.js') }}"></script>
<script type="text/javascript" src="{{ asset('plugins/daterange-picker/daterangepicker.js') }}"></script>

{!! $dataTable->scripts() !!}

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
        if (!project) {
            project = 'all';
        }
        var client = $('#client').val();

        data['startDate'] = startDate;
        data['endDate'] = endDate;
        data['status'] = status;
        data['project'] = project;
        data['client'] = client;
    });
    
    var table;
    $(function() {

        $(document).ready(function(){
            loadTable();
        });


        $('body').on('click', '.sa-params', function(){
            var id = $(this).data('payment-id');
            swal({
                title: "@lang('messages.sweetAlertTitle')",
                text: "@lang('messages.confirmation.recoverPaymentRecord')",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('messages.deleteConfirmation')",
                cancelButtonText: "@lang('messages.confirmNoArchive')",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                    var url = "{{ route('admin.payments.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                loadTable();
                            }
                        }
                    });
                }
            });
        });

        $('#import-excel').click(function () {
            $.easyAjax({
                url: '{{route('admin.payments.importExcel')}}',
                container: '#importExcel',
                type: "POST",
                redirect: true,
                file: (document.getElementById("import_file").files.length == 0) ? false : true
            })
        });


    });

    function loadTable(){
        window.LaravelDataTables["payments-table"].draw();
    }

    $('.toggle-filter').click(function () {
        $('#ticket-filters').toggle('slide');
    })

    $('#apply-filters').click(function () {
        loadTable();
    });

    $('#reset-filters').click(function () {
        $('#filter-form')[0].reset();
        $('.select2').val('all');
       $('#project').select2();
       $('#start-date').val('');
       $('#end-date').val('');
        $('#reportrange span').html('');
        loadTable();
    })

    $('body').on('click', '.view-payment', function () {
        var id = $(this).data('payment-id');
        var url = '{{route('admin.payments.show', ":id")}}';
        url = url.replace(':id', id);

        $.ajaxModal('#paymentDetail', url);
    })


</script>
@endpush
