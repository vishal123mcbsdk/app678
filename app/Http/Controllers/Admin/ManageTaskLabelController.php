<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\LabelDataTable;
use App\Helper\Reply;
use App\Http\Requests\Admin\TaskLabel\StoreRequest;
use App\Http\Requests\Admin\TaskLabel\UpdateRequest;
use App\TaskLabel;
use App\TaskLabelList;

class ManageTaskLabelController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageIcon = 'fa fa-file';
        $this->pageTitle = 'app.menu.taskLabel';
        $this->middleware(function ($request, $next) {
            if(!in_array('tasks', $this->user->modules)){
                abort(403);
            }
            return $next($request);
        });
    }

    public function index(LabelDataTable $dataTable)
    {
        return $dataTable->render('admin.task-label.index', $this->data);
    }

    public function create()
    {
        return view('admin.task-label.create', $this->data);
    }

    public function store(StoreRequest $request)
    {
        $taskLabel = new TaskLabelList();
        $this->storeUpdate($request, $taskLabel);
        return Reply::redirect(route('admin.task-label.index'), __('messages.taskLabel.addedSuccess'));
    }

    public function edit($id)
    {
        $this->taskLabel = TaskLabelList::find($id);
        return view('admin.task-label.edit', $this->data);
    }

    public function update(UpdateRequest $request, $id)
    {
        $taskLabel = TaskLabelList::findOrFail($id);
        $this->storeUpdate($request, $taskLabel);

        return Reply::redirect(route('admin.task-label.index'), __('messages.taskLabel.updatedSuccess'));
    }

    public function show($id)
    {
        //
    }

    private function storeUpdate($request, $taskLabel)
    {
        $taskLabel->label_name  = $request->label_name;
        $taskLabel->color       = $request->color;
        $taskLabel->description = $request->description;
        $taskLabel->save();

        return $taskLabel;
    }

    public function destroy($id)
    {
        TaskLabel::where('label_id', $id)->delete();
        TaskLabelList::destroy($id);

        return Reply::success(__('messages.taskLabel.deletedSuccess'));
    }

    public function createLabel()
    {
        return view('admin.task-label.create-ajax', $this->data);
    }

    public function storeLabel(StoreRequest $request)
    {
        $label_name_array = !is_null($request->label_name_array) ? explode(',', $request->label_name_array) : null;

        $taskLabel = new TaskLabelList();
        $this->storeUpdate($request, $taskLabel);
        $allTaskLabels = TaskLabelList::all();
        $labels = '';
        foreach ($allTaskLabels as $key => $value) {

            $selected = (!is_null($label_name_array) && in_array($value->id, $label_name_array)) ? 'selected' : '';

            $labels .= '<option '.$selected.' data-content="<label class=\'badge b-all\' style=\'background:' . $value->label_color . '\'>' . $value->label_name . '</label> " value="' . $value->id . '">' . $value->label_name . '</option>';
        }
        return Reply::successWithData(__('messages.taskLabel.addedSuccess'), ['labels' => $labels,'data' => $this->data]);
    }

}
