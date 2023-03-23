@extends('layouts.member-app')

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
                <li><a href="{{ route('member.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('member.tickets.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.addNew')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/html5-editor/bootstrap-wysihtml5.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
@endpush

@section('content')

    {!! Form::open(['id'=>'storeTicket','class'=>'ajax-form','method'=>'POST']) !!}
    <div class="form-body">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('modules.tickets.requestTicket')</div>

                    <div class="panel-wrapper collapse in">
                        <div class="panel-body">

                            <div class="row">

                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.tickets.ticketSubject') <span class="text-danger">*</span></label>
                                        <input type="text" id="subject" name="subject" class="form-control">
                                    </div>
                                </div>

                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.tickets.ticketDescription') <span class="text-danger">*</span></label></label>
                                        <textarea class="textarea_editor form-control" rows="10" name="description"
                                                  id="description"></textarea>
                                    </div>
                                </div>
                                <!--/span-->

                            </div>
                            <!--/row-->

                        </div>
                    </div>

                    <div class="panel-footer text-right">
                        <div class="btn-group dropup">
                            <button class="btn btn-success" id="submit-ticket" type="button">@lang('app.submit')</button>
                        </div>
                    </div>
                </div>


            </div>

        </div>
        <!-- .row -->
    </div>
    {!! Form::close() !!}

@endsection


@push('footer-script')
<script src="{{ asset('plugins/bower_components/html5-editor/wysihtml5-0.3.0.js') }}"></script>
<script src="{{ asset('plugins/bower_components/html5-editor/bootstrap-wysihtml5.js') }}"></script>
<script>
    $('.textarea_editor').wysihtml5();

    $('#submit-ticket').click(function () {
        $.easyAjax({
            url: '{{route('member.tickets.store')}}',
            container: '#storeTicket',
            type: "POST",
            data: $('#storeTicket').serialize()
        })
    });

</script>
@endpush