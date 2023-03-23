@extends('layouts.sass-app')
@section('content')
    <!-- START Contact Section -->
    <section class="contact-section bg-white sp-100-70">
        <div class="container">
            @if(!is_null($frontDetail->contact_html))
                <div class="row">
                    <div class="col-md-10 mx-auto">
                        {!! $frontDetail->contact_html !!}
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col-md-10 mx-auto">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="contact-info">
                                <div class="mobile-device"><span class="fa fa-home fa-fw style"></span><span class="heading-info">@lang('app.address')</span>
                                    <div class="address-content">{{ $frontDetail->address }}</div>
                                </div>

                                <div class="mobile-device"><span class="fa fa-envelope fa-fw style"></span><span class="heading-info">@lang('app.email')</span>
                                    <div class="address-content">{{ $frontDetail->email }}</div>
                                </div>
                                @if($frontDetail->phone)
                                    <div class="mobile-device"><span class="fa fa-phone fa-fw style"></span><span class="heading-info">@lang('app.phone')</span>
                                        <div class="address-content">{{ $frontDetail->phone }}</div>
                                    </div>
                                @endif
                            </div>
                            {{--<h2>Get in Touch</h2>--}}
                        </div>
                    </div>
                    {!! Form::open(['id'=>'contactUs', 'method'=>'POST']) !!}
                    <div class="row">
                        <div class="alert col-md-12 text-center" id="alert"></div>
                    </div>
                    <div class="row" id="contactUsBox">
                        <div class="form-group mb-4 col-lg-6 col-12">
                            <input type="text" name="name" class="form-control" placeholder="@lang('modules.profile.yourName')"
                                   id="name">
                        </div>
                        <div class="form-group mb-4 col-lg-6 col-12">
                            <input type="email" class="form-control" placeholder="@lang('modules.profile.yourEmail')"
                                   name="email" id="email">
                        </div>
                        <div class="form-group mb-4 col-12">
                            <textarea rows="6" name="message" class="form-control br-10"
                                      placeholder="@lang('modules.messages.message')"
                                      id="message"></textarea>
                        </div>
                        @if($global->google_recaptcha_status  && $global->google_captcha_version=="v2")
                        <div class="form-group {{ $errors->has('g-recaptcha-response') ? 'has-error' : '' }}">
                                <div class="g-recaptcha"
                                     data-sitekey="{{ $global->google_recaptcha_key }}">
                                </div>
                                @if ($errors->has('g-recaptcha-response'))
                                    <div class="help-block with-errors">{{ $errors->first('g-recaptcha-response') }}</div>
                                @endif
                        </div>
                        @endif

                    <input type='hidden' name='recaptcha_token' id='recaptcha_token'>
                        <div class="col-12" style="margin-top: 12px;">
                            <button type="button" class="btn btn-lg btn-custom mt-1" id="contact-submit">
                                {{ $frontMenu->contact_submit }}
                            </button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>

        </div>
    </section>
    <!-- END Contact Section -->
@endsection
@push('footer-script')
    <script>
        $('#contact-submit').click(function () {

            @if($global->google_recaptcha_status  && $global->google_recaptcha_status =="v2")
            if (grecaptcha.getResponse().length == 0) {
                alert('Please click the reCAPTCHA checkbox');
                return false;
            }
            @endif

            $.easyAjax({
                url: '{{route('front.contact-us')}}',
                container: '#contactUs',
                type: "POST",
                data: $('#contactUs').serialize(),
                messagePosition: "inline",
                success: function (response) {
                    if (response.status === 'success') {
                        $('#contactUsBox').remove();
                    }
                }
            })
        });
    </script>
    @if($global->google_recaptcha_status  && $global->google_captcha_version=="v3")
    <script src="https://www.google.com/recaptcha/api.js?render={{ $global->google_recaptcha_key }}"></script>

    <script>
        setTimeout(function () {

            grecaptcha.ready(function () {
                grecaptcha.execute('{{ $global->google_recaptcha_key }}', {action: 'submit'}).then(function (token) {
                    document.getElementById("recaptcha_token").value = token;
                });
            });

        }, 3000);

    </script>
@endif
@endpush
