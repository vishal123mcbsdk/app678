<?php

namespace App\Http\Controllers\Admin;

use App\Discussion;
use App\DiscussionCategory;
use App\DiscussionFile;
use App\DiscussionReply;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\Discussion\StoreRequest;
use App\TaskFile;
use Illuminate\Http\Request;

class DiscussionController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.discussion';
        $this->pageIcon = 'ti-comments';
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
        $this->categories = DiscussionCategory::orderBy('order', 'asc')->get();
        $this->projectId = request('id');
        $this->upload = can_upload();
        return view('admin.discussion.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $discussion = new Discussion();
        $discussion->title = $request->title;
        $discussion->discussion_category_id = $request->discussion_category_id;
        if (request()->has('project_id')) {
            $discussion->project_id = $request->project_id;
        }
        $discussion->user_id = $this->user->id;
        $discussion->save();

        $reply = DiscussionReply::create(
            [
                'body' => $request->description,
                'user_id' => $this->user->id,
                'discussion_id' => $discussion->id
            ]
        );
        return Reply::dataOnly(['discussionID' => $discussion->id]);
        //        return Reply::success(__('messages.recordSaved'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $replies = DiscussionReply::where('discussion_id', $id)->get();

        foreach ($replies as $reply) {
            $taskFiles = DiscussionFile::where('discussion_reply_id', $reply->id)->get();
            foreach ($taskFiles as $file) {
                Files::deleteFile($file->hashname, 'discussion-files/');
                $file->delete();
            }
        }

        Discussion::destroy($id);
        return Reply::success(__('messages.deleteSuccess'));

    }

    public function setBestAnswer(Request $request)
    {
        if ($request->type == 'set') {
            Discussion::where('id', $request->discussionId)
                ->update(
                    [
                        'best_answer_id' => $request->replyId
                    ]
                );
        } else {
            Discussion::where('id', $request->discussionId)
                ->update(
                    [
                        'best_answer_id' => null
                    ]
                );
        }
        

        return $this->ajaxDiscussionReplies($request->discussionId);
    }

    /**
     * send ajax replies
     *
     * @param int $discussionId
     *
     * @return \Illuminate\Http\Response
     */
    public function ajaxDiscussionReplies($discussionId)
    {
        $this->discussionReplies = DiscussionReply::with('user')->where('discussion_id', $discussionId)->orderBy('id', 'asc')->get();
        $this->discussion = Discussion::with('category')->findOrFail($discussionId);

        $html = view('admin.discussion-reply.reply', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'html' => $html]);
    }

}
