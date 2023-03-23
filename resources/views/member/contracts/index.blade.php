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
            @if($user->cans('add_contract'))
                <a href="{{ route('member.contracts.create') }}" class="btn btn-outline btn-success btn-sm">@lang('modules.contracts.createContract') <i class="fa fa-plus" aria-hidden="true"></i></a>
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
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/daterange-picker/daterangepicker.css') }}" />

    <style>
        #contracts-table_wrapper .dt-buttons{
            display: none !important;
        }
    </style>
@endpush

@section('filter-section')
    <div class="row" id="ticket-filters">

        <form action="" id="filter-form">
            <div class="col-xs-12">
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
            <div class="col-xs-12">
                <h5 >@lang('app.client')</h5>
                <div class="form-group">
                    <div>
                        <select class="select2 form-control" data-placeholder="@lang('app.client')" name="client" id="clientID">
                            <option value="all">@lang('app.all')</option>
                            @foreach($clients as $client)

                                <option value="{{ $client->id }}">{{ ucwords($client->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-xs-12">
                <h5 >@lang('modules.contracts.contractType')</h5>
                <div class="form-group">
                    <div>
                        <select class="select2 form-control" data-placeholder="@lang('modules.contracts.contractType')" name="contractType" id="contractType">
                            <option value="all">@lang('app.all')</option>
                            @foreach($contractType as $type)

                                <option value="{{ $type->id }}">{{ ucwords($type->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-xs-12">
                <div class="form-group m-t-10">
                    <label class="control-label col-xs-12">&nbsp;</label>
                    <button type="button" id="apply-filters" class="btn btn-success col-md-6"><i class="fa fa-check"></i> @lang('app.apply')</button>
                    <button type="button" id="reset-filters" class="btn btn-inverse col-md-5 col-md-offset-1"><i class="fa fa-refresh"></i> @lang('app.reset')</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('content')
    <div class="row dashboard-stats">
        <div class="col-md-12 m-b-30">
            <div class="white-box">
                <div class="col-sm-4 text-center">
                    <h4><span class="text-dark" id="totalProjects">{{ $contractCounts }}</span> <span class="font-12 text-muted m-l-5"> @lang('modules.contracts.totalContracts')</span></h4>
                </div>
                <div class="col-sm-4 b-l text-center">
                    <h4><span class="text-warning" id="daysPresent">{{ $aboutToExpireCounts }}</span> <span class="font-12 text-muted m-l-5"> @lang('modules.contracts.aboutToExpire')</span></h4>
                </div>
                <div class="col-sm-4 b-l text-center">
                    <h4><span class="text-danger" id="overdueProjects">{{ $expiredCounts }}</span> <span class="font-12 text-muted m-l-5"> @lang('modules.contracts.expired')</span></h4>
                </div>

            </div>
        </div>

    </div>


    <div class="row">
        <div class="col-xs-12">
            <div class="white-box">

                <div class="table-responsive">
                    {!! $dataTable->table(['class' => 'table table-bordered table-hover toggle-circle default footable-loaded footable']) !!}
                </div>
            </div>
        </div>
    </div>
    <!-- .row -->

@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
@if($global->locale == 'en')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/locales/bootstrap-datepicker.{{ $global->locale }}-AU.min.js"></script>
@else
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/locales/bootstrap-datepicker.{{ $global->locale }}.min.js"></script>
@endif
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

    $(function() {

        $('body').on('click', '.sa-params', function(){
            var id = $(this).data('contract-id');
            swal({
                title: "@lang('messages.sweetAlertTitle')",
                text: "@lang('messages.confirmation.recoverNewContract')",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('messages.deleteConfirmation')",
                cancelButtonText: "@lang('messages.confirmNoArchive')",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                    var url = "{{ route('member.contracts.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                window.LaravelDataTables["contracts-table"].draw();
                            }
                        }
                    });
                }
            });
        });

    });

    $('.toggle-filter').click(function () {
        $('#ticket-filters').toggle('slide');
    })

    $('#apply-filters').click(function () {
        $('#contracts-table').on('preXhr.dt', function (e, settings, data) {
            var startDate = $('#start-date').val();
            if (startDate == '') {
                startDate = null;
            }
            var endDate = $('#end-date').val();
            if (endDate == '') {
                endDate = null;
            }
            var clientID = $('#clientID').val();
            var contractType = $('#contractType').val();
            var status = $('#status').val();
            data['startDate'] = startDate;
            data['endDate'] = endDate;
            data['status'] = status;
            data['clientID'] = clientID;
            data['contractType'] = contractType;
        });
        window.LaravelDataTables["contracts-table"].draw();
    });

    $('#reset-filters').click(function () {
        $('#filter-form')[0].reset();
        $('.select2').val('all');
        $('#filter-form').find('select').select2();
        $('#start-date').val('');
        $('#end-date').val('');
        $('#reportrange span').html('');
        window.LaravelDataTables["contracts-table"].draw();
    });
</script>
@endpush
