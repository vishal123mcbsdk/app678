@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12 bg-title-left">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-6 col-sm-8 col-md-8 col-xs-12 bg-title-right">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('admin.leads.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.addNew')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
<style>
    .salutation .form-control {
        padding: 2px 2px;
      }
   </style>
@endpush

@section('content')

    <div class="row">
        <div class="col-xs-12">

            <div class="panel panel-inverse">
                <div class="panel-heading"> @lang('modules.lead.createTitle')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        {!! Form::open(['id'=>'createLead','class'=>'ajax-form','method'=>'POST']) !!}
                            <div class="form-body">
                                <h3 class="box-title">@lang('modules.lead.companyDetails')</h3>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">@lang('modules.lead.companyName')</label>
                                            <input type="text" id="company_name" name="company_name" class="form-control" >
                                        </div>
                                    </div>
                                    <!--/span-->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">@lang('modules.lead.website')</label>
                                            <input type="text" id="website" name="website" class="form-control" >
                                        </div>
                                    </div>
                                    <!--/span-->
                                </div>
                                <!--/row-->
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label class="control-label">@lang('app.address')</label>
                                            <textarea name="address"  id="address"  rows="5" class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <!--/span-->

                                </div>
                                <!--/row-->
                                <div class="row">
                                <div class="col-md-3">
                                    <label>@lang('modules.lead.mobile')</label>
                                        <div class="form-group" style="
                                        display: flex;">
                                                <select class="select2 phone_country_code form-control" name="phone_code"
                                                style=" border-right-top-radius: 0px;border-top-right-radius: 0px !important;
                                                border-bottom-right-radius: 0px !important;">
                                                <option value ="">--</option>
                                               @foreach ($countries as $item)
                                                    <option value="{{ $item->phonecode }}">+{{ $item->phonecode.' ('.$item->iso.')' }}</option>
                                                @endforeach
                                            </select>
                                            <input type="tel" name="mobile" id="mobile" class="form-control" style=" border-top-left-radius: 0px;
                                            border-bottom-left-radius: 0px;">
                                        </div>
                                    </div>
                                    <div class="col-md-3 ">
                                        <div class="form-group">
                                            <label>@lang('modules.clients.officePhoneNumber')</label>
                                            <input type="text" name="office_phone" id="office_phone"   class="form-control">
                                        </div>
                                    </div>
                                <div class="col-md-3 ">
                                        <div class="form-group">
                                            <label>@lang('modules.stripeCustomerAddress.city')</label>
                                            <input type="text" name="city" id="city"  class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-3 ">
                                        <div class="form-group">
                                            <label>@lang('modules.stripeCustomerAddress.state')</label>
                                            <input type="text" name="state" id="state"   class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                <div class="col-md-3 ">
                                        <div class="form-group">
                                            <label>@lang('modules.stripeCustomerAddress.country')</label>
                                            <input type="text" name="country" id="country"  class="form-control">
                                        </div>
                                    </div>
                                <div class="col-md-3 ">
                                        <div class="form-group">
                                            <label>@lang('modules.stripeCustomerAddress.postalCode')</label>
                                            <input type="text" name="postal_code" id="postalCode"class="form-control">
                                        </div>
                                    </div>
                                </div>


                                <h3 class="box-title m-t-40">@lang('modules.lead.leadDetails')</h3>
                                <hr>

                                <div class="row">
                                <div class="col-md-1 ">
                                        <div class="form-group salutation" style="margin-top: 23px">
                                        <select name="salutation" id="salutation" class="form-control">
                                            <option value="">@lang('app.select')</option>
                                            <option value="mr">@lang('app.mr')</option>
                                            <option value="mrs">@lang('app.mrs')</option>
                                            <option value="miss">@lang('app.miss')</option>
                                            <option value="dr">@lang('app.dr')</option>
                                            <option value="sir">@lang('app.sir')</option>
                                            <option value="madam">@lang('app.madam')</option>
                                        </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="required">@lang('app.name')</label>
                                            <input type="text" name="name" id="name"  class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="required">@lang('app.email')</label>
                                            <input type="email" name="email" id="email"  class="form-control">
                                        </div>
                                    </div>

                                   
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('app.next_follow_up')</label>
                                            <select name="next_follow_up" id="next_follow_up" class="form-control">
                                                <option value="yes"> @lang('app.yes')</option>
                                                <option value="no"> @lang('app.no')</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!--/span-->
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('app.lead') @lang('app.value')</label>
                                            <input type="number" min="0" value="0" name="value" id="value"  class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">@lang('modules.tickets.chooseAgents') 
                                                <a href="javascript:;"  id="addLeadAgent" class="btn btn-xs btn-outline btn-success"><i
                                                            class="fa fa-plus"></i> @lang('app.add') @lang('app.leadAgent')</a></label>
                                            <select class="select2 form-control" data-placeholder="@lang('modules.tickets.chooseAgents')" id="agent_id" name="agent_id">
                                                <option value="">@lang('modules.tickets.chooseAgents')</option>
                                                @foreach($leadAgents as $emp)
                                                    <option value="{{ $emp->id }}">{{ ucwords($emp->user->name) }} @if($emp->user->id == $user->id)
                                                            (YOU) @endif</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">@lang('modules.lead.leadSource')  <a href="javascript:;"
                                                                                               id="addLeadsource"
                                                                                               class="btn btn-xs btn-outline btn-success"><i
                                                            class="fa fa-plus"></i> @lang('app.add') @lang('modules.lead.leadSource')</a></label>
                                            <select class="select2 form-control" data-placeholder="@lang('modules.lead.leadSource')"  id="source_id" name="source_id">
                                                @foreach($sources as $source)
                                                    <option value="{{ $source->id }}">{{ ucwords($source->type) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!--/span-->
                                </div>
                                <div class="row">
                                    <div class="col-md-4 ">
                                             <div class="form-group" style="margin-top: 7px;">
                                              <label >@lang('modules.lead.leadCategory')
                                                <a href="javascript:;" id="addLeadCategory" class="btn btn-xs btn-success btn-outline"><i class="fa fa-plus"></i></a>
                                                 </label>
                                            <select class="select2 form-control" name="category_id" id="category_id"
                                                data-style="form-control">
                                                @forelse($categories as $category)
                                                <option value="{{ $category->id }}">{{ ucwords($category->category_name) }}</option>
                                            @empty
                                                <option value="">@lang('messages.noCategoryAdded')</option>
                                            @endforelse

                                             </select>
                                         </div>
                                    </div>
                                <div class="row">
                                    @if(isset($fields))
                                        @foreach($fields as $field)
                                            <div class="col-md-6">
    
                                                <div class="form-group">
                                                    <label @if($field->required == 'yes') class="required" @endif>{{ ucfirst($field->label) }}</label>
                                                    @if( $field->type == 'text')
                                                        <input type="text" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" placeholder="{{$field->label}}" value="{{$editUser->custom_fields_data['field_'.$field->id] ?? ''}}">
                                                    @elseif($field->type == 'password')
                                                        <input type="password" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" placeholder="{{$field->label}}" value="{{$editUser->custom_fields_data['field_'.$field->id] ?? ''}}">
                                                    @elseif($field->type == 'number')
                                                        <input type="number" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" placeholder="{{$field->label}}" value="{{$editUser->custom_fields_data['field_'.$field->id] ?? ''}}">
    
                                                    @elseif($field->type == 'textarea')
                                                        <textarea name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" id="{{$field->name}}" cols="3">{{$editUser->custom_fields_data['field_'.$field->id] ?? ''}}</textarea>
    
                                                    @elseif($field->type == 'radio')
                                                        <div class="radio-list">
                                                            @foreach($field->values as $key=>$value)
                                                                <label class="radio-inline @if($key == 0) p-0 @endif">
                                                                    <div class="radio radio-info">
                                                                        <input type="radio" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" id="optionsRadios{{$key.$field->id}}" value="{{$value}}" @if(isset($editUser) && $editUser->custom_fields_data['field_'.$field->id] == $value) checked @elseif($key==0) checked @endif>>
                                                                        <label for="optionsRadios{{$key.$field->id}}">{{$value}}</label>
                                                                    </div>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    @elseif($field->type == 'select')
                                                        {!! Form::select('custom_fields_data['.$field->name.'_'.$field->id.']',
                                                                $field->values,
                                                                 isset($editUser)?$editUser->custom_fields_data['field_'.$field->id]:'',['class' => 'form-control gender'])
                                                         !!}
    
                                                    @elseif($field->type == 'checkbox')
                                                    <div class="mt-checkbox-inline custom-checkbox checkbox-{{$field->id}}">
                                                        <input type="hidden" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" 
                                                        id="{{$field->name.'_'.$field->id}}" value=" ">
                                                        @foreach($field->values as $key => $value)
                                                            <label class="mt-checkbox mt-checkbox-outline">
                                                                <input name="{{$field->name.'_'.$field->id}}[]"
                                                                       type="checkbox" onchange="checkboxChange('checkbox-{{$field->id}}', '{{$field->name.'_'.$field->id}}')" value="{{$value}}"> {{$value}}
                                                                <span></span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                    @elseif($field->type == 'date')
                                                        <input type="text" class="form-control date-picker" size="16" name="custom_fields_data[{{$field->name.'_'.$field->id}}]"
                                                               value="{{ isset($editUser->dob)?Carbon\Carbon::parse($editUser->dob)->format('Y-m-d'):Carbon\Carbon::now()->format($global->date_format)}}">
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
                                        <label>@lang('app.note')</label>
                                        <div class="form-group">
                                            <textarea name="note" id="note" class="form-control summernote" rows="5"></textarea>
                                        </div>
                                    </div>
                                </div>


                            </div>
                            <div class="form-actions">
                                <button type="submit" id="save-form" class="btn btn-success"> <i class="fa fa-check"></i> @lang('app.save')</button>

                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>    <!-- .row -->
    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="projectCategoryModal" role="dialog" aria-labelledby="myModalLabel"
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
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>

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

    $('#createLead').on('click', '#addLeadAgent', function () {
        var url = '{{ route('admin.lead-agent-settings.create')}}';
        $('#modelHeading').html('Manage Lead Agent');
        $.ajaxModal('#projectCategoryModal', url);
    })
    $('.summernote').summernote({
        height: 200,                 // set editor height
        minHeight: null,             // set minimum height of editor
        maxHeight: null,             // set maximum height of editor
        focus: false,
        toolbar: [
            // [groupName, [list of button]]
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['fontsize', ['fontsize']],
            ['para', ['ul', 'ol', 'paragraph']],
            ["view", ["fullscreen"]]
        ]
    });


    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });
    $(".date-picker").datepicker({
        todayHighlight: true,
        autoclose: true,
        weekStart:'{{ $global->week_start }}',
        format: '{{ $global->date_picker_format }}',
    });

    $('#createLead').on('click', '#addLeadsource', function () {
        var url = '{{ route('admin.lead-source-settings.create')}}';
        $('#modelHeading').html('Manage Lead Source');
        $.ajaxModal('#projectCategoryModal', url);
    })

    $('#save-form').click(function () {
        $.easyAjax({
            url: '{{route('admin.leads.store')}}',
            container: '#createLead',
            type: "POST",
            redirect: true,
            data: $('#createLead').serialize()
        })
    });
    $('#addLeadCategory').click(function () {
        var url = '{{ route('admin.leadCategory.create')}}';
        $('#modelHeading').html('...');
        $.ajaxModal('#projectCategoryModal', url);
    })

</script>
@endpush

