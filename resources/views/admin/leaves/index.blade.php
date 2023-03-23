@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 bg-title-left">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }} <span class="text-warning b-l p-l-10 m-l-5">{{ count($pendingLeaves) }}</span> <a href="{{ route('admin.leaves.pending') }}" class="font-12 text-muted m-l-5"> @lang('modules.leaves.pendingLeaves')</a>
            </h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12 text-right bg-title-right">
            <a href="{{ route('admin.leave.all-leaves') }}" class="btn btn-sm btn-info waves-effect waves-light btn-outline">
                <i class="fa fa-list"></i> @lang('app.all') @lang('app.menu.leaves')
            </a>
            
            <a href="{{ route('admin.leaves.create') }}" class="btn btn-sm btn-success waves-effect waves-light m-l-10 btn-outline">
            <i class="ti-plus"></i> @lang('modules.leaves.assignLeave')</a>
            
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('css/full-calendar/main.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/timepicker/bootstrap-timepicker.min.css') }}">

<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/multiselect/css/multi-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bootstrap-colorselector/bootstrap-colorselector.min.css') }}">

<style>
    .fc-event{
        font-size: 10px !important;
    }
</style>
@endpush

@section('content')


    <div class="row">
        <div class="col-xs-12">
            <div class="white-box">
               


                <div id="calendar"></div>
            </div>
        </div>

        {{--<div class="col-md-5">--}}
            {{--<div class="panel panel-default">--}}
                {{--<div class="panel-heading">@lang('modules.leaves.pendingLeaves')</div>--}}
                {{--<div class="panel-wrapper collapse in">--}}
                    {{--<div class="panel-body">--}}
                        {{--<ul class="list-task list-group" data-role="tasklist">--}}
                            {{--@forelse($pendingLeaves as $key=>$pendingLeave)--}}
                                {{--<li class="list-group-item" data-role="task">--}}
                                    {{--{{ ($key+1) }}. <strong>{{ ucwords($pendingLeave->user->name) }}</strong> for {{ $pendingLeave->leave_date->format($global->date_format) }} ({{ $pendingLeave->leave_date->format('l') }})--}}
                                    {{--<br>--}}
                                    {{--<strong>@lang('app.reason'): </strong>{{ $pendingLeave->reason }}--}}
                                    {{--<br>--}}
                                    {{--<div class="m-t-10"></div>--}}
                                    {{--<a href="javascript:;" data-leave-id="{{ $pendingLeave->id }}" data-leave-action="approved" class="btn btn-xs btn-success btn-rounded m-r-5 leave-action"><i class="fa fa-check"></i> @lang('app.accept')</a>--}}

                                    {{--<a href="javascript:;" data-leave-id="{{ $pendingLeave->id }}" data-leave-action="rejected" class="btn btn-xs btn-danger btn-rounded leave-action-reject"><i class="fa fa-times"></i> @lang('app.reject')</a>--}}
                                {{--</li>--}}
                            {{--@empty--}}
                                {{--@lang('messages.noPendingLeaves')--}}
                            {{--@endforelse--}}
                        {{--</ul>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}

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
    var taskEvents = [
        @foreach($leaves as $leave)
        @if($leave->status == 'approved')
        {
            id: '{{ ucfirst($leave->id) }}',
            title: '{{ ucfirst($leave->user->name) }}',
            start: '{{ $leave->leave_date->format("Y-m-d") }}',
            end: '{{ $leave->leave_date->format("Y-m-d") }}',
            className: 'bg-{{ $leave->type->color }}'
        },
        @else
        {
            id: '{{ ucfirst($leave->id) }}',
            title: '<i class="fa fa-warning"></i> {{ ucfirst($leave->user->name) }}',
            start: '{{ $leave->leave_date->format("Y-m-d") }}',
            end: '{{ $leave->leave_date->format("Y-m-d") }}',
            className: 'bg-{{ $leave->type->color }}'
        },
        @endif
        @endforeach
    ];

    var getEventDetail = function (id) {
        var url = '{{ route('admin.leaves.show', ':id')}}';
        url = url.replace(':id', id);

        $('#modelHeading').html('Event');
        $.ajaxModal('#eventDetailModal', url);
    }

    var calendarLocale = '{{ $global->locale }}';
    var firstDay = '{{ $global->week_start }}';

    $('.leave-action-reject').click(function () {
        var action = $(this).data('leave-action');
        var leaveId = $(this).data('leave-id');
        var searchQuery = "?leave_action="+action+"&leave_id="+leaveId;
        var url = '{!! route('admin.leaves.show-reject-modal') !!}'+searchQuery;
        $('#modelHeading').html('Reject Reason');
        $.ajaxModal('#eventDetailModal', url);
    });

    $('.leave-action').on('click', function() {
        var action = $(this).data('leave-action');
        var leaveId = $(this).data('leave-id');
        var url = '{{ route("admin.leaves.leaveAction") }}';

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

    })

    $('#pending-leaves').click(function() {
        window.location = '{{ route("admin.leaves.pending") }}';
    })
</script>

<script src="{{ asset('plugins/bower_components/calendar/jquery-ui.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/moment/moment.js') }}"></script>
<script src="{{ asset('js/full-calendar/main.min.js') }}"></script>
<script src="{{ asset('js/full-calendar/locales-all.min.js') }}"></script>
<script>
    var firstDay = '{{ $global->week_start }}';
    var initialLocaleCode = '{{ $global->locale }}';
    document.addEventListener('DOMContentLoaded', function() {
      var calendarEl = document.getElementById('calendar');
  
      var calendar = new FullCalendar.Calendar(calendarEl, {
        firstDay: firstDay,
        locale: initialLocaleCode,
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        // initialDate: '2020-09-12',
        navLinks: true, // can click day/week names to navigate views
        selectable: false,
        selectMirror: true,
        select: function(arg) {
          var title = prompt('Event Title:');
          if (title) {
            calendar.addEvent({
              title: title,
              start: arg.start,
              end: arg.end,
              allDay: arg.allDay
            })
          }
          calendar.unselect()
        },
        eventClick: function(arg) {
            getEventDetail(arg.event.id);
        },
        editable: false,
        dayMaxEvents: true, // allow "more" link when too many events
        events: taskEvents,
        eventDidMount: function(info){
            if (info.el.querySelector('.fc-event-title') !== null) {
                info.el.querySelector('.fc-event-title').innerHTML = info.event.title;
            }
            if (info.el.querySelector('.fc-list-event-title') !== null) {
                info.el.querySelector('.fc-list-event-title').innerHTML = info.event.title;
            }

        }
        
      });
  
      calendar.render();
    });
  
</script>
@endpush
