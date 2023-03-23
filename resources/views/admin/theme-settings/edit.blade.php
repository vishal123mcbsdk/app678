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
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/clockpicker/dist/jquery-clockpicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/jquery-asColorPicker-master/css/asColorPicker.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/switchery/dist/switchery.min.css') }}">

@endpush

@section('content')


    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-inverse">
                <div class="panel-heading">{{ __($pageTitle) }}</div>

                <div class="vtabs customvtab m-t-10">
                    @include('sections.admin_setting_menu')

                    <div class="tab-content">
                        <div id="vhome3" class="tab-pane active">
                            <div class="row">
                                <div class="col-sm-12">


                                        <div class="form-group">
                                            <div class="radio-list">
                                                <label class="radio-inline p-0">
                                                    <div class="radio radio-info">
                                                        <input type="radio" name="active_theme" @if($global->active_theme == 'default') checked @endif id="default_theme" value="default">
                                                        <label for="default_theme">@lang('modules.themeSettings.useDefaultTheme')</label>
                                                    </div>
                                                </label>
                                                <label class="radio-inline">
                                                    <div class="radio radio-info">
                                                        <input type="radio" name="active_theme" id="custom_theme" @if($global->active_theme == 'custom') checked @endif value="custom">
                                                        <label for="custom_theme">@lang('modules.themeSettings.useCustomTheme')</label>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <p class="box-title m-t-30">@lang('modules.themeSettings.enableRoundTheme')</p>
                                            <div class="radio-list">
                                                <label class="radio-inline p-0">
                                                    <div class="radio radio-info">
                                                        <input type="radio" id="rounded_theme_yes" name="rounded_theme" @if($global->rounded_theme) checked @endif value="1">
                                                        <label for="rounded_theme_yes">@lang('app.yes')</label>
                                                    </div>
                                                </label>
                                                <label class="radio-inline">
                                                    <div class="radio radio-info">
                                                        <input type="radio" id="rounded_theme_no" name="rounded_theme" @if(!$global->rounded_theme) checked @endif value="0">
                                                        <label for="rounded_theme_no">@lang('app.no')</label>
                                                    </div>
                                                </label>
                                            </div>

                                        </div>

                                    </div>
                                        <div class="form-group">
                                            <label
                                                class="control-label">@lang('modules.themeSettings.enableRtl')
                                             </label>
                                            <div class="switchery-demo">
                                                <input type="checkbox" id="rtl" name="rtl"
                                                    @if($global->rtl == true) checked
                                                @endif class="js-switch rtl" data-color="#00c292"
                                                data-secondary-color="#f96262"/>
                                            </div>
                                         </div>
                                        <div id="custom-theme-options" >
                                            {!! Form::open(['id'=>'editSettings','class'=>'ajax-form','method'=>'POST']) !!}
                                            @if(module_enabled('Subdomain') && $global->login_ui == 0)
                                            <h3 class="box-title m-b-0"><b>@lang('app.login') @lang('app.panel') @lang('app.theme')</b></h3>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="example">
                                                        <p class="box-title m-t-30">@lang('app.loginLogoBackgroundColor')</p>
                                                        <input type="text" class="complex-colorpicker form-control"  id="logo_background_color" required name="logo_background_color" value="{{ $adminTheme->login_background }}" />
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            @endif
                                            <div @if($global->active_theme == 'default') style="display: none" @endif>
                                                <h3 class="box-title m-b-0"><b>@lang('modules.themeSettings.adminPanelTheme')</b></h3>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="example">
                                                            <p class="box-title m-t-30">@lang('modules.themeSettings.headerColor')</p>
                                                            <input type="text" class="colorpicker form-control header_color" required name="theme_settings[1][header_color]" value="{{ $adminTheme->header_color }}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="example">
                                                            <p class="box-title m-t-30">@lang('modules.themeSettings.sidebarColor')</p>
                                                            <input type="text" class="complex-colorpicker sidebar_color form-control" required name="theme_settings[1][sidebar_color]" value="{{ $adminTheme->sidebar_color }}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="example">
                                                            <p class="box-title m-t-30">@lang('modules.themeSettings.sidebarTextColor')</p>
                                                            <input type="text" class="complex-colorpicker sidebar_text_color form-control" required name="theme_settings[1][sidebar_text_color]" value="{{ $adminTheme->sidebar_text_color }}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="example">
                                                            <p class="box-title m-t-30">@lang('modules.themeSettings.linkColor')</p>
                                                            <input type="text" class="complex-colorpicker link_color form-control" required name="theme_settings[1][link_color]" value="{{ $adminTheme->link_color }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>

                                                <h3 class="box-title m-b-0"><b>@lang('modules.themeSettings.employeePanelTheme')</b></h3>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="example">
                                                            <p class="box-title m-t-30">@lang('modules.themeSettings.headerColor')</p>
                                                            <input type="text" class="colorpicker form-control" required name="theme_settings[3][header_color]" value="{{ $employeeTheme->header_color }}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="example">
                                                            <p class="box-title m-t-30">@lang('modules.themeSettings.sidebarColor')</p>
                                                            <input type="text" class="complex-colorpicker form-control" required name="theme_settings[3][sidebar_color]" value="{{ $employeeTheme->sidebar_color }}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="example">
                                                            <p class="box-title m-t-30">@lang('modules.themeSettings.sidebarTextColor')</p>
                                                            <input type="text" class="complex-colorpicker form-control" required name="theme_settings[3][sidebar_text_color]" value="{{ $employeeTheme->sidebar_text_color }}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="example">
                                                            <p class="box-title m-t-30">@lang('modules.themeSettings.linkColor')</p>
                                                            <input type="text" class="complex-colorpicker form-control" required name="theme_settings[3][link_color]" value="{{ $employeeTheme->link_color }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <h3 class="box-title m-b-0"><b>@lang('modules.themeSettings.clientPanelTheme')</b></h3>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="example">
                                                            <p class="box-title m-t-30">@lang('modules.themeSettings.headerColor')</p>
                                                            <input type="text" class="colorpicker form-control" required name="theme_settings[4][header_color]" value="{{ $clientTheme->header_color }}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="example">
                                                            <p class="box-title m-t-30">@lang('modules.themeSettings.sidebarColor')</p>
                                                            <input type="text" class="complex-colorpicker form-control" required name="theme_settings[4][sidebar_color]" value="{{ $clientTheme->sidebar_color }}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="example">
                                                            <p class="box-title m-t-30">@lang('modules.themeSettings.sidebarTextColor')</p>
                                                            <input type="text" class="complex-colorpicker form-control" required name="theme_settings[4][sidebar_text_color]" value="{{ $clientTheme->sidebar_text_color }}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="example">
                                                            <p class="box-title m-t-30">@lang('modules.themeSettings.linkColor')</p>
                                                            <input type="text" class="complex-colorpicker form-control" required name="theme_settings[4][link_color]" value="{{ $clientTheme->link_color }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                            </div>



{{--                                            <h3 class="box-title m-b-0"><b>@lang('modules.themeSettings.customCss')</b></h3>--}}

{{--                                            <div class="row">--}}
{{--                                                <div class="col-md-12 alert alert-info">--}}
{{--                                                   <i class="fa fa-info-circle"></i> @lang('modules.themeSettings.customCssNote')--}}
{{--                                                    <br>--}}
{{--                                                    <br>--}}
{{--                                                    <ol>--}}
{{--                                                        <li>admin-custom.css (css file for admin panel)</li>--}}
{{--                                                        <li>member-custom.css (css file for member panel)</li>--}}
{{--                                                        <li>client-custom.css (css file for client panel)</li>--}}
{{--                                                    </ol>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}

{{--                                            <div class="row">--}}
{{--                                                <div class="form-group">--}}
{{--                                                    <div class="col-xs-12">--}}
{{--                                                        <label for="">@lang('app.adminPanel') @lang('modules.themeSettings.customCss')</label>--}}
{{--                                                        <textarea name="theme_settings[1][user_css]" class="my-code-area" rows="20" style="width: 100%">@if(is_null($adminTheme->user_css))/*@lang('modules.themeSettings.customCssPlaceholder')*/@else {!! $adminTheme->user_css !!} @endif</textarea>--}}

{{--                                                    </div>--}}

{{--                                                    <div class="col-md-12 m-t-20">--}}
{{--                                                        <label for="">@lang('app.employeePanel') @lang('modules.themeSettings.customCss')</label>--}}
{{--                                                        <textarea name="theme_settings[3][user_css]" class="my-code-area" rows="20" style="width: 100%">@if(is_null($employeeTheme->user_css))/*@lang('modules.themeSettings.customCssPlaceholder')*/@else {!! $employeeTheme->user_css !!} @endif</textarea>--}}

{{--                                                    </div>--}}
{{--                                                    <div class="col-md-12 m-t-20">--}}
{{--                                                        <label for="">@lang('app.clientPanel') @lang('modules.themeSettings.customCss')</label>--}}
{{--                                                        <textarea name="theme_settings[4][user_css]" class="my-code-area" rows="20" style="width: 100%">@if(is_null($clientTheme->user_css))/*@lang('modules.themeSettings.customCssPlaceholder')*/@else {!! $clientTheme->user_css !!} @endif</textarea>--}}

{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}

                                            <div class="row">
                                                <div class="col-md-12 m-t-30">
                                                    <button class="btn btn-success" id="save-form" type="submit"><i class="fa fa-check"></i> @lang('app.save')</button>
                                                </div>
                                            </div>
                                            {!! Form::close() !!}
                                        </div>

                                </div>
                            </div>
                            <!-- /.row -->

                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>


    </div>
    <!-- .row -->



@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/jquery-asColorPicker-master/libs/jquery-asColor.js') }}"></script>
<script src="{{ asset('plugins/bower_components/jquery-asColorPicker-master/libs/jquery-asGradient.js') }}"></script>
<script src="{{ asset('plugins/bower_components/jquery-asColorPicker-master/dist/jquery-asColorPicker.min.js') }}"></script>

<script src="{{ asset('plugins/ace/ace.js') }}"></script>
<script src="{{ asset('plugins/ace/theme-twilight.js') }}"></script>
<script src="{{ asset('plugins/ace/mode-css.js') }}"></script>
<script src="{{ asset('plugins/ace/jquery-ace.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/switchery/dist/switchery.min.js') }}"></script>

<script>
    // Colorpicker
 // Switchery
 var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
     $('.js-switch').each(function() {
     new Switchery($(this)[0], $(this).data());

    });
    $(".colorpicker").asColorPicker();
    $(".complex-colorpicker").asColorPicker({
        mode: 'complex'
    });
    $(".gradient-colorpicker").asColorPicker({
        mode: 'gradient'
    });

    // $('#logo_background_color').on('asColorPicker::change', function (e) {
    //     alert('hello');
    // });
    $('.header_color').on('asColorPicker::change', function (e) {
        document.documentElement.style.setProperty('--header_color', e.target.value);
    });

    $('.sidebar_color').on('asColorPicker::change', function (e) {
        document.documentElement.style.setProperty('--sidebar_color', e.target.value);
    });

    $('.sidebar_text_color').on('asColorPicker::change', function (e) {
        document.documentElement.style.setProperty('--sidebar_text_color', e.target.value);
    });

    $('.link_color').on('asColorPicker::change', function (e) {
        document.documentElement.style.setProperty('--link_color', e.target.value);
    });

    $('.my-code-area').ace({ theme: 'twilight', lang: 'css' })

    $('#save-form').click(function () {
        $.easyAjax({
            url: '{{route('admin.theme-settings.store')}}',
            container: '#editSettings',
            data: $('#editSettings').serialize(),
            type: "POST",
            redirect: true
        })
    });

    $('input[name=active_theme]').click(function () {
        var theme = $('input[name=active_theme]:checked').val();

        $.easyAjax({
            url: '{{route('admin.theme-settings.activeTheme')}}',
            type: "POST",
            data: {'_token': '{{ csrf_token() }}', 'active_theme': theme}
        })
    });

    $('input[name=rounded_theme]').click(function () {
        var theme = $('input[name=rounded_theme]:checked').val();

        $.easyAjax({
            url: '{{route('admin.theme-settings.roundedTheme')}}',
            type: "POST",
            data: {'_token': '{{ csrf_token() }}', 'rounded_theme': theme}
        })
    });
    
    $('.rtl').change(function () {
        var rtl  = $('.rtl').is(':checked');
        $.easyAjax({
            url: '{{route('admin.theme-settings.rtlTheme')}}',
            type: "POST",
            data: {'_token': '{{ csrf_token() }}', 'rtl': rtl}
        })
        return false;
    });
</script>
@endpush

