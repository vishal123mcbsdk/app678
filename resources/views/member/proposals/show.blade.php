@extends('layouts.member-app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 bg-title-left">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }} #{{ $lead->id }} - <span
                        class="font-bold">{{ ucwords($lead->company_name) }}</span></h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12 bg-title-right">
            <ol class="breadcrumb">
                <li><a href="{{ route('member.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('member.leads.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('modules.projects.files')</li>
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
@endpush

@section('content')

    <div class="row">
        <div class="col-xs-12">

            <section>
                <div class="sttabs tabs-style-line">
                    <div class="white-box">
                        <nav>
                            <ul>
                                <li ><a href="{{ route('member.leads.show', $lead->id) }}"><span>@lang('modules.lead.profile')</span></a>
                                </li>
                                @if($user->cans('edit_lead'))
                                    <li class="tab-current"><a href="{{ route('member.proposals.show', $lead->id) }}"><span>@lang('modules.lead.proposal')</span></a></li>
                                    <li ><a href="{{ route('member.lead-files.show', $lead->id) }}"><span>@lang('modules.lead.file')</span></a></li>
                                    <li ><a href="{{ route('member.leads.followup', $lead->id) }}"><span>@lang('modules.lead.followUp')</span></a></li>
                                @endif
                            </ul>
                        </nav>
                    </div>

                    <div class="content-wrap">
                        <section id="section-line-3" class="show">
                            <div class="row">
                                <div class="col-xs-12" id="files-list-panel">
                                    <div class="white-box">
                                        <h2>@lang('modules.proposal.title')</h2>

                                        <div class="row m-b-10">
                                            <div class="col-xs-12">
                                                <div class="white-box">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <a href="{{ route('member.proposals.create') }}/{{$lead->id}}" class="btn btn-outline btn-success btn-sm">@lang('modules.proposal.createTitle') <i class="fa fa-plus" aria-hidden="true"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-hover toggle-circle default footable-loaded footable" id="proposal-table">
                                                            <thead>
                                                            <tr>
                                                                <th>@lang('app.id')</th>
                                                                <th>@lang('app.lead')</th>
                                                                <th>@lang('modules.invoices.total')</th>
                                                                <th>@lang('modules.estimates.validTill')</th>
                                                                <th>@lang('app.status')</th>
                                                                <th>@lang('app.action')</th>
                                                            </tr>
                                                            </thead>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </section>

                    </div><!-- /content -->
                </div><!-- /tabs -->
            </section>
        </div>


    </div>
    <!-- .row -->

@endsection

@push('footer-script')
    <script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
<script>


    $("body").tooltip({
        selector: '[data-toggle="tooltip"]'
    });

    var table = $('#proposal-table').dataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: '{!! route('member.proposals.data', $lead->id) !!}',
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
            { data: 'id', name: 'id' },
            { data: 'company_name', name: 'company_name' },
            { data: 'total', name: 'total' },
            { data: 'valid_till', name: 'valid_till' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', width: '5%' }
        ]
    });

    $('body').on('click', '.sa-params', function(){
        var id = $(this).data('proposal-id');
        swal({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.confirmation.deleteProposal')",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "@lang('messages.deleteConfirmation')",
            cancelButtonText: "@lang('messages.confirmNoArchive')",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm){
            if (isConfirm) {

                var url = "{{ route('member.proposals.destroy',':id') }}";
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

</script>
@endpush