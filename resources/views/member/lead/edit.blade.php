@extends('layouts.member-app')

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
                <li><a href="{{ route('member.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('member.leads.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.edit')</li>
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

@endpush

@section('content')

    <div class="row">
        <div class="col-xs-12">

            <div class="panel panel-inverse">
                <div class="panel-heading"> @lang('modules.lead.updateTitle')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        {!! Form::open(['id'=>'updateLead','class'=>'ajax-form','method'=>'PUT']) !!}
                        <div class="form-body">
                            <h3 class="box-title">@lang('modules.lead.companyDetails')</h3>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.lead.companyName')</label>
                                        <input type="text" id="company_name" name="company_name" class="form-control"  value="{{ $lead->company_name ?? '' }}">
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.lead.website')</label>
                                        <input type="text" id="website" name="website" class="form-control" value="{{ $lead->website ?? '' }}" >
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                            <!--/row-->
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.address')</label>
                                        <textarea name="address"  id="address"  rows="5" class="form-control">{{ $lead->address ?? '' }}</textarea>
                                    </div>
                                </div>
                                <!--/span-->

                            </div>
                            <!--/row-->
                            <div class="row">
                                
                                <div class="col-md-3 ">
                                    <div class="form-group">
                                        <label>@lang('modules.clients.officePhoneNumber')</label>
                                        <input type="text" name="office_phone" id="office_phone" value="{{ $lead->office_phone }}"  class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3 ">
                                        <div class="form-group">
                                            <label>@lang('modules.stripeCustomerAddress.city')</label>
                                            <input type="text" name="city" id="city"  value="{{ $lead->city }}" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-3 ">
                                        <div class="form-group">
                                            <label>@lang('modules.stripeCustomerAddress.state')</label>
                                            <input type="text" name="state" id="state"  value="{{ $lead->state }}" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-3 ">
                                        <div class="form-group">
                                            <label>@lang('modules.stripeCustomerAddress.country')</label>
                                            <input type="text" name="country" id="country" value="{{ $lead->country }}" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                
                                <div class="col-md-3 ">
                                        <div class="form-group">
                                            <label>@lang('modules.stripeCustomerAddress.postalCode')</label>
                                            <input type="text" name="postal_code" id="postalCode" value="{{ $lead->postal_code }}"class="form-control">
                                        </div>
                                    </div>
                                </div>

                            <h3 class="box-title m-t-40">@lang('modules.lead.leadDetails')</h3>
                            <hr>
                            <div class="row">
                                <div class="col-md-6 ">
                                    <div class="form-group">
                                        <label for="">@lang('modules.tickets.chooseAgents')</label>
                                        <select class="select2 form-control" data-placeholder="@lang('modules.tickets.chooseAgents')" name="agent_id">
                                            <option value="">@lang('modules.tickets.chooseAgents')</option>
                                            @foreach($leadAgents as $emp)
                                                <option  @if($emp->id == $lead->agent_id) selected @endif  value="{{ $emp->id }}">{{ ucwords($emp->user->name). ' ['.$emp->user->email.']' }} @if($emp->user->id == $user->id)
                                                        (YOU) @endif</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                            <div class="row">
                                <div class="col-md-6 ">
                                    <div class="form-group">
                                        <label>@lang('modules.lead.clientName')</label>
                                        <input type="text" name="client_name" id="client_name" class="form-control" value="{{ $lead->client_name }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('modules.lead.clientEmail')</label>
                                        <input type="email" name="email" id="client_email" class="form-control" value="{{ $lead->client_email }}">
                                        <span class="help-block">@lang('modules.lead.emailNote')</span>
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                            <div class="row">
                                <!--/span-->

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('modules.lead.mobile')</label>
                                        <input type="tel" name="mobile" id="mobile" value="{{ $lead->mobile }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('app.next_follow_up')</label>
                                        <select name="next_follow_up" id="next_follow_up" class="form-control">
                                            <option @if($lead->next_follow_up == 'yes') selected
                                                    @endif value="yes"> @lang('app.yes')</option>
                                           <option @if($lead->next_follow_up == 'no') selected
                                                    @endif value="no"> @lang('app.no')</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('app.lead') @lang('app.value')</label>
                                        <input type="number" min="0"  name="value" value="{{ $lead->value }}"  class="form-control">
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                            <div class="row">

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('app.status')</label>
                                        <select name="status" id="status" class="form-control">
                                            @forelse($status as $sts)
                                            <option @if($lead->status_id == $sts->id) selected
                                                    @endif value="{{ $sts->id }}"> {{ $sts->type }}</option>
                                            @empty

                                            @endforelse
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('app.source')</label>
                                        <select name="source" id="source" class="form-control">
                                            @forelse($sources as $source)
                                                <option @if($lead->source_id == $source->id) selected
                                                        @endif value="{{ $source->id }}"> {{ $source->type }}</option>
                                            @empty

                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 ">
                                    <div class="form-group">
                                        <label >@lang('modules.lead.leadCategory')
                                         </label>
                                        <select class="select2 form-control" name="category_id" id="category_id"
                                                    data-style="form-control">
                                                    @forelse($categories as $category)
                                                <option value="{{ $category->id }}"
                                                        @if($lead->category_id == $category->id)
                                                        selected
                                                        @endif
                                                >{{ ucwords($category->category_name) }}</option>
                                            @empty
                                                <option value="">@lang('messages.noCategoryAdded')</option>
                                            @endforelse
                                        </select>
                                     </div>
                                </div>
                            </div>
                            <!--/row-->
                            <div class="row">
                                @foreach($fields as $field)
                                    <div class="col-md-4">
                                        <label>{{ ucfirst($field->label) }}</label>
                                        <div class="form-group">
                                            @if( $field->type == 'text')
                                                <input type="text" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" placeholder="{{$field->label}}" value="{{$lead->custom_fields_data['field_'.$field->id] ?? ''}}">
                                            @elseif($field->type == 'password')
                                                <input type="password" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" placeholder="{{$field->label}}" value="{{$lead->custom_fields_data['field_'.$field->id] ?? ''}}">
                                            @elseif($field->type == 'number')
                                                <input type="number" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" placeholder="{{$field->label}}" value="{{$lead->custom_fields_data['field_'.$field->id] ?? ''}}">

                                            @elseif($field->type == 'textarea')
                                                <textarea name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" id="{{$field->name}}" cols="3">{{$lead->custom_fields_data['field_'.$field->id] ?? ''}}</textarea>

                                            @elseif($field->type == 'radio')
                                                <div class="radio-list">
                                                    @foreach($field->values as $key=>$value)
                                                        <label class="radio-inline @if($key == 0) p-0 @endif">
                                                            <div class="radio radio-info">
                                                                <input type="radio" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" id="optionsRadios{{$key.$field->id}}" value="{{$value}}" @if(isset($lead) && $lead->custom_fields_data['field_'.$field->id] == $value) checked @elseif($key==0) checked @endif>>
                                                                <label for="optionsRadios{{$key.$field->id}}">{{$value}}</label>
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @elseif($field->type == 'select')
                                                {!! Form::select('custom_fields_data['.$field->name.'_'.$field->id.']',
                                                        $field->values,
                                                         isset($lead)?$lead->custom_fields_data['field_'.$field->id]:'',['class' => 'form-control gender'])
                                                 !!}

                                            @elseif($field->type == 'checkbox')
                                                <div class="mt-checkbox-inline custom-checkbox checkbox-{{$field->id}}">
                                                    <input type="hidden" name="custom_fields_data[{{$field->name.'_'.$field->id}}]"
                                                           id="{{$field->name.'_'.$field->id}}" value="{{$lead->custom_fields_data['field_'.$field->id]}}">
                                                    @foreach($field->values as $key => $value)
                                                        <label class="mt-checkbox mt-checkbox-outline">
                                                            <input name="{{$field->name.'_'.$field->id}}[]" class="custom_fields_data[{{$field->name.'_'.$field->id}}]"
                                                                   type="checkbox" value="{{$value}}" onchange="checkboxChange('checkbox-{{$field->id}}', '{{$field->name.'_'.$field->id}}')"
                                                                   @if($lead->custom_fields_data['field_'.$field->id] != '' && in_array($value ,explode(', ', $lead->custom_fields_data['field_'.$field->id]))) checked @endif > {{$value}}
                                                            <span></span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @elseif($field->type == 'date')
                                                <input type="text" class="form-control date-picker" size="16" name="custom_fields_data[{{$field->name.'_'.$field->id}}]"
                                                       value="{{ ($lead->custom_fields_data['field_'.$field->id] != '') ? \Carbon\Carbon::parse($lead->custom_fields_data['field_'.$field->id])->format($global->date_format) : \Carbon\Carbon::now()->format($global->date_format)}}">
                                            @endif
                                            <div class="form-control-focus"> </div>
                                            <span class="help-block"></span>

                                        </div>
                                    </div>
                                @endforeach

                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <label>@lang('app.note')</label>
                                    <div class="form-group">
                                        <textarea name="note" id="note" class="form-control summernote" rows="5">{{ $lead->note ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="form-actions">
                            <button type="submit" id="save-form" class="btn btn-success"> <i class="fa fa-check"></i> @lang('app.update')</button>
                            <a href="{{ route('member.leads.index') }}" class="btn btn-default">@lang('app.back')</a>
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
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>

<script type="text/javascript">

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

    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

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
    $(".date-picker").datepicker({
        todayHighlight: true,
        autoclose: true,
        weekStart:'{{ $global->week_start }}',
        format: '{{ $global->date_picker_format }}',
    });
    
    $('#save-form').click(function () {
        $.easyAjax({
            url: '{{route('member.leads.update', [$lead->id])}}',
            container: '#updateLead',
            type: "POST",
            redirect: true,
            data: $('#updateLead').serialize()
        })
    });
</script>
@endpush