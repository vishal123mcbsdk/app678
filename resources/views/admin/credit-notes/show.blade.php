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
            <a class="btn btn-default btn-sm btn-outline"
            href="{{ route('admin.all-credit-notes.download', $creditNote->id) }}"> <span><i class="fa fa-file-pdf-o"></i> @lang('modules.credit-notes.downloadPdf')</span> </a>

            @if($invoiceExist && $creditNote->invoice_id)
                <a href="{{ route('admin.all-invoices.show', $creditNote->invoice_id) }}" class="btn btn-info btn-outline btn-sm">
                    @lang('app.viewInvoice')
                </a>
            @endif
            @if ($creditNote->status == 'open')
                <a href="javascript:;" onclick="applyToInvoice('{{ route('admin.all-credit-notes.apply-to-invoice-modal', $creditNote->id) }}')" class="btn btn-primary btn-outline btn-sm">
                    @lang('app.applyToInvoice')
                </a>    
            @endif
            @if ($creditNote->invoices->count() > 0)
                <a href="javascript:;" onclick="showCreditedInvoices('{{ route('admin.all-credit-notes.credited-invoices', $creditNote->id) }}')" class="btn btn-warning btn-outline btn-sm">
                    @lang('app.creditedInvoices')
                </a>    
            @endif
            
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang("app.menu.home")</a></li>
                <li><a href="{{ route('admin.all-credit-notes.index') }}">@lang("app.menu.credit-note")</a></li>
                <li class="active">@lang('app.credit-note')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">

<style>
    .ribbon-wrapper {
        background: #ffffff !important;
    }
</style>
@endpush


@section('content')

    <div class="row">
        <div class="col-xs-12">
            <div class="white-box">
                <div class="col-md-4 text-center">
                    <h4><span class="text-dark">{{ currency_formatter($creditNote->total,$creditNote->currency->currency_symbol)}}</span> <span class="font-12 text-muted m-l-5"> @lang('modules.credit-notes.creditAmountTotal')</span></h4>
                </div>
                
                <div class="col-md-4 text-center b-l">
                    <h4><span class="text-success">{{ currency_formatter($creditNote->creditAmountRemaining(),$creditNote->currency->currency_symbol) }}</span> <span class="font-12 text-muted m-l-5"> @lang('modules.credit-notes.creditAmountRemaining')</span></h4>
                </div>
                
                <div class="col-md-4 text-center b-l">
                    <h4><span class="text-danger">{{ currency_formatter($creditNote->creditAmountUsed(),$creditNote->currency->currency_symbol) }}</span> <span class="font-12 text-muted m-l-5"> @lang('modules.credit-notes.creditAmountUsed')</span></h4>
                </div>
                
            </div>
        </div>

    </div>

    <div class="row">
  
        <div class="col-xs-12">
            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                   <i class="fa fa-check"></i> {!! $message !!}
                </div>
                <?php Session::forget('success');?>
            @endif

            @if ($message = Session::get('error'))
                <div class="custom-alerts alert alert-danger fade in">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                    {!! $message !!}
                </div>
                <?php Session::forget('error');?>
            @endif


            <div class="white-box printableArea ribbon-wrapper">
                
                <div class="clearfix"></div>

                <div class="ribbon-content  m-t-40 b-all p-20">
                    @if($creditNote->status == 'closed')
                        <div class="ribbon ribbon-bookmark ribbon-danger">@lang('modules.credit-notes.closed')</div>
                    @else
                        <div class="ribbon ribbon-bookmark ribbon-success">@lang('modules.credit-notes.open')</div>
                    @endif
                    <h4 class="text-right"><b>@lang('app.credit-note')</b> <span class="pull-right m-l-10">{{ $creditNote->cn_number }}</span></h4>
                    <hr>
                    <div class="row">
                        <div class="col-xs-12">

                            <div class="pull-left">
                                <address>
                                    <h4> &nbsp;<b class="text-danger">{{ ucwords($global->company_name) }}</b></h4>
                                    @if(!is_null($settings))
                                        <p class="text-muted m-l-5">{!! nl2br($global->address) !!}</p>
                                    @endif
                                    @if($creditNoteSetting->show_gst == 'yes' && !is_null($creditNoteSetting->gst_number))
                                        <p class="text-muted m-l-5"><b>@lang('app.gstIn')
                                                :</b>{{ $creditNoteSetting->gst_number }}</p>
                                    @endif
                                </address>
                            </div>
                            <div class="pull-right text-right">
                                <address>
                                    @if($creditNote->project)
                                    @if(!is_null($creditNote->project_id) && !is_null($creditNote->project->clientdetails))
                                        <h3>@lang('modules.invoices.to'),</h3>
                                        <h4 class="font-bold">{{ ucwords($creditNote->project->clientdetails->name) }}</h4>

                                        <p class="text-muted m-l-30">{!! nl2br($creditNote->project->clientdetails->address) !!}</p>
                                        @if($creditNoteSetting->show_gst == 'yes' && !is_null($creditNote->project->clientdetails->gst_number))
                                            <p class="m-t-5"><b>@lang('app.gstIn')
                                                    :</b>  {{ $creditNote->project->clientdetails->gst_number }}
                                            </p>
                                        @endif
                                        @endif
                                    @elseif(!is_null($creditNote->client_id))
                                        <h3>@lang('modules.invoices.to'),</h3>
                                        <h4 class="font-bold">{{ ucwords($creditNote->client->name) }}</h4>
                                        @if(!is_null($creditNote->client->client[0]))
                                            <p class="text-muted m-l-30">{!! nl2br($creditNote->clientdetails->address) !!}</p>
                                            @if($creditNoteSetting->show_gst == 'yes' && !is_null($creditNote->clientdetails->gst_number))
                                                <p class="m-t-5"><b>@lang('app.gstIn')
                                                        :</b>  {{ $creditNote->clientdetails->gst_number }}
                                                </p>
                                            @endif
                                        @endif

                                    @elseif(is_null($creditNote->client_id) || (is_null($creditNote->project_id) && is_null($creditNote->project->clientdetails)))
                                        @if($creditNote->invoice->client)
                                            <h3>@lang('modules.invoices.to'),</h3>
                                            <h4 class="font-bold">{{ ucwords($creditNote->invoice->client->name) }}</h4>
                                            <p class="text-muted m-l-30">{!! nl2br($creditNote->invoice->client->client_detail->address) !!}</p>
                                            @if($creditNoteSetting->show_gst == 'yes' && !is_null($creditNote->invoice->client->client_detail->gst_number))
                                                <p class="m-t-5"><b>@lang('app.gstIn')
                                                        :</b>  {{ $creditNote->invoice->client->client_detail->gst_number }}
                                                </p>
                                            @endif
                                        @elseif($creditNote->invoice->project)
                                            <h3>@lang('modules.invoices.to'),</h3>
                                            <h4 class="font-bold">{{ ucwords($creditNote->invoice->project->clientdetails->name) }}</h4>

                                            <p class="text-muted m-l-30">{!! nl2br($creditNote->invoice->project->clientdetails->address) !!}</p>
                                            @if($creditNoteSetting->show_gst == 'yes' && !is_null($creditNote->invoice->project->clientdetails->gst_number))
                                                <p class="m-t-5"><b>@lang('app.gstIn')
                                                        :</b>  {{ $creditNote->invoice->project->clientdetails->gst_number }}
                                                </p>
                                            @endif
                                        @endif
                                    @endif

                                    <p class="m-t-30"><b>@lang('app.credit-note') @lang('app.date') :</b> <i
                                                class="fa fa-calendar"></i> {{ $creditNote->issue_date->format($global->date_format) }}
                                    </p>

                                    <p><b>@lang('app.dueDate') :</b> <i
                                                class="fa fa-calendar"></i> {{ $creditNote->due_date->format($global->date_format) }}
                                    </p>
                                    @if($creditNote->recurring == 'yes')
                                        <p><b class="text-danger">@lang('modules.creditNotes.billingFrequency') : </b> {{ $creditNote->billing_interval . ' '. ucfirst($creditNote->billing_frequency) }} ({{ ucfirst($creditNote->billing_cycle) }} cycles)</p>
                                    @endif
                                </address>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="table-responsive m-t-40" style="clear: both;">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>@lang("modules.credit-notes.item")</th>
                                        @if($invoiceSetting->hsn_sac_code_show)
                                            <th >@lang('modules.invoices.hsnSacCode')</th>
                                        @endif
                                        <th class="text-right">@lang("modules.credit-notes.qty")</th>
                                        <th class="text-right">@lang("modules.credit-notes.unitPrice")</th>
                                        <th class="text-right">@lang("modules.credit-notes.price")</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $count = 0; ?>
                                    @foreach($creditNote->items as $item)
                                        @if($item->type == 'item')
                                            <tr>
                                                <td class="text-center">{{ ++$count }}</td>
                                                <td>{{ ucfirst($item->item_name) }}</td>
                                                @if($invoiceSetting->hsn_sac_code_show)
                                                    <td>{{ ($item->hsn_sac_code) ?? '--' }}</td>
                                                @endif
                                                <td class="text-right">{{ $item->quantity }}</td>
                                                <td class="text-right"> {{ currency_formatter($item->unit_price,$creditNote->currency->currency_symbol) }} </td>
                                                <td class="text-right">{{ currency_formatter($item->amount,$creditNote->currency->currency_symbo) }} </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="pull-right m-t-30 m-b-15 text-right">
                                <p>@lang("modules.credit-notes.subTotal")
                                    : {{ currency_formatter($creditNote->sub_total,$creditNote->currency->currency_symbol) }}</p>

                                @if ($discount > 0)
                                    <p>@lang("modules.credit-notes.discount")
                                    : {{ currency_formatter($discount,$creditNote->currency->currency_symbol) }} </p>
                                @endif
                                @foreach($taxes as $key=>$tax)
                                    <p>{{ strtoupper($key) }}
                                        : {{ currency_formatter($tax,$creditNote->currency->currency_symbol) }} </p>
                                @endforeach
                                <hr>
                                <h3><b>@lang("modules.credit-notes.total")
                                        :</b> {{ currency_formatter($creditNote->total,$creditNote->currency->currency_symbol) }}
                                </h3>
                                <hr>

                            </div>
                        </div>



                        <div class="col-xs-12">
                            @if(!is_null($creditNote->note))
                                <div class="col-xs-12">
                                    <p><strong>@lang('app.note')</strong>: {{ $creditNote->note }}</p>
                                </div>
                            @endif
                            <div class="clearfix"></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-lg in" id="applyToInvoice" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span>
                </div>
                <div class="modal-body">
                    @lang('app.loading')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">@lang('app.close')</button>
                    <button type="button" class="btn blue">@lang('app.save') @lang('app.changes')</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    {{--Ajax Modal Ends--}}

    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-lg in" id="creditedInvoices" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span>
                </div>
                <div class="modal-body">
                    @lang('app.loading')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">@lang('app.close')</button>
                    <button type="button" class="btn blue">@lang('app.save') @lang('app.changes')</button>
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
<script>
    function applyToInvoice(url) {
        $.ajaxModal('#applyToInvoice', url);
    }

    function showCreditedInvoices(url) {
        $.ajaxModal('#creditedInvoices', url);
    }

    function deleteCreditedInvoice(credit_id, id) {
        let url = '{{ route('admin.all-credit-notes.delete-credited-invoice', [':id']) }}';
        url = url.replace(':id', id);

        $.easyAjax({
            url: url,
            type: 'POST',
            data: { credit_id: credit_id, _token: '{{csrf_token()}}'},
            success: function (response) {
                $('#creditedInvoices .modal-content').html(response.view);
                $('#creditedInvoices').on('hide.bs.modal', function (e) {
                    location.reload();
                })
            }
        })
    }

    $(function () {
        var table = $('#credit-notes-table').dataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: '{{ route('admin.all-credit-notes.create') }}',
            deferRender: true,
            "order": [[0, "desc"]],
            language: {
                "url": "<?php echo __("app.datatable") ?>"
            },
            "fnDrawCallback": function (oSettings) {
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'project_name', name: 'projects.project_name'},
                {data: 'cn_number', name: 'cn_number'},
                {data: 'currency_symbol', name: 'currencies.currency_symbol'},
                {data: 'total', name: 'total'},
                {data: 'issue_date', name: 'issue_date'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });

    });

    // Show Payment detail modal
    function showPayments() {
        var url = '{{route('admin.all-credit-notes.payment-detail', $creditNote->id)}}';
        $.ajaxModal('#paymentDetail', url);
    }

</script>
@endpush