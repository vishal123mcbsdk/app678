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
            <a href="{{ route('admin.currency.create') }}"
               class="btn btn-outline btn-success btn-sm">@lang('modules.currencySettings.addNewCurrency') <i
                        class="fa fa-plus" aria-hidden="true"></i></a>
            <a href="javascript:;" id="update-exchange-rates"
               class="btn btn-outline btn-info btn-sm">@lang('app.update') @lang('modules.currencySettings.exchangeRate')
                <i class="fa fa-refresh" aria-hidden="true"></i></a>

            <ol class="breadcrumb">
                <li><a href="{{ route('super-admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>

        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@section('content')

    <div class="row">
        <div class="col-xs-12">
            <div class="panel">

                <div class=" sttabs tabs-style-line">
                    @include('admin.currencies.tabs')

                    @include('sections.admin_setting_menu')

                    <div class="tab-content">
                        <div id="vhome3" class="tab-pane active">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="white-box">
                                        <h3 class="box-title m-b-0">@lang('modules.currencySettings.currencies')</h3>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="alert alert-info"><i
                                                            class="fa fa-info-circle"></i> @lang('messages.exchangeRateNote')
                                                </div>
                                            </div>
                                        </div>


                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>@lang('modules.currencySettings.currencyName')</th>
                                                    <th>@lang('modules.currencySettings.currencySymbol')</th>
                                                    <th>@lang('modules.currencySettings.currencyPosition')</th>
                                                    <th>@lang('modules.currencySettings.currencyCode')</th>
                                                    <th>@lang('modules.currencySettings.exchangeRate')</th>
                                                    <th>@lang('app.status')</th>
                                                    <th class="text-nowrap">@lang('app.action')</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($currencies as $currency)
                                                    <tr>
                                                        <td>{{ ucwords($currency->currency_name) }} {!! ($global->currency_id == $currency->id) ? "<label class='label label-success'>DEFAULT</label>" : "" !!}</td>
                                                        <td>{{ $currency->currency_symbol }}</td>
                                                        <td>@lang('modules.currencySettings.'.$currency->currency_position)</td>
                                                        <td>{{ $currency->currency_code }}</td>
                                                        <td>{{ $currency->exchange_rate }}</td>
                                                        <td>
                                                            @php
                                                                $class = ($currency->status == 'enable') ? 'label-custom' : 'label-danger';
                                                            @endphp
                                                            <span class="label {{ $class }}">{{ ucfirst($currency->status) }}</span>
                                                        </td>
                                                        <td class="text-nowrap">
                                                            <a href="{{ route('admin.currency.edit', [$currency->id]) }}"
                                                               class="btn btn-info btn-circle"
                                                               data-toggle="tooltip" data-original-title="Edit"><i
                                                                        class="fa fa-pencil" aria-hidden="true"></i></a>

                                                            {{--<a href="javascript:;" class="btn btn-danger btn-circle sa-params"--}}
                                                            {{--data-toggle="tooltip" data-currency-id="{{ $currency->id }}" data-original-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></a>--}}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>    <!-- .row -->

                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>


    </div>
    <!-- .row -->
    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="projectCategoryModal" role="dialog" aria-labelledby="myModalLabel"
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
    <script>
        $('body').on('click', '.sa-params', function () {
            var id = $(this).data('currency-id');
            swal({
                title: "@lang('messages.sweetAlertTitle')",
                text: "@lang('messages.confirmation.deleteCurrancy')",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('messages.deleteConfirmation')",
                cancelButtonText: "@lang('messages.confirmNoArchive')",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {

                    var url = "{{ route('admin.currency.destroy',':id') }}";
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
                                window.location.reload();
                            }
                        }
                    });
                }
            });
        });
        $('ul.showClientTabs .currencySetting').addClass('tab-current');

        $('#update-exchange-rates').click(function () {
            var url = '{{route('admin.currency.update-exchange-rates')}}';
            $.easyAjax({
                url: url,
                type: "GET",
                success: function (response) {
                    if (response.status == "success") {
                        $.unblockUI();
//                                    swal("Deleted!", response.message, "success");
                        window.location.reload();
                    }
                }
            })
        });

    </script>
@endpush
