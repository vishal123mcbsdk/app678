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
            <a href="{{ route('member.leaves.create') }}" class="btn btn-sm btn-success btn-outline waves-effect waves-light">
                <i class="ti-plus"></i> @lang('modules.leaves.applyLeave')
            </a>
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
<link rel="stylesheet" href="{{ asset('plugins/bower_components/timepicker/bootstrap-timepicker.min.css') }}">

<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/multiselect/css/multi-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bootstrap-colorselector/bootstrap-colorselector.min.css') }}">

<link rel="stylesheet" href="{{ asset('plugins/bower_components/morrisjs/morris.css') }}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
@endpush

@section('content')

    <div class="row">

        @if($user->cans('view_leave'))
        <div class="sttabs tabs-style-line col-md-12">
            <div class="white-box">
                <nav>
                    <ul>
                        <li  class="tab-current"><a href="{{ route('member.leaves.index') }}"><span>@lang('modules.leaves.myLeaves')</span></a>
                        </li>
                        <li><a href="{{ route('member.leaves-dashboard.index') }}"><span>@lang('app.menu.dashboard')</span></a>
                        </li>

                    </ul>
                </nav>
            </div>
        </div>
        @endif

        <div class="col-xs-12">
            <div class="white-box">
                <div class="row">
                    <div class="col-md-4">
                        <ul class="basic-list">
                            @forelse($leaveTypes as $key=>$leaveType)
                                @if (isset($employeeLeavesQuota[$key]) && $employeeLeavesQuota[$key]->no_of_leaves > 0)
                                    <li>{{ ucfirst($leaveType->type_name) }}
                                        <span class="pull-right label-{{ $leaveType->color }} label">{{ (isset($leaveType->leavesCount[0])) ? $leaveType->leavesCount[0]->count : '0' }}</span>
                                    </li>                                    
                                @endif
                            @empty
                                <li>@lang('messages.noRecordFound')</li>
                            @endforelse
                        </ul>
                    </div>

                    <div class="col-md-3 col-md-offset-1 text-center"><span class="donut" data-peity='{ "fill": ["red", "#eeeeee"],    "innerRadius": 40, "radius": 60 }'>{{ count($leaves) }}/{{ $allowedLeaves }}</span><br>
                        <div class="btn btn-inverse btn-rounded">@lang('modules.leaves.leavesTaken') : {{ $leavesCount }}/{{ $allowedLeaves }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">@lang('modules.leaves.pendingLeaves')</div>
                <div class="panel-wrapper collapse in">
                    <div class="panel-body">
                        <ul class="list-task list-group" data-role="tasklist">
                            @forelse($pendingLeaves as $key=>$pendingLeave)
                                <li class="list-group-item" data-role="task">
                                    {{ ($key+1) }}. <strong>{{ ucwords($pendingLeave->user->name) }}</strong> for {{ $pendingLeave->leave_date->format($global->date_format) }}
                                    <br>
                                    <div class="m-t-10"></div>

                                    <a href="javascript:;" data-leave-id="{{ $pendingLeave->id }}" data-leave-action="rejected" class="btn btn-xs btn-danger btn-rounded leave-action"><i class="fa fa-times"></i> @lang('app.cancel')</a>
                                </li>
                            @empty
                                @lang('messages.noPendingLeaves')
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-xs-12">
            <div class="white-box">
                <div class="row">
                    <h3 class="box-title col-md-3">@lang('app.menu.leaves')</h3>

                    

                </div>


                <div class="table-responsive">
                    <table class="table table-bordered table-hover toggle-circle default footable-loaded footable" id="leaves-table">
                        <thead>
                        <tr>
                            <th>@lang('app.id')</th>
                            <th>@lang('modules.leaves.leaveType')</th>
                            <th>@lang('app.date')</th>
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
    <div class="modal fade bs-modal-md in" id="eventDetailModal" role="dialog" aria-labelledby="myModalLabel"
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

<script>

    function getEventDetail(id) {
        var url = '{{ route('member.leaves.show', ':id')}}';
        url = url.replace(':id', id);

        $('#modelHeading').html('Event');
        $.ajaxModal('#eventDetailModal', url);
    }

    $('.leave-action').click(function () {
        var action = $(this).data('leave-action');
        var leaveId = $(this).data('leave-id');
        var url = '{{ route("member.leaves.leaveAction") }}';

        $.easyAjax({
            type: 'POST',
            url: url,
            data: { 'action': action, 'leaveId': leaveId, '_token': '{{ csrf_token() }}' },
            success: function (response) {
                if(response.status == 'success'){
                    window.location.reload();
                }
            }
        });
    });

    $(function() {
        var table = $('#leaves-table').dataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            stateSave: true,
            ajax: '{!! route('member.leaves.data') !!}',
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
                { data: 'type', name: 'type' },
                { data: 'leave_date', name: 'leave_date' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', width: '15%' }
            ]
        });
    });

    $('body').on('click', '.sa-params', function(){
        var id = $(this).data('user-id');
        swal({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.confirmation.recoverLeaveApplication')",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "@lang('messages.deleteConfirmation')",
            cancelButtonText: "@lang('messages.confirmNoArchive')",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm){
            if (isConfirm) {
                var url = "{{ route('member.leaves.destroy',':id') }}";
                url = url.replace(':id', id);
                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token, '_method': 'DELETE'},
                    success: function (response) {
                        if (response.status == "success") {
                            window.location.reload();
                        }
                    }
                });
            }
        });
    });

</script>

<script src="{{ asset('plugins/bower_components/moment/moment.js') }}"></script>

<script src="{{ asset('plugins/bower_components/peity/jquery.peity.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/peity/jquery.peity.init.js') }}"></script>

<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
@endpush
