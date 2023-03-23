<?php

namespace App\Http\Controllers\Member;

use App\Helper\Reply;
use App\Http\Requests\Notice\StoreNotice;
use App\Notice;
use App\Notifications\NewNotice;
use App\Team;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Yajra\DataTables\Facades\DataTables;

class MemberNoticesController extends MemberBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.noticeBoard';
        $this->pageIcon = 'ti-layout-media-overlay';
        $this->middleware(function ($request, $next) {
            if (!in_array('notices', $this->user->modules)) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index()
    {
        $this->notices = Notice::orderBy('id', 'desc')->where('to', 'employee')->where('department_id', null)->orWhere('department_id', user()->employeeDetail->department_id)->limit(10)->get();
        return view('member.notices.index', $this->data);
    }

    public function show($id)
    {
        $this->notice = Notice::with('member', 'member.user')->findOrFail($id);

        $readUser = $this->notice->member->filter(function ($value, $key) {
            return $value->user_id == $this->user->id && $value->notice_id == $this->notice->id;
        })->first();

        if ($readUser) {
            $readUser->read = 1;
            $readUser->save();
        }
        if ($this->user->cans('view_notice'))
        {
            $this->readMembers = $this->notice->member->filter(function ($value, $key) {
                return $value->read == 1;
            });

            $this->unReadMembers = $this->notice->member->filter(function ($value, $key) {
                return $value->read == 0;
            });
        }

        return view('member.notices.show', $this->data);
    }

    public function create()
    {
        if(!$this->user->cans('add_notice')){
            abort(403);
        }
        $this->teams = Team::all();
        return view('member.notices.create', $this->data);
    }

    public function store(StoreNotice $request)
    {
        $notice = new Notice();
        $notice->heading = $request->heading;
        $notice->description = $request->description;
        $notice->to = $request->to;

        if($request->file){
            $notice->attachment = $request->file->hashName();
            $request->file->store('notice-attachment');
        }

        $notice->save();

        $this->logSearchEntry($notice->id, 'Notice: '.$notice->heading, 'admin.notices.edit', 'notice');

        return Reply::redirect(route('member.notices.index'), __('messages.noticeAdded'));
    }

    public function edit($id)
    {
        if(!$this->user->cans('edit_notice')){
            abort(403);
        }
        $this->notice = Notice::findOrFail($id);
        $this->teams = Team::all();
        return view('member.notices.edit', $this->data);
    }

    public function update(StoreNotice $request, $id)
    {
        $notice = Notice::findOrFail($id);
        $notice->heading = $request->heading;
        $notice->description = $request->description;
        $notice->to = $request->to;
        $notice->department_id = $request->team_id;

        if($request->file){
            $notice->attachment = $request->file->hashName();
            $request->file->store('notice-attachment');
        }

        $notice->save();

        return Reply::redirect(route('member.notices.index'), __('messages.noticeUpdated'));
    }

    public function destroy($id)
    {
        Notice::destroy($id);
        return Reply::success(__('messages.noticeDeleted'));
    }

    public function data(Request $request)
    {
        $notice = Notice::select('id', 'heading', 'created_at', 'department_id')->where('to', 'employee')
            ->where('department_id', null)->orWhere('department_id', user()->employeeDetail->department_id);
        if($request->startDate !== null && $request->startDate != 'null' && $request->startDate != ''){
            $startDate = $startDate = Carbon::createFromFormat($this->global->date_format, $request->startDate)->format('Y-m-d');
            $notice = $notice->where(DB::raw('DATE(notices.`created_at`)'), '>=', $startDate);
        }

        if($request->endDate !== null && $request->endDate != 'null' && $request->endDate != ''){
            $endDate = $endDate = Carbon::createFromFormat($this->global->date_format, $request->endDate)->format('Y-m-d');
            $notice = $notice->where(DB::raw('DATE(notices.`created_at`)'), '<=', $endDate);
        }

        $notice = $notice->get();

        return DataTables::of($notice)
            ->addColumn('action', function($row){
                $action = '';
                $action .= '<a href="javascript:;" class="btn btn-success btn-circle noticeShow"
                  data-toggle="tooltip" data-notice-id="' . $row->id . '" data-original-title="View"><i class="fa fa-search" aria-hidden="true"></i></a> ';

                if($this->user->cans('edit_notice')){
                    $action .= '<a href="'.route('member.notices.edit', [$row->id]).'" class="btn btn-info btn-circle"
                      data-toggle="tooltip" data-original-title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                }

                if($this->user->cans('delete_notice')) {
                    $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                      data-toggle="tooltip" data-user-id="' . $row->id . '" data-original-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></a>';
                }
                return $action;
            })
            ->addColumn('heading', function($row){
                return '<a href="javascript:;"  data-notice-id="' . $row->id . '"  class="noticeShow">' .ucfirst($row->heading) . '</a>';
            })

            ->editColumn(
                'created_at',
                function ($row) {
                    return Carbon::parse($row->created_at)->format($this->global->date_format);
                }
            )
            ->rawColumns(['heading','action'])
            ->make(true);
    }

}
