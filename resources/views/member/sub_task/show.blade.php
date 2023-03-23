@foreach($subTasks as $subtask)
    @php $subTaskCount = (isset($subtask->files)) ? $subtask->files->count() : 0; @endphp
    <li class="list-group-item row subTaskFileDiv{{$subtask->id}} @if($subTaskCount > 0) nonBottomBorder @endif">
        <div class="col-xs-9">
            <div class="checkbox checkbox-success checkbox-circle task-checkbox">
                <input class="task-check" data-sub-task-id="{{ $subtask->id }}" id="checkbox{{ $subtask->id }}" type="checkbox"
                       @if($subtask->status == 'complete') checked @endif>
                <label for="checkbox{{ $subtask->id }}">&nbsp;</label>
                <span>{{ ucfirst($subtask->title) }}</span>
            </div>
            @if($subtask->due_date)<span class="text-muted m-l-5"> - @lang('modules.invoices.due'): {{ $subtask->due_date->format($global->date_format) }}</span>@endif
        </div>

        <div class="col-xs-3 text-right">
            <a href="javascript:;" data-sub-task-id="{{ $subtask->id }}" title="@lang('app.edit')" class="edit-sub-task"><i class="fa fa-pencil"></i></a>&nbsp;
            <a href="javascript:;" data-sub-task-id="{{ $subtask->id }}"  title="@lang('app.delete')"  class="delete-sub-task"><i class="fa fa-trash"></i></a>
        </div>
        
    </li>

@endforeach
