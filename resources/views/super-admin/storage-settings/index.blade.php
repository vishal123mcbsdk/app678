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
            <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
            <li class="active">{{ __($pageTitle) }}</li>
        </ol>
    </div>
    <!-- /.breadcrumb -->
</div>
@endsection


@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
@endpush

@section('content')

<div class="row">
    <div class="col-xs-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">@lang('app.menu.storageSettings')</div>

            <div class="vtabs customvtab m-t-10">
                @include('sections.super_admin_setting_menu')

                <div class="tab-content">
                    <div id="vhome3" class="tab-pane active">

                        <div class="row">
                            <div class="col-xs-12">

                                <div class="row">
                                    <div class="col-sm-12 col-xs-12 ">
                                        {!! Form::open(['id'=>'updateSettings','class'=>'ajax-form','method'=>'POST'])
                                        !!}
                                        <div class="form-body">
                                            <div class="row">

                                                <div class="col-xs-12">
                                                    <div class="form-group">
                                                        <label class="control-label">Select Storage</label>
                                                        <select class="select2 form-control" id="storage"
                                                            name="storage">
                                                            <option value="local" @if(isset($localCredentials) &&
                                                                $localCredentials->status == 'enabled') selected
                                                                @endif>Local (Default)</option>
                                                            <option value="aws" @if(isset($awsCredentials) &&
                                                                $awsCredentials->status == 'enabled') selected
                                                                @endif>AWS S3 Storage (Amazon Web Services S3)</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-12 aws-form">
                                                    <div class="form-group">
                                                        <label>AWS Key</label>
                                                        <input type="text" class="form-control" name="aws_key"
                                                            @if(isset($awsCredentials) && isset($awsCredentials->key))
                                                        value="{{ $awsCredentials->key }}" @endif>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>AWS Secret</label>
                                                        <input type="text" class="form-control" name="aws_secret"
                                                            @if(isset($awsCredentials) &&
                                                            isset($awsCredentials->secret))
                                                        value="{{ $awsCredentials->secret }}" @endif>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>AWS Region</label>
                                                        <select class="select2 form-control" name="aws_region">
                                                            @foreach (\App\StorageSetting::$awsRegions as $key =>
                                                            $region)

                                                            <option @if(isset($awsCredentials) && $awsCredentials->
                                                                region == $key) selected @endif
                                                                value="{{$key}}">{{  $region }}</option>

                                                            @endforeach

                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>AWS Bucket</label>
                                                        <input type="text" class="form-control" name="aws_bucket"
                                                            @if(isset($awsCredentials) &&
                                                            isset($awsCredentials->bucket))
                                                        value="{{ $awsCredentials->bucket }}" @endif>
                                                    </div>
                                                </div>

                                            </div>

                                            <!--/row-->

                                        </div>
                                        <div class="form-actions">
                                            <button type="submit" id="save-form-2" class="btn btn-success"><i
                                                    class="fa fa-check"></i>
                                                @lang('app.save')
                                            </button>

                                            <button type="button" id="test-aws" class="aws-form btn btn-primary">Test
                                                AWS</button>

                                        </div>



                                        {!! Form::close() !!}
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
<div class="modal fade bs-modal-md in" id="testMailModal" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md" id="modal-data-application">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Test AWS Setting</h4>
            </div>
            <div class="modal-body">
                {!! Form::open(['id'=>'testEmail','class'=>'ajax-form','method'=>'POST']) !!}
                <div class="form-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label>Upload file to test if its getting uploaded to aws bucket</label>
                                <input type="file" name="file" id="file">
                            </div>
                        </div>
                        <!--/span-->
                    </div>
                    <!--/row-->
                </div>
                <div class="form-actions">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="test-aws-submit">submit</button>
                    <a target="_blank" class="btn btn-info" href="" id="show-file">View File</a>
                </div>
                {!! Form::close() !!}
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->.
    </div>

</div> {{--Ajax Modal Ends--}}

@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script>
    $('#test-aws').click(function () {
            $('#testMailModal').modal('show');
            $('#show-file').hide();

    });

    $(".select2").select2({
            formatNoMatches: function () {
                return "{{ __('messages.noRecordFound') }}";
            }
        });

        $(function () {
           var type = $('#storage').val();
            if (type == 'aws') {
                $('.aws-form').show();
            } else if(type == 'local') {
                $('.aws-form').hide();
            }
        });

        $('#storage').on('change', function(event) {
            event.preventDefault();
            var type = $(this).val();
            if (type == 'aws') {
                $('.aws-form').show();
            } else if(type == 'local') {
                $('.aws-form').hide();
            }
        });

        $('#save-form-2').click(function () {
            $.easyAjax({
                url: '{{ route('super-admin.storage-settings.store')}}',
                container: '#updateSettings',
                type: "POST",
                redirect: true,
                data: $('#updateSettings').serialize(),
                success: function(response){
                    if(response.status == 'success' && response.storage =='aws') {
                        $('.aws-form').show();
                    }
                 }
            })
        });

        $('#test-aws-submit').click(function () {
            $.easyAjax({
                url: '{{route('super-admin.storage-settings.awstest')}}',
                type: "POST",
                file:true,
                messagePosition: "inline",
                container: "#testEmail",
                data: $('#testEmail').serialize(),
                success: function(response){
                    if(response.status == 'success') {
                        $('#show-file').show();
                        $("#show-file").attr("href", response.fileurl);
                    }
                 }

            })
        });
</script>
@endpush