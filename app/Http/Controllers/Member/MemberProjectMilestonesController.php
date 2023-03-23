<?php

namespace App\Http\Controllers\Member;

use App\Project;
use App\Http\Requests\Milestone\StoreMilestone;
use App\ProjectMilestone;
use App\Helper\Reply;
use Yajra\DataTables\Facades\DataTables;
use App\Currency;
use Carbon\Carbon;

class MemberProjectMilestonesController extends MemberBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.projects';
        $this->pageIcon = 'icon-layers';
        $this->middleware(function ($request, $next) {
            abort_if(!in_array('projects', $this->user->modules), 403);
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
    public function store(StoreMilestone $request)
    {
        $milestone = new ProjectMilestone();
        $milestone->project_id = $request->project_id;
        $milestone->milestone_title = $request->milestone_title;
        $milestone->summary = $request->summary;
        $milestone->cost = ($request->cost == '') ? '0' : $request->cost;
        $milestone->currency_id = $request->currency_id;
        $milestone->status = $request->status;
        $milestone->due_date = ($request->due_date != '') ? Carbon::createFromFormat($this->global->date_format, $request->due_date)->format('Y-m-d') : null;
        $milestone->save();

        if ($request->add_to_budget == 'yes') {
            $project = Project::findOrFail($request->project_id);
            $project->project_budget = (!is_null($project->project_budget) ? ($project->project_budget + $milestone->cost) : $milestone->cost);
            $project->currency_id = $request->currency_id;
            $project->save();
        }

        return Reply::success(__('messages.milestoneSuccess'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort_if(!$this->user->cans('view_projects'), 403);

        $this->project = Project::findorFail($id);
        $this->currencies = Currency::all();
        return view('member.projects.milestones.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(!$this->user->cans('edit_projects'), 403);

        $this->milestone = ProjectMilestone::findOrFail($id);
        $this->currencies = Currency::all();
        return view('member.projects.milestones.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreMilestone $request, $id)
    {
        $milestone = ProjectMilestone::findOrFail($id);
        $milestone->project_id = $request->project_id;
        $milestone->milestone_title = $request->milestone_title;
        $milestone->summary = $request->summary;
        $milestone->cost = ($request->cost == '') ? '0' : $request->cost;
        $milestone->currency_id = $request->currency_id;
        $milestone->status = $request->status;
        $milestone->due_date = ($request->due_date != '') ? Carbon::createFromFormat($this->global->date_format, $request->due_date)->format('Y-m-d') : null;
        $milestone->save();

        return Reply::success(__('messages.milestoneSuccess'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        ProjectMilestone::destroy($id);
        return Reply::success(__('messages.deleteSuccess'));
    }

    public function data($id)
    {
        $milestones = ProjectMilestone::with('currency')->where('project_id', $id)->get();

        return DataTables::of($milestones)
            ->addColumn('action', function ($row) {
                return '<a href="javascript:;" class="btn btn-info btn-circle edit-milestone"
                        data-toggle="tooltip" data-milestone-id="' . $row->id . '"  data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>

                        <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                        data-toggle="tooltip" data-milestone-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times" aria-hidden="true"></i></a>';
            })
            ->editColumn('status', function ($row) {
                if ($row->status == 'complete') {
                    return '<label class="label label-success">' . __('app.'.$row->status) . '</label>';
                } else {
                    return '<label class="label label-danger">' . __('app.'.$row->status) . '</label>';
                }
            })
            ->editColumn('cost', function ($row) {
                if (!is_null($row->currency_id)) {
                    return currency_formatter($row->cost, $row->currency->currency_symbol);
                }
                return $row->cost;
            })
            ->editColumn('milestone_title', function ($row) {
                return '<a href="javascript:;" class="milestone-detail" data-milestone-id="' . $row->id . '">' . ucfirst($row->milestone_title) . '</a>';
            })
            ->editColumn('due_date', function ($row) {
                return ($row->due_date != null) ? ($row->due_date->format($this->global->date_format)) : '--';
            })
            ->rawColumns(['status', 'action', 'milestone_title','due_date'])
            ->removeColumn('project_id')
            ->make(true);
    }

    public function detail($id)
    {
        abort_if(!$this->user->cans('view_projects'), 403);
        $this->milestone = ProjectMilestone::findOrFail($id);
        return view('member.projects.milestones.detail', $this->data);
    }

    /////
}
