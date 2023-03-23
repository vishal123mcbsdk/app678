<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ $superadmin->favicon_url }}">
    {{--<link rel="manifest" href="{{ asset('favicon/manifest.json') }}">--}}
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ $superadmin->favicon_url }}">
    <meta name="theme-color" content="#ffffff">

    <title>Admin Panel | {{ __($pageTitle) }}</title>
    <!-- Bootstrap Core CSS -->
    <link href="{{ asset('bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel='stylesheet prefetch'
          href='https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css'>
    <link rel='stylesheet prefetch'
          href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/css/bootstrap-select.min.css'>

    <!-- This is Sidebar menu CSS -->
    <link href="{{ asset('plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">

    <link href="{{ asset('plugins/bower_components/toast-master/css/jquery.toast.css') }}"   rel="stylesheet">
    <link href="{{ asset('plugins/bower_components/sweetalert/sweetalert.css') }}"   rel="stylesheet">

    <!-- This is a Animation CSS -->
    <link href="{{ asset('css/animate.css') }}" rel="stylesheet">

    <!-- This is a Custom CSS -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <!-- color CSS you can use different color css from css/colors folder -->
    <!-- We have chosen the skin-blue (default.css) for this starter
       page. However, you can choose any other skin from folder css / colors .
       -->
    <link href="{{ asset('css/colors/default.css') }}" id="theme"  rel="stylesheet">
    <link href="{{ asset('plugins/froiden-helper/helper.css') }}"   rel="stylesheet">
    <link href="{{ asset('css/custom-new.css') }}"   rel="stylesheet">
    <link href="{{ asset('css/rounded.css') }}"   rel="stylesheet">


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <style>
        .sidebar .notify  {
            margin: 0 !important;
        }
        .sidebar .notify .heartbit {
            top: -23px !important;
            right: -15px !important;
        }
        .sidebar .notify .point {
            top: -13px !important;
        }

        .wrapper {
            position: relative;
            width: 100%;
            height: 170px;
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .signature-pad {
            position: absolute;
            left: 0;
            top: 0;
            width:100%;
            height: 100%;
            background-color: white;
        }
        .tabs-style-line nav a {
            box-shadow: unset !important;
        }
        .discussion-action-button {
            display: none;
        }
        .discussion-action-button {
            display: none;
        }
        .sl-right:hover .discussion-action-button {
            display: block;
        }
    </style>
</head>
<body class="fix-sidebar">
<!-- Preloader -->
<div class="preloader">
    <div class="cssload-speeding-wheel"></div>
</div>
<div id="wrapper">

    <!-- Left navbar-header end -->
    <!-- Page Content -->
    <div id="page-wrapper" style="margin-left: 0px !important;">
        <div class="container">

            <!-- .row -->
            <div class="row" style="margin-top: 70px; !important;">

                <div class="col-xs-12" id="estimates">
                    <div class="row m-b-20">
                        <div class="col-xs-12">
                            <div class="visible-xs">
                                <div class="clearfix"></div>
                            </div>
                            @if(!$contract->signature)
                                <button type="button" id="accept_action" class="btn btn-success pull-right m-r-10" onclick="sign();return false;"><i class="fa fa-check"></i> @lang('app.sign')</button>
                            @else
                                <button class="btn btn-default pull-right m-r-10 disabled"><i class="fa fa-check"></i> @lang('app.signed')</button>
                            @endif

                            <a href="{{ route("front.contract.download", $contract->id) }}" class="btn btn-default pull-right m-r-10"><i class="fa fa-file-pdf-o"></i> @lang('app.download')</a>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="white-box printableArea" style="background: #ffffff !important;">
                                <div class="sttabs tabs-style-line" id="invoice_container">
                                    <nav>
                                        <ul class="customtab" role="tablist" id="myTab">
                                            <li class="nav-item active"><a class="nav-link" href="#summery" data-toggle="tab" role="tab"><span><i class="glyphicon glyphicon-file"></i> @lang('modules.contracts.summery')</span></a>
                                            </li>

{{--                                            <li class="nav-item"><a class="nav-link" href="#discussion" data-toggle="tab" role="tab"><span><i class="glyphicon glyphicon-comment"></i> @lang('modules.contracts.discussion')</span></a></li>--}}
                                        </ul>
                                    </nav>

                                    <div class="tab-content tabcontent-border">
                                        <div class="tab-pane active" id="summery" role="tabpanel">
                                            <div class="row p-20">
                                                <div class="col-md-8">
                                                    {!! $contract->contract_detail !!}
                                                </div>
                                                <div class="col-md-4 p-l-30">
                                                    <div class="card p-20">
                                                        <div class="card-body">
                                                            <address>
                                                                <h3><b class="text-danger">{{ ucwords($global->company_name) }}</b></h3>
                                                                <p class="text-muted">{!! nl2br($global->address) !!}</p>
                                                            </address>
                                                            <h3>@lang('modules.contracts.contractValue'): {{ currency_position($contract->amount,$global->currency->currency_symbol) }}</h3>

                                                            <table>
                                                                <tr>
                                                                    <td># @lang('modules.contracts.contractNumber')</td>
                                                                    <td>{{ $contract->id }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>@lang('modules.projects.startDate')</td>
                                                                    <td>{{ $contract->start_date->format($global->date_format) }}</td>
                                                                </tr>
                                                                @if($contract->end_date != null)
                                                                <tr>
                                                                    <td>@lang('modules.contracts.endDate')</td>
                                                                    <td>{{ $contract->end_date->format($global->date_format) }}</td>
                                                                </tr>
                                                                @endif
                                                                <tr>
                                                                    <td>@lang('modules.contracts.contractType')</td>
                                                                    <td>{{$contract->contract_type ? $contract->contract_type->name : ''}}</td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>

        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- /#page-wrapper -->


    {{--Timer Modal--}}
    <div class="modal fade bs-modal-md in" id="estimateAccept" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                    <button type="button" class="btn blue">Accept</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    {{--Timer Modal Ends--}}
</div>
<!-- /#wrapper -->

<!-- jQuery -->
<script src="{{ asset('plugins/bower_components/jquery/dist/jquery.min.js') }}"></script>
<!-- Bootstrap Core JavaScript -->
<script src="{{ asset('bootstrap/dist/js/bootstrap.min.js') }}"></script>

<!-- Sidebar menu plugin JavaScript -->
<script src="{{ asset('plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js') }}"></script>
<!--Slimscroll JavaScript For custom scroll-->
<script src="{{ asset('js/jquery.slimscroll.js') }}"></script>
<!--Wave Effects -->
<script src="{{ asset('js/waves.js') }}"></script>
<!-- Custom Theme JavaScript -->
<script src="{{ asset('plugins/bower_components/sweetalert/sweetalert.min.js') }}"></script>
<script src="{{ asset('js/custom.min.js') }}"></script>
<script src="{{ asset('js/jasny-bootstrap.js') }}"></script>
<script src="{{ asset('plugins/froiden-helper/helper.js') }}"></script>
<script src="{{ asset('plugins/bower_components/toast-master/js/jquery.toast.js') }}"></script>

{{--sticky note script--}}
<script src="{{ asset('js/cbpFWTabs.js') }}"></script>
<script src="{{ asset('plugins/bower_components/icheck/icheck.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/icheck/icheck.init.js') }}"></script>
<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>

<script>
    $(document).ready(() => {
        let url = location.href.replace(/\/$/, "");

        if (location.hash) {
            const hash = url.split("#");
            $('#myTab a[href="#'+hash[1]+'"]').tab("show");
            url = location.href.replace(/\/#/, "#");
            history.replaceState(null, null, url);
            setTimeout(() => {
                $(window).scrollTop(0);
            }, 400);
        }

        $('a[data-toggle="tab"]').on("click", function() {
            let newUrl;
            const hash = $(this).attr("href");
            if(hash == "#summery") {
                newUrl = url.split("#")[0];
            } else {
                newUrl = url.split("#")[0] + hash;
            }
            // newUrl += "/";
            history.replaceState(null, null, newUrl);
        });
    });

    //Accept estimate
    function sign() {
        var url = '{{ route('front.contract.sign-modal', $contract->id) }}';
        $.ajaxModal('#estimateAccept', url);
    }
</script>

</body>
</html>
