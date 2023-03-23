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
                <li><a href="{{ route('admin.settings.index') }}">@lang('app.menu.settings')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@section('content')

    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-inverse">
                <div class="panel-heading">@lang('app.menu.ticketChannel')</div>

                <div class="vtabs customvtab m-t-10">

                    @include('sections.ticket_setting_menu')

                    <div class="tab-content">
                        <div id="vhome3" class="tab-pane active">

                            <div class="row">

                                <div class="col-xs-12">
                                    <div class="white-box">
                                        <h3>@lang('app.addNew') @lang('app.menu.ticketChannel')</h3>

                                        {!! Form::open(['id'=>'createChannel','class'=>'ajax-form','method'=>'POST']) !!}

                                        <div class="form-body">

                                            <div class="form-group">
                                                <label>@lang('modules.tickets.channelName')</label>
                                                <input type="text" name="channel_name" id="channel_name" class="form-control">
                                            </div>

                                            <div class="form-actions">
                                                <button type="submit" id="save-channel" class="btn btn-success"><i
                                                            class="fa fa-check"></i> @lang('app.save')
                                                </button>
                                            </div>
                                        </div>

                                        {!! Form::close() !!}

                                    </div>
                                </div>

                                <div class="col-xs-12">
                                    <div class="white-box">
                                        <h3>@lang('app.menu.ticketChannel')</h3>


                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>@lang('app.name')</th>
                                                    <th>@lang('app.action')</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @forelse($channels as $key=>$channel)
                                                    <tr>
                                                        <td>{{ ($key+1) }}</td>
                                                        <td>{{ ucwords($channel->channel_name) }}</td>
                                                        <td>
                                                            <a href="javascript:;" data-channel-id="{{ $channel->id }}"
                                                               class="btn btn-sm btn-info btn-rounded btn-outline edit-channel"><i
                                                                        class="fa fa-edit"></i> @lang('app.edit')</a>
                                                            <a href="javascript:;" data-channel-id="{{ $channel->id }}"
                                                               class="btn btn-sm btn-danger btn-rounded btn-outline delete-channel"><i
                                                                        class="fa fa-times"></i> @lang('app.remove')</a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td>
                                                            @lang('messages.noTicketChannelAdded')
                                                        </td>
                                                    </tr>
                                                @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>


    </div>
    <!-- .row -->


    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="ticketChannelModal" role="dialog" aria-labelledby="myModalLabel"
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
<script type="text/javascript">


    //    save project members
    $('#save-channel').click(function () {
        $.easyAjax({
            url: '{{route('admin.ticketChannels.store')}}',
            container: '#createChannel',
            type: "POST",
            data: $('#createChannel').serialize(),
            success: function (response) {
                if (response.status == "success") {
                    $.unblockUI();
                    window.location.reload();
                }
            }
        })
    });


    $('body').on('click', '.delete-channel', function () {
        var id = $(this).data('channel-id');
        swal({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.confirmation.removeChannelType')",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "@lang('app.yes')",
            cancelButtonText: "@lang('messages.confirmNoArchive')",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {

                var url = "{{ route('admin.ticketChannels.destroy',':id') }}";
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


    $('.edit-channel').click(function () {
        var typeId = $(this).data('channel-id');
        var url = '{{ route("admin.ticketChannels.edit", ":id")}}';
        url = url.replace(':id', typeId);

        $('#modelHeading').html("{{  __('app.edit')." ".__('app.menu.ticketChannel') }}");
        $.ajaxModal('#ticketChannelModal', url);
    })


</script>


@endpush

