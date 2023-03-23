@extends('layouts.super-admin')

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
                <li><a href="{{ route('super-admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('super-admin.language-settings.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.addNew')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@section('content')

    <div class="row">
        <div class="col-md-6">
            <div class="white-box">
                <h3 class="box-title m-b-0">@lang('app.add') @lang('app.language')</h3>

                <p class="text-muted m-b-30 font-13"></p>

                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        {!! Form::open(['id'=>'createCurrency','class'=>'ajax-form','method'=>'POST']) !!}
                        <div class="form-group">
                            <label for="language_name">@lang('app.language') @lang('app.name')</label>
                            <input type="text" class="form-control" id="language_name" name="language_name"
                                   placeholder="Enter Language Name">
                        </div>
                        <div class="form-group">
                            <label for="language_code">@lang('app.language_code')</label>
                            <input type="text" class="form-control" id="language_code" name="language_code"
                                   placeholder="Enter Language Code">
                        </div>
                        <div class="form-group ">
                            <label for="usd_price">@lang('app.status') </label>
                            <select class="form-control" name="status">
                                <option value="enabled">@lang('app.enable')</option>
                                <option value="disabled">@lang('app.disable')</option>
                            </select>
                        </div>

                        <button type="submit" id="save-form" class="btn btn-success waves-effect waves-light m-r-10">
                            @lang('app.save')
                        </button>
                        <button type="reset" class="btn btn-inverse waves-effect waves-light">@lang('app.reset')</button>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- .row -->

@endsection

@push('footer-script')
<script>

    // store language
    $('#save-form').click(function () {
        $.easyAjax({
            url: '{{route('super-admin.language-settings.store')}}',
            container: '#createCurrency',
            type: "POST",
            redirect: true,
            data: $('#createCurrency').serialize()
        })
    });
</script>
@endpush

