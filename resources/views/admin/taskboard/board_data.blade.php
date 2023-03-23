@foreach($boardColumns as $key=>$column)
    <div class="panel col-xs-3 board-column p-0" data-column-id="{{ $column->id }}" >
        <div class="panel-heading p-t-5 p-b-5" >
            <div class="panel-title">
                <h6 style="color: {{ $column->label_color }}">{{ ucwords($column->column_name) }}

                    <div style="position: relative;" class="dropdown pull-right fullscreen-hide">
                        <a href="javascript:;"  data-toggle="dropdown"  class="dropdown-toggle "><i class="ti-settings font-normal"></i></a>
                        <ul role="menu" class="dropdown-menu">
                            <li><a href="javascript:;" data-column-id="{{ $column->id }}"  data-project-id="{{ $projectID }}"  class="add-task">@lang('modules.tasks.newTask')</a></li>
                            <li><a href="javascript:;" data-column-id="{{ $column->id }}" class="edit-type" >@lang('app.edit')</a>
                            </li>
                            @if($column->slug != 'completed' && $column->slug != 'incomplete')
                                <li><a href="javascript:;" data-column-id="{{ $column->id }}" class="delete-column"  >@lang('app.delete')</a></li>
                            @endif
                        </ul>

                    </div>
                </h6>
            </div>
        </div>
        <div class="panel-body" id="taskBox_{{ $column->id }}" style="height: 80vh; overflow-y: auto">
            <div class="row">
                <div class="col-xs-12" style="height: 400px !important;">
                    @foreach($column->tasks as $task)
                        <div class="panel panel-default lobipanel view-task" data-task-id="{{ $task->id }}" data-sortable="true">
                            <div class="panel-body">
                                <div class="p-10 p-b-0 font-12 font-semi-bold">{{ ucfirst($task->heading) }}

                                    @if ($task->is_private)
                                        <label class="label pull-right" style="background: #ea4c89">@lang('app.private')</label>
                                    @endif
                                </div>

                                @if (!is_null($task->project_id))
                                    <div class="p-10 p-t-5 text-muted"><small><i class="icon-layers"></i> {{ ucfirst($task->project->project_name) }}</small></div>
                                @endif

                                @if (!is_null($task->label) && count($task->label) > 0)
                                    <div class="p-10">
                                        @foreach($task->label as $key => $label)
                                            <label class="badge text-capitalize font-semi-bold" style="background:{{ $label->label->label_color }}">{{ ucwords($label->label->label_name) }} </label>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="p-t-10 p-b-10 p-10">
                                    @foreach ($task->users as $item)
                                        <img src="{{$item->image_url}}" data-toggle="tooltip" data-original-title="{{ ucwords($item->name)}} " data-placement="right"
                                         alt="user" class="img-circle" width="25" height="25">
                                    @endforeach
                                </div>
                                <div class="bg-grey p-10">
                                    @if($task->due_date)
                                        @if($task->due_date->endOfDay()->isPast())
                                            <span class="text-danger"><i class="icon-calender"></i> {{ $task->due_date->format($global->date_format) }}</span>
                                        @elseif($task->due_date->setTimezone($global->timezone)->isToday())
                                            <span class="text-success"><i class="icon-calender"></i> @lang('app.today')</span>
                                        @else
                                            <span><i class="icon-calender"></i> {{ $task->due_date->format($global->date_format) }}</span>
                                        @endif
                                    @else
                                            <span><i class="icon-calender"></i> </span>
                                    @endif

                                    <span class="pull-right" data-toggle="tooltip" data-original-title="@lang('modules.tasks.comment')" data-placement="left" >
                                        <i class="ti-comment"></i> {{ count($task->comments) }}
                                    </span>

                                    @if(count($task->subtasks) > 0)
                                        <span class="pull-right m-r-5" data-toggle="tooltip" data-original-title="@lang('modules.tasks.subTask')" data-placement="left" >
                                            <i class="ti-check-box"></i> {{ count($task->completedSubtasks) }} / {{ count($task->subtasks) }}
                                        </span>
                                    @endif

                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="panel panel-default lobipanel"  data-sortable="true"></div>
                </div>
            </div>
        </div>
    </div>
@endforeach
<script>
    $(function () {
        var data ='@lang("app.menu.tasks") @lang("app.from")<strong> {{ \Carbon\Carbon::parse($startDate)->format($global->date_format) }} </strong> to <strong>{{ \Carbon\Carbon::parse($endDate)->format($global->date_format) }}</strong>';
        $('#filter-result').html(data);

        // @foreach($boardColumns as $key=>$column)
        // $('#taskBox_{{ $column->id }}').slimScroll({
        //     height: '70vh'
        // });
        // @endforeach

        $('.lobipanel').on('dragged.lobiPanel', function () {
            var $parent = $(this).parent(),
                $children = $parent.children();

            var boardColumnIds = [];
            var taskIds = [];
            var prioritys = [];

            $children.each(function (ind, el) {
//                console.log(el, $(el).index());
                boardColumnIds.push($(el).closest('.board-column').data('column-id'));
                taskIds.push($(el).data('task-id'));
                prioritys.push($(el).index());
            });

            // update values for all tasks
            $.easyAjax({
                url: '{{ route("admin.taskboard.updateIndex") }}',
                type: 'POST',
                data:{boardColumnIds: boardColumnIds, taskIds: taskIds, prioritys: prioritys,'_token':'{{ csrf_token() }}'},
                success: function (response) {
                }
            });

            $("body").tooltip({
                selector: '[data-toggle="tooltip"]'
            });

            $('.board-column').each(function () {
                let lobipanelItems = $(this).find('.view-task').length;
                // console.log(lobipanelItems);
                if (lobipanelItems == 1) {
                    $(this).find('.lobipanel:first').addClass('m-b-0');
                }
            })

        }).lobiPanel({
            sortable: true,
            reload: false,
            editTitle: false,
            close: false,
            minimize: false,
            unpin: false,
            expand: false

        });

        var isDragging = 0;
        $('.lobipanel-parent-sortable').on('sortactivate', function(){
            // console.log("activate event handle");
            $('.board-column > .panel-body').css('overflow-y', 'unset');
            isDragging = 1;
        });
        $('.lobipanel-parent-sortable').on('sortstop', function(e){
            // console.log("stop event handle");
            $('.board-column > .panel-body').css('overflow-y', 'auto');
            isDragging = 0;
        });



        $('.view-task').click(function () {
            if (isDragging == 0) {
                $(".right-sidebar").slideDown(50).addClass("shw-rside");

                var id = $(this).data('task-id');
                var url = "{{ route('admin.all-tasks.show',':id') }}";
                url = url.replace(':id', id);

                $.easyAjax({
                    type: 'GET',
                    url: url,
                    success: function (response) {
                        if (response.status == "success") {
                            $('#right-sidebar-content').html(response.view);
                        }
                    }
                });

            }
        })



        $('.add-task').click(function () {
            var id = $(this).data('column-id');
            var projectID = $(this).data('project-id');
            var url = '{{ route('admin.all-tasks.ajaxCreate', ':id')}}?projectID='+projectID;
            url = url.replace(':id', id);

            $('#modelHeading').html('Add Task');
            $.ajaxModal('#eventDetailModal', url);
        })

        $('.delete-column').click(function () {
            var id = $(this).data('column-id');
            var url = '{{ route('admin.taskboard.destroy', ':id')}}';
            url = url.replace(':id', id);

            swal({
                title: "@lang('messages.sweetAlertTitle')",
                text: "@lang('messages.confirmation.recoveColumn')",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('messages.deleteConfirmation')",
                cancelButtonText: "@lang('messages.confirmNoArchive')",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {
                    $.easyAjax({
                        url: url,
                        type: 'POST',
                        data: { '_token': '{{ csrf_token() }}', '_method': 'DELETE'},
                        success: function (response) {
                            if(response.status == 'success'){
                                window.location.reload();
                            }
                        }
                    });

                }
            });

        })


        {{--$('.edit-column').click(function () {--}}
            {{--var id = $(this).data('column-id');--}}
            {{--var url = '{{ route("admin.taskboard.edit", ':id') }}';--}}
            {{--url = url.replace(':id', id);--}}

            {{--$.easyAjax({--}}
                {{--url: url,--}}
                {{--type: "GET",--}}
                {{--success: function (response) {--}}
                    {{--$('#edit-column-form').html(response.view);--}}
                    {{--$(".colorpicker").asColorPicker();--}}
                    {{--$('#edit-column-form').show();--}}
                {{--}--}}
            {{--})--}}
        {{--})--}}

    });
</script>
