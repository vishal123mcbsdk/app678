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
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('admin.payments.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.update')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/switchery/dist/switchery.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datetime-picker/datetimepicker.css') }}">

@endpush

@section('content')

    <div class="row">
        <div class="col-xs-12">

            <div class="panel  panel-inverse">
                <div class="panel-heading"> @lang('modules.payments.updatePayment')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        {!! Form::open(['id'=>'updatePayment','class'=>'ajax-form','method'=>'PUT']) !!}
                        <div class="form-body">

                            <div class="row">
                                @if(in_array('projects', $modules))
                                    <div class="col-md-12 ">
                                        <div class="form-group">
                                            <label>@lang('app.selectProject')</label>
                                            <select class="select2 form-control" onchange="getInvoice(this.value)" data-placeholder="@lang('app.selectProject') (@lang('app.optional'))" name="project_id">
                                                <option value="">--</option>
                                                @foreach($projects as $project)
                                                    <option
                                                            @if($project->id == $payment->project_id) selected @endif
                                                            value="{{ $project->id }}">{{ $project->project_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif
                                    @if(in_array('invoices', $modules))
                                        <div class="col-md-12 ">
                                            <div class="form-group">
                                                <label>@lang('app.selectInvoice')</label>
                                                <select class="select2 form-control" data-placeholder="@lang('app.selectInvoice') (@lang('app.optional'))" name="invoice_id" id="invoice_id">
                                                    <option value="">--</option>
                                                    @foreach($invoices as $invoice)
                                                        <option @if($payment->invoice_id == $invoice->id) selected @endif  value="{{ $invoice->id }}">{{ $invoice->invoice_number }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                <div class="col-md-12 ">
                                    <div class="form-group">
                                        <label>@lang('modules.invoices.currency')</label>
                                        <select class="form-control" name="currency_id" id="currency_id">
                                            @foreach($currencies as $currency)
                                                <option
                                                        @if($currency->id == $payment->currency_id) selected @endif
                                                        value="{{ $currency->id }}">{{ $currency->currency_symbol.' ('.$currency->currency_code.')' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('modules.invoices.amount')</label>
                                        <input type="text" name="amount" id="amount" value="{{ number_format((float)$payment->amount, 2, '.', '') }}" class="form-control">
                                    </div>
                                </div>
                                <!--/span-->


                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('modules.payments.paymentGateway')</label>
                                        <input type="text" name="gateway" id="gateway" value="{{ $payment->gateway }}" class="form-control">
                                        <span class="help-block"> Paypal, Authorize.net, Stripe, Bank Transfer, Cash or others.</span>
                                    </div>
                                </div>
                                <!--/span-->

                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('modules.payments.transactionId')</label>
                                        <input type="text" name="transaction_id" id="transaction_id" value="{{ $payment->transaction_id }}" class="form-control">
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                            <!--/row-->

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.payments.paidOn')</label>
                                        <input type="text" class="form-control" name="paid_on"  id="paid_on" @if(is_null($payment->paid_on)) value="{{ Carbon\Carbon::today()->format($company->date_format.' '.$company->time_format) }}" @else value="{{ $payment->paid_on->format('d/m/Y H:i') }}" @endif>
                                    </div>
                                </div>

                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.receipt')</label>

                                        <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                            <div class="form-control" data-trigger="fileinput">
                                                <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                                <span class="fileinput-filename">{{ $payment->bill }}</span>
                                            </div>
                                            <span class="input-group-addon btn btn-default btn-file">
                                                <span class="fileinput-new">@lang('app.selectFile')</span>
                                                <span class="fileinput-exists">@lang('app.change')</span>
                                                <input type="file" name="bill" id="bill">
                                            </span>
                                            <a href="#" class="input-group-addon btn btn-default fileinput-exists"
                                               data-dismiss="fileinput">@lang('app.remove')</a>
                                        </div>
                                        @if(!is_null($payment->bill))
                                            <a target="_blank"
                                               href="{{ $payment->file_url }}">@lang('app.view') @lang('app.receipt')</a>
                                        @endif
                                    </div>
                                </div>


                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.remark')</label>
                                        <textarea id="remarks" name="remarks" class="form-control">{{ $payment->remarks }}</textarea>
                                    </div>
                                </div>

                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.status')</label>
                                        <select name="status" id="status" class="form-control">
                                            <option @if($payment->status == 'complete') selected @endif value="complete">@lang('app.completed')</option>
                                            <option @if($payment->status == 'pending') selected @endif value="pending">@lang('app.pending')</option>
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <!--/span-->


                        </div>
                        <div class="form-actions">
                            <button type="submit" id="save-form-2" class="btn btn-success"><i class="fa fa-check"></i>
                                @lang('app.update')
                            </button>

                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>    <!-- .row -->

@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/switchery/dist/switchery.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/moment/moment.js') }}"></script>
<script src="{{ asset('plugins/datetime-picker/datetimepicker.js') }}"></script>
<script>
    // Switchery
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    $('.js-switch').each(function() {
        new Switchery($(this)[0], $(this).data());

    });

    function getInvoice(project_id){
        var url = "{{route('admin.payments.getinvoice')}}";
        var token = "{{ csrf_token() }}";
        $.easyAjax({
            url: url,
            type: "GET",
            data: {project_id: project_id},
            success: function (data) {
                $('#invoice_id').html(data.invoices);
                $('#invoice_id').select2();
            }
        })
    }

    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    var timeFormat = '';
    var dateformat = '{{ $global->moment_format }}';

    @if($global->time_format == 'h:i A')
        timeFormat = 'hh:mm A';
    @elseif($global->time_format == 'h:i a')
        timeFormat = 'hh:mm a';
    @else
        timeFormat = 'HH:mm';
    @endif
    var dateTimeFormat = dateformat+' '+timeFormat;
    jQuery('#paid_on').datetimepicker({
        format: dateTimeFormat
    });

    $('#save-form-2').click(function () {
        $.easyAjax({
            url: '{{route('admin.payments.update', $payment->id)}}',
            container: '#updatePayment',
            type: "POST",
            file: true,
            data: $('#updatePayment').serialize()
        })
    });
</script>
@endpush
