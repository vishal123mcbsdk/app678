<div id="event-detail">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><i class="fa fa-flag"></i> @lang('modules.projects.milestones') @lang('app.details')</h4>
        </div>
        <div class="modal-body">
            {!! Form::open(['id'=>'updateEvent','class'=>'ajax-form','method'=>'GET']) !!}
            <div class="form-body">
                <div class="row">
                    <div class="col-md-12 ">
                        <div class="form-group">
                            <label>@lang('modules.projects.milestoneTitle')</label>
                            <p>
                                {{ $milestone->milestone_title }}
                            </p>
                        </div>
                    </div>
    
                </div>

                <div class="row">
                    @if(!is_null($milestone->currency_id))
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>@lang('modules.projects.milestoneCost')</label>
                                <p>
                                    {{ currency_formatter($milestone->cost,$milestone->currency->currency_symbol) }}

                                    @if($milestone->cost > 0 && $milestone->invoice_created == 0)
                                        <a href="{{ route('admin.all-invoices.convert-milestone', $milestone->id) }}" class="btn btn-xs btn-info btn-rounded m-l-15">@lang('app.create') @lang('app.invoice')</a>
                                    @elseif($milestone->cost > 0 && $milestone->invoice_created == 1)
                                        <a href="{{ route('admin.all-invoices.show', $milestone->invoice_id) }}" class="btn btn-xs btn-info btn-rounded m-l-15">@lang('app.view') @lang('app.invoice')</a>
                                    @endif
                                </p>
                            </div>
                        </div>

                      
                                
                    @endif

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>@lang('app.status')</label>
                            <p>
                                @if($milestone->status == 'incomplete') 
                                    <label class="label label-danger">@lang('app.incomplete')</label>
                                @else
                                    <label class="label label-success">@lang('app.complete')</label>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>@lang('app.deadline')</label>
                            <p> {{ ($milestone->due_date != '') ? $milestone->due_date->format($global->date_format):'--' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                            <div class="form-group">
                                <label>@lang('modules.projects.milestoneSummary')</label>
                                <p>{{ $milestone->summary }}</p>
                            </div>
                    </div>
                    <div class="col-xs-12">
                            <h4>@lang('app.menu.tasks')</h4>
                            <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>@lang('app.task')</th>
                                            <th>@lang('modules.tasks.assignTo')</th>
                                            <th>@lang('modules.tasks.assignBy')</th>
                                            <th>@lang('app.deadline')</th>
                                            <th>@lang('app.status')</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($milestone->tasks as $key=>$item)
                                                <tr>
                                                    <td>{{ ($key+1) }}</td>
                                                    <td>{{ ucfirst($item->heading) }}</td>
                                                    <td> @forelse ($item->users as $index => $user)
                                                        @if($index > 0),<br />@endif
                                                        {{ ucwords($user->name) }} 
                                                    @empty
                                                        --
                                                    @endforelse</td>
                                                    <td>{{ ($item->created_by) ? ucwords($item->create_by->name) : "--" }}</td>
                                                    <td>{{ (!is_null($item->due_date)) ? $item->due_date->format($global->date_format):'--' }}</td>
                                                <td><label class="label" style="background-color: {{ $item->board_column->label_color }}">{{ $item->board_column->column_name }}</label></td>
                                                </tr>
                                                    
                                            @empty
                                            <tr>
                                                    <td colspan="6">@lang('messages.noRecordFound')</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                    </div>
    
                </div>
            </div>
            {!! Form::close() !!}
    
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-white waves-effect" data-dismiss="modal">Close</button>
        </div>
    
    </div>
  