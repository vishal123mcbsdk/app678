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
            @if($user->cans('add_invoices'))
                <a href="{{ route('member.all-invoices.create') }}" class="btn btn-outline btn-success btn-sm">@lang('modules.invoices.addInvoice') <i class="fa fa-plus" aria-hidden="true"></i></a>
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
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/daterange-picker/daterangepicker.css') }}" />

<style>
    select.bs-select-hidden, select.selectpicker {
        display: block!important;
    }
</style>
@endpush

@section('content')

    <div class="row">
        <div class="col-xs-12">
            <div class="white-box">


                @if($user->cans('view_invoices'))
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
                            @if(in_array('projects', $modules))
                            <div class="col-xs-12">
                                <h5 >@lang('app.project')</h5>
                                <div class="form-group">
                                    {{--<label class="control-label">@lang('app.status')</label>--}}
                                    <select class="form-control select2" name="projectID" id="projectID" data-style="form-control">
                                        <option value="all">@lang('app.all')</option>
                                        @forelse($projects as $project)
                                            <option value="{{$project->id}}">{{ ucfirst($project->project_name) }}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            @endif
                            <div class="col-xs-12">
                                <h5 >@lang('app.status')</h5>
                                <div class="form-group">
                                    {{--<label class="control-label">@lang('app.status')</label>--}}
                                    <select class="form-control selectpicker" name="status" id="status" data-style="form-control">
                                        <option value="all">@lang('app.all')</option>
                                        <option value="unpaid">@lang('app.unpaid')</option>
                                        <option value="paid">@lang('app.paid')</option>
                                        <option value="partial">@lang('app.partial')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12">
                                <div class="form-group m-t-10">
                                    <button type="button" id="apply-filters" class="btn btn-success col-md-6"><i class="fa fa-check"></i> @lang('app.apply')</button>
                                    <button type="button" id="reset-filters" class="btn btn-inverse col-md-5 col-md-offset-1"><i class="fa fa-refresh"></i> @lang('app.reset')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    @endsection
                @endif
                <div class="table-responsive">
                    <table class="table table-bordered table-hover toggle-circle default footable-loaded footable" id="invoice-table">
                        <thead>
                        <tr>
                            <th>@lang('app.id')</th>
                            <th>@lang('app.invoice') #</th>
                            @if(in_array('projects', $modules))
                                <th>@lang('app.project')</th>
                            @endif
                            <th>@lang('app.client')</th>
                            <th>@lang('modules.invoices.total')</th>
                            <th>@lang('modules.invoices.invoiceDate')</th>
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


    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="invoiceUploadModal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-md" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading">@lang('modules.invoices.uploadInvoice')</span>
                </div>
                <div class="modal-body">
                    <div class="row" id="file-dropzone">
                        <div class="row m-b-20" id="file-dropzone">
                            <div class="col-xs-12">
                                <form action="{{route('member.invoiceFile.store')}}"
                                      class="dropzone"
                                      id="file-upload-dropzone">
                                    {{ csrf_field() }}
                                    <div class="fallback">
                                        <input name="file" type="file" />
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">@lang('app.close')</button>
                    <button type="button" class="btn blue" data-dismiss="modal">@lang('app.save')</button>
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
<script src="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
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
    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });
    var table;
    $(function() {
        loadTable();

        $('body').on('click', '.sa-params', function(){
            var id = $(this).data('invoice-id');
            swal({
                title: "@lang('messages.sweetAlertTitle')",
                text: "@lang('messages.confirmation.deleteRecoverInvoice')",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('messages.deleteConfirmation')",
                cancelButtonText: "@lang('messages.confirmNoArchive')",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                    var url = "{{ route('member.all-invoices.destroy',':id') }}";
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



    });

    //    $('#file-upload-dropzone').dropzone({
    Dropzone.options.fileUploadDropzone = {
        paramName: "file", // The name that will be used to transfer the file
        dictDefaultMessage: "@lang('modules.projects.dropFile')",
        uploadMultiple: false,
        dictCancelUpload: "Cancel",
        accept: function (file, done) {
            done();
        },
        init: function () {
            this.on('addedfile', function(){
                if(this.files.length > 1) {
                    this.removeFile(this.files[0]);
                }
            });
            this.on("success", function (file, response) {
            });
            var newDropzone = this;

            $('#invoiceUploadModal').on('hide.bs.modal', function(){
                newDropzone.removeAllFiles(true);
            });

        }
    };

    function loadTable(){
        var startDate = $('#start-date').val();

        if (startDate == '') {
            startDate = null;
        }

        var endDate = $('#end-date').val();

        if (endDate == '') {
            endDate = null;
        }

        var status = $('#status').val();
        var projectID = $('#projectID').val();
        if (!projectID) {
            projectID = 'all';
        }

        table = $('#invoice-table').dataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            destroy: true,
            ajax: '{!! route('member.all-invoices.data') !!}?startDate=' + startDate + '&endDate=' + endDate + '&status=' + status + '&projectID=' + projectID,
            "order": [[ 0, "desc" ]],
            language: {
                "url": "<?php echo __("app.datatable") ?>"
            },
            "fnDrawCallback": function( oSettings ) {
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            },
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'invoice_number', name: 'invoice_number' },
                @if(in_array('projects', $modules))
                    { data: 'project_name', name: 'project_name' },
                @endif
                { data: 'client_name', name: 'client_name' },
                { data: 'total', name: 'total' },
                { data: 'issue_date', name: 'issue_date' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', width: '12%' }
            ]
        });
        $('.table-responsive').on('click', '.invoice-upload', function(){
            var invoiceId = $(this).data('invoice-id');
            $('#file-upload-dropzone').prepend('<input name="invoice_id", value="' + invoiceId + '" type="hidden">');
        });
    }

    function toggleShippingAddress(invoiceId) {
        let url = "{{ route('member.all-invoices.toggleShippingAddress', ':id') }}";
        url = url.replace(':id', invoiceId);

        $.easyAjax({
            url: url,
            type: 'GET',
            success: function (response) {
                if (response.status === 'success') {
                    loadTable();
                }
            }
        })
    }

    function addShippingAddress(invoiceId) {
        let url = "{{ route('member.all-invoices.shippingAddressModal', ':id') }}";
        url = url.replace(':id', invoiceId);

        $.ajaxModal('#invoiceUploadModal', url);
    }
    
    $('.toggle-filter').click(function () {
        $('#ticket-filters').toggle('slide');
    })

    $('#apply-filters').click(function () {
        loadTable();
    });

    $('#reset-filters').click(function () {
        $('#filter-form')[0].reset();
        $('#projectID').val('all');
        $('#status').selectpicker('render');
        $('#projectID').select2();
        $('#start-date').val('');
        $('#end-date').val('');
        $('#reportrange span').html('');
        loadTable();
    })

    // Change Status As cancelled
    $('body').on('click', '.sa-cancel', function(){
        var id = $(this).data('invoice-id');
        swal({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.confirmation.cancelChangeInvoice')",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "@lang('messages.doIt')",
            cancelButtonText: "@lang('messages.confirmNoArchive')",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm){
            if (isConfirm) {

                var url = "{{ route('member.all-invoices.update-status',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'GET',
                    url: url,
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

    $('body').on('click', '.sendButton', function(){
        var id = $(this).data('invoice-id');
        var url = "{{ route('member.all-invoices.send-invoice',':id') }}";
        url = url.replace(':id', id);

        var token = "{{ csrf_token() }}";

        $.easyAjax({
            type: 'POST',
            url: url,
            data: {'_token': token},
            success: function (response) {
                if (response.status == "success") {
                    table._fnDraw();
                }
            }
        });
    });

</script>
@endpush