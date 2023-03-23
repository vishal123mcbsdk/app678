<?php

namespace App\Http\Controllers\Client;

use App\Helper\Reply;
use App\ModuleSetting;
use App\Project;
use App\SubTask;
use App\Task;
use App\TaskboardColumn;
use App\TaskCategory;
use App\TaskRequest;
use App\Traits\ProjectProgress;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class ClientTasksController extends ClientBaseController
{
    use ProjectProgress;

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.projects';
        $this->pageIcon = 'icon-layers';
        $this->middleware(function ($request, $next) {
            abort_if(!in_array('tasks', $this->user->modules), 403);
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
            abort(403);
        $task = new Task();
        $task->heading = $request->title;
        if ($request->description != '') {
            $task->description = $request->description;
        }
        $taskBoardColumn = TaskboardColumn::where('slug', 'incomplete')->first();

        $task->start_date = Carbon::createFromFormat($this->global->date_format, $request->start_date)->format('Y-m-d');
        if (!$request->has('without_duedate')) {
            $task->due_date = Carbon::createFromFormat($this->global->date_format, $request->due_date)->format('Y-m-d');
        }
        $task->project_id = $request->project_id;
        $task->priority = $request->priority;
        $task->board_column_id = $taskBoardColumn->id;
        //        $task->task_category_id = $request->category_id;
        $task->dependent_task_id = $request->has('dependent') && $request->dependent == 'yes' && $request->has('dependent_task_id') && $request->dependent_task_id != '' ? $request->dependent_task_id : null;
        $task->is_private = 0;
        $task->billable   = $request->has('billable') && $request->billable == 'true' ? 1 : 0;
        $task->created_by = $this->user->id;

        if ($request->milestone_id != '') {
            $task->milestone_id = $request->milestone_id;
        }

        $task->save();

        $this->project = Project::findOrFail($task->project_id);
        //        $view = view('client.projects.tasks.task-list-ajax', $this->data)->render();

        return Reply::success(__('messages.taskCreatedSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->task = Task::with('files')->findOrFail($id);
        $this->upload = can_upload();
        $view = view('client.tasks.show', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // abort(403);
        $this->tasks = Task::leftJoin('projects', 'projects.id', '=', 'tasks.project_id')
            ->where('tasks.project_id', '=', $id)
            ->where('projects.client_id', '=', $this->user->id)
            ->select('tasks.*')
            ->get();
        $this->project = Project::findOrFail($id);
        $this->employees  = User::withoutGlobalScope('active')->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('users.id', 'users.name', 'users.email', 'users.email_notifications', 'users.created_at', 'users.image')
            ->where('roles.name', '<>', 'client')
            ->where('users.company_id', company()->id)
            ->orderBy('users.name', 'asc')
            ->groupBy('users.id')
            ->get();
        $this->categories = TaskCategory::all();

        if ($this->project->client_view_task == 'disable') {
            abort(403);
        }
        return view('client.tasks.edit', $this->data);
    }

    public function checkTask($taskID)
    {
        $task = Task::findOrFail($taskID);
        $subTask = SubTask::where(['task_id' => $taskID, 'status' => 'incomplete'])->count();

        return Reply::dataOnly(['taskCount' => $subTask, 'lastStatus' => $task->board_column->slug]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function ajaxEdit($id)
    {

        $this->task = Task::findOrFail($id);

        if ($this->task->project->client_view_task == 'disable' ) {
            abort(403);
        }

        $this->taskBoardColumns = TaskboardColumn::all();
        $this->categories = TaskCategory::all();

        $completedTaskColumn = TaskboardColumn::where('slug', '=', 'completed')->first();
        if ($completedTaskColumn) {
            $this->allTasks = Task::where('board_column_id', '<>', $completedTaskColumn->id)
                ->where('id', '!=', $id);

            if ($this->task->project_id != '') {
                $this->allTasks = $this->allTasks->where('project_id', $this->task->project_id);
            }

            $this->allTasks = $this->allTasks->get();
        } else {
            $this->allTasks = [];
        }



        $view = view('client.tasks.ajax-edit', $this->data)->render();
        return Reply::dataOnly(['html' => $view]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        abort(403);
        $task = Task::findOrFail($id);
        if ($task->created_by != $this->user->id) {
            abort(403);
        }
        $task->heading = $request->title;
        if ($request->description != '') {
            $task->description = $request->description;
        }
        $task->start_date = Carbon::createFromFormat($this->global->date_format, $request->start_date)->format('Y-m-d');
        if (!$request->has('without_duedate')) {
            $task->due_date = Carbon::createFromFormat($this->global->date_format, $request->due_date)->format('Y-m-d');
        }
            $task->priority = $request->priority;
        //        $task->task_category_id = $request->category_id;
        $task->board_column_id = $request->status;
        $task->dependent_task_id = $request->has('dependent') && $request->dependent == 'yes' && $request->has('dependent_task_id') && $request->dependent_task_id != '' ? $request->dependent_task_id : null;
        $task->is_private = $request->has('is_private') && $request->is_private == 'true' ? 1 : 0;
        $task->billable = $request->has('billable') && $request->billable == 'true' ? 1 : 0;

        $taskBoardColumn = TaskboardColumn::findOrFail($request->status);
        if ($taskBoardColumn->slug == 'completed') {
            $task->completed_on = Carbon::now();
        } else {
            $task->completed_on = null;
        }

        if ($request->milestone_id != '') {
            $task->milestone_id = $request->milestone_id;
        }

        $task->save();

        // Sync task users
        $task->users()->sync($request->user_id);



        //calculate project progress if enabled
        $this->calculateProjectProgress($request->project_id);



        return Reply::success(__('messages.taskUpdatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort(403);
        $task = Task::findOrFail($id);

        if ($task->created_by != $this->user->id) {
            abort(403);
        }
        // Delete current task
        Task::destroy($id);

        //calculate project progress if enabled
        $this->calculateProjectProgress($task->project_id);

        return Reply::success(__('messages.taskDeletedSuccessfully'));
    }

    public function data(Request $request, $projectId = null)
    {
        $tasks = Task::leftJoin('projects', 'projects.id', '=', 'tasks.project_id')
            ->join('taskboard_columns', 'taskboard_columns.id', '=', 'tasks.board_column_id')
            ->leftJoin('users as creator_user', 'creator_user.id', '=', 'tasks.created_by')
            //            ->join('task_users', 'task_users.task_id', '=', 'tasks.id')
            ->leftJoin('users as client', 'client.id', '=', 'projects.client_id')
            ->where('tasks.project_id', '=', $projectId)
            ->where('projects.client_id', '=', $this->user->id)
            ->where('tasks.is_private', 0)
            ->select('tasks.id', 'projects.project_name', 'projects.read_only', 'projects.client_view_task','tasks.heading', 'client.name as clientName', 'creator_user.name as created_by', 'creator_user.image as created_image', 'tasks.due_date', 'taskboard_columns.column_name', 'taskboard_columns.label_color', 'tasks.project_id', 'tasks.created_by as created_id')
            ->with('users')
            ->groupBy('tasks.id');

        $tasks->get();

        return DataTables::of($tasks)
            ->addColumn('action', function ($row) {
                $button = '';
                if($row->read_only != 'enable' || $row->client_view_task == 'enable'){
                    $button .= '<a href="javascript:;" class="btn btn-info btn-circle edit-task"
                    data-toggle="tooltip" data-task-id="' . $row->id . '" data-original-title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                        $button .= '&nbsp;&nbsp;<a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                    data-toggle="tooltip" data-task-id="' . $row->id . '" data-original-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></a>';

                    return $button;
                } else {
                    return '--';
                }
            })
            ->editColumn('due_date', function ($row) {
                if($row->due_date){
                    if ($row->due_date->isPast()) {
                        return '<span class="text-danger">' . $row->due_date->format($this->global->date_format) . '</span>';
                    }
                    return '<span class="text-success">' . $row->due_date->format($this->global->date_format) . '</span>';
                }else{
                    return '--';
                }
            })
            ->editColumn('users', function ($row) {
                $members = '';
                foreach ($row->users as $member) {
                    $members .= '<a href="javascript:;">';
                    $members .= '<img data-toggle="tooltip" data-original-title="' . ucwords($member->name) . '" src="' . $member->image_url . '"
                    alt="user" class="img-circle" width="25" height="25"> ';
                    $members .= '</a>';
                }

                return $members;
            })

            ->editColumn('created_by', function ($row) {
                if (!is_null($row->created_by) && $row->created_id == $this->user->id) {
                    return 'You';
                } elseif (!is_null($row->created_by) && $row->created_id != $this->user->id) {
                    return ($row->created_image) ? '<img src="' . asset_url('avatar/' . $row->created_image) . '"
                                                            alt="user" class="img-circle" width="25" height="25"> ' . ucwords($row->created_by) : '<img src="' . asset('img/default-profile-3.png') . '"
                                                            alt="user" class="img-circle" width="25" height="25"> ' . ucwords($row->created_by);
                }
                return '-';
            })
            ->editColumn('heading', function ($row) {
                return '<a href="javascript:;" data-task-id="' . $row->id . '" class="show-task-detail">' . ucfirst($row->heading) . '</a>';
            })
            ->editColumn('column_name', function ($row) {
                return '<label class="label" style="background-color: ' . $row->label_color . '">' . $row->column_name . '</label>';
            })
            ->rawColumns(['column_name', 'action', 'clientName', 'due_date', 'users', 'created_by', 'heading'])
            ->removeColumn('project_id')
            ->removeColumn('image')
            ->removeColumn('created_image')
            ->removeColumn('label_color')
            ->addIndexColumn()
            ->make(true);
    }

}
