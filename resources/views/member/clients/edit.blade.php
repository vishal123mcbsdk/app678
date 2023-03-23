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
                <li><a href="{{ route('member.clients.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.edit')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
@endpush

@section('content')

    <div class="row">
        <div class="col-xs-12">

            <div class="panel panel-inverse">
                <div class="panel-heading"> @lang('modules.client.updateTitle')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        {!! Form::open(['id'=>'updateClient','class'=>'ajax-form','method'=>'PUT']) !!}
                        <div class="form-body">
                            <h3 class="box-title">@lang('modules.client.companyDetails')</h3>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.client.companyName')</label>
                                        <input type="text" id="company_name" name="company_name" class="form-control"  value="{{ $clientDetail->company_name ?? '' }}">
                                    </div>
                                </div>
                                <!--/span-->
                                @php $website = explode("://",$clientDetail->website);
                                @endphp

                                <div class="col-md-1 ">
                                    <div class="form-group salutation" style="margin-top: 23px">
                                        <select name="hyper_text" id="hyper_text" class="form-control">
                                            <option value="">--</option>
                                            <option value="http://"  @if(isset($website) && $website[0] == 'http' ) selected @endif  >http://</option>
                                            <option value="https://"@if(isset($website) && $website[0] == 'https' ) selected @endif  >https://</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.client.website')</label>
                                        <input type="text" id="website" name="website" class="form-control" value="{{ $clientDetail->website ?? '' }}" >
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                            <!--/row-->
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.address')</label>
                                        <textarea name="address"  id="address"  rows="5" class="form-control">{{ $clientDetail->address ?? '' }}</textarea>
                                    </div>
                                </div>
                                <!--/span-->

                            </div>
                            <!--/row-->

                            <h3 class="box-title m-t-40">@lang('modules.client.clientDetails')</h3>
                            <hr>
                            <div class="row">
                                <div class="col-md-6 ">
                                    <div class="form-group">
                                        <label>@lang('modules.client.clientName')</label>
                                        <input type="text" name="name" id="name" class="form-control" value="{{ $clientDetail->name }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('modules.client.clientEmail')</label>
                                        <input type="email" name="email" id="email" class="form-control" value="{{ $clientDetail->email }}">
                                        <span class="help-block">@lang('modules.client.emailNote')</span>
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                            <div class="row">

                                

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('modules.client.mobile')</label>
                                        <input type="tel" name="mobile" id="mobile" class="form-control" value="{{ $userDetail->mobile }}">
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                            <!--/row-->

                            <div class="row">

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Skype</label>
                                        <input type="text" name="skype" id="skype" class="form-control" value="{{ $clientDetail->skype ?? '' }}">
                                    </div>
                                </div>
                                <!--/span-->

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Linkedin</label>
                                        <input type="text" name="linkedin" id="linkedin" class="form-control" value="{{ $clientDetail->linkedin ?? '' }}">
                                    </div>
                                </div>
                                <!--/span-->

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Twitter</label>
                                        <input type="text" name="twitter" id="twitter" class="form-control" value="{{ $clientDetail->twitter ?? '' }}">
                                    </div>
                                </div>
                                <!--/span-->

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Facebook</label>
                                        <input type="text" name="facebook" id="facebook" class="form-control" value="{{ $clientDetail->facebook ?? '' }}">
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                            <!--/row-->
                            <!--row gst number-->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gst_number">@lang('app.gstNumber')</label>
                                        <input type="text" id="gst_number" name="gst_number" class="form-control" value="{{ $clientDetail->gst_number ?? '' }}">
                                    </div>
                                </div>
                            </div>
                            <!--/row-->
                            <div class="row">
                                @if(isset($fields))
                                    @foreach($fields as $field)
                                        <div class="col-md-6">
                                            <label>{{ ucfirst($field->label) }}</label>
                                            <div class="form-group">
                                                @if( $field->type == 'text')
                                                    <input type="text" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" placeholder="{{$field->label}}" value="{{$clientDetail->custom_fields_data['field_'.$field->id] ?? ''}}">
                                                @elseif($field->type == 'password')
                                                    <input type="password" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" placeholder="{{$field->label}}" value="{{$clientDetail->custom_fields_data['field_'.$field->id] ?? ''}}">
                                                @elseif($field->type == 'number')
                                                    <input type="number" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" placeholder="{{$field->label}}" value="{{$clientDetail->custom_fields_data['field_'.$field->id] ?? ''}}">

                                                @elseif($field->type == 'textarea')
                                                    <textarea name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" id="{{$field->name}}" cols="3">{{$clientDetail->custom_fields_data['field_'.$field->id] ?? ''}}</textarea>

                                                @elseif($field->type == 'radio')
                                                    <div class="radio-list">
                                                        @foreach($field->values as $key=>$value)
                                                            <label class="radio-inline @if($key == 0) p-0 @endif">
                                                                <div class="radio radio-info">
                                                                    <input type="radio" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" id="optionsRadios{{$key.$field->id}}" value="{{$value}}" @if(isset($clientDetail) && $clientDetail->custom_fields_data['field_'.$field->id] == $value) checked @elseif($key==0) checked @endif>>
                                                                    <label for="optionsRadios{{$key.$field->id}}">{{$value}}</label>
                                                                </div>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                @elseif($field->type == 'select')
                                                    {!! Form::select('custom_fields_data['.$field->name.'_'.$field->id.']',
                                                            $field->values,
                                                             isset($clientDetail)?$clientDetail->custom_fields_data['field_'.$field->id]:'',['class' => 'form-control gender'])
                                                     !!}

                                                @elseif($field->type == 'checkbox')
                                                <div class="mt-checkbox-inline custom-checkbox checkbox-{{$field->id}}">
                                                    <input type="hidden" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" 
                                                    id="{{$field->name.'_'.$field->id}}" value="{{$clientDetail->custom_fields_data['field_'.$field->id]}}">
                                                    @foreach($field->values as $key => $value)
                                                        <label class="mt-checkbox mt-checkbox-outline">
                                                            <input name="{{$field->name.'_'.$field->id}}[]" class="custom_fields_data[{{$field->name.'_'.$field->id}}]"
                                                                   type="checkbox" value="{{$value}}" onchange="checkboxChange('checkbox-{{$field->id}}', '{{$field->name.'_'.$field->id}}')"
                                                                   @if($clientDetail->custom_fields_data['field_'.$field->id] != '' && in_array($value ,explode(', ', $clientDetail->custom_fields_data['field_'.$field->id]))) checked @endif > {{$value}}
                                                            <span></span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                                @elseif($field->type == 'date')
                                                    <input type="text" class="form-control date-picker" size="16" name="custom_fields_data[{{$field->name.'_'.$field->id}}]"
                                                           value="{{ isset($employeeDetail->custom_fields_data['field_'.$field->id])?Carbon\Carbon::createFromFormat('m/d/Y', $employeeDetail->custom_fields_data['field_'.$field->id])->format('m/d/Y'):Carbon\Carbon::now()->format($global->date_format)}}">
                                                @endif
                                                <div class="form-control-focus"> </div>
                                                <span class="help-block"></span>

                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <label>@lang('app.shippingAddress')</label>
                                    <div class="form-group">
                                        <textarea name="shipping_address" id="shipping_address" class="form-control" rows="4">{{$clientDetail->shipping_address}}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <label>@lang('app.note')</label>
                                    <div class="form-group">
                                        <textarea name="note" id="note" class="form-control" rows="5">{{ $clientDetail->note ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="form-actions">
                            <button type="submit" id="save-form" class="btn btn-success"> <i class="fa fa-check"></i> @lang('app.update')</button>
                            <a href="{{ route('member.clients.index') }}" class="btn btn-default">@lang('app.back')</a>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>    <!-- .row -->

@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script>
    function checkboxChange(parentClass, id){
        var checkedData = '';
        $('.'+parentClass).find("input[type= 'checkbox']:checked").each(function () {
            if(checkedData !== ''){
                checkedData = checkedData+', '+$(this).val();
            }
            else{
                checkedData = $(this).val();
            }
        });
        $('#'+id).val(checkedData);
    }

    $(".date-picker").datepicker({
        todayHighlight: true,
        autoclose: true,
        format: '{{ $global->date_picker_format }}',
    });
    
    $('#save-form').click(function () {
        $.easyAjax({
            url: '{{route('member.clients.update', [$userDetail->id])}}',
            container: '#updateClient',
            type: "POST",
            redirect: true,
            data: $('#updateClient').serialize()
        })
    });
</script>
@endpush