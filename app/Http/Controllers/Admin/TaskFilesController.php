<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Files;
use App\Helper\Reply;
use App\TaskFile;
use App\TaskRequestFile;
use Illuminate\Http\Request;
use File;

class TaskFilesController extends AdminBaseController
{

    private $mimeType = [
        'txt' => 'fa-file-text',
        'htm' => 'fa-file-code-o',
        'html' => 'fa-file-code-o',
        'php' => 'fa-file-code-o',
        'css' => 'fa-file-code-o',
        'js' => 'fa-file-code-o',
        'json' => 'fa-file-code-o',
        'xml' => 'fa-file-code-o',
        'swf' => 'fa-file-o',
        'flv' => 'fa-file-video-o',

        // images
        'png' => 'fa-file-image-o',
        'jpe' => 'fa-file-image-o',
        'jpeg' => 'fa-file-image-o',
        'jpg' => 'fa-file-image-o',
        'gif' => 'fa-file-image-o',
        'bmp' => 'fa-file-image-o',
        'ico' => 'fa-file-image-o',
        'tiff' => 'fa-file-image-o',
        'tif' => 'fa-file-image-o',
        'svg' => 'fa-file-image-o',
        'svgz' => 'fa-file-image-o',

        // archives
        'zip' => 'fa-file-o',
        'rar' => 'fa-file-o',
        'exe' => 'fa-file-o',
        'msi' => 'fa-file-o',
        'cab' => 'fa-file-o',

        // audio/video
        'mp3' => 'fa-file-audio-o',
        'qt' => 'fa-file-video-o',
        'mov' => 'fa-file-video-o',
        'mp4' => 'fa-file-video-o',
        'mkv' => 'fa-file-video-o',
        'avi' => 'fa-file-video-o',
        'wmv' => 'fa-file-video-o',
        'mpg' => 'fa-file-video-o',
        'mp2' => 'fa-file-video-o',
        'mpeg' => 'fa-file-video-o',
        'mpe' => 'fa-file-video-o',
        'mpv' => 'fa-file-video-o',
        '3gp' => 'fa-file-video-o',
        'm4v' => 'fa-file-video-o',

        // adobe
        'pdf' => 'fa-file-pdf-o',
        'psd' => 'fa-file-image-o',
        'ai' => 'fa-file-o',
        'eps' => 'fa-file-o',
        'ps' => 'fa-file-o',

        // ms office
        'doc' => 'fa-file-text',
        'rtf' => 'fa-file-text',
        'xls' => 'fa-file-excel-o',
        'ppt' => 'fa-file-powerpoint-o',
        'docx' => 'fa-file-text',
        'xlsx' => 'fa-file-excel-o',
        'pptx' => 'fa-file-powerpoint-o',


        // open office
        'odt' => 'fa-file-text',
        'ods' => 'fa-file-text',
    ];

    /**
     * ManageLeadFilesController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->pageIcon = 'icon-layers';
        $this->pageTitle = 'app.menu.taskFiles';
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
     * @param Request $request
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        if ($request->hasFile('file')) {
            $task_request_files = TaskRequestFile::where('task_id', $request->task_request_id)->get();
            
            $limitReached = false;
            foreach ($request->file as $fileData){
                $upload = can_upload($fileData->getSize() / (1000 * 1024));
                if($upload) {
                    $file = new TaskFile();
                    $file->user_id = $this->user->id;
                    $file->task_id = $request->task_id;

                    $filename = Files::uploadLocalOrS3($fileData, 'task-files/' . $request->task_id);

                    $file->filename = $fileData->getClientOriginalName();
                    $file->hashname = $filename;
                    $file->size = $fileData->getSize();
                    
                    $file->save();
                } else {
                    $limitReached = true;
                }
            }
            if($task_request_files != ''){
                foreach($task_request_files as $task_request_file){
                    $file = new TaskFile();
                    $file->user_id = $this->user->id;
                    $file->task_id = $request->task_id;
                    $filename = $task_request_file->filename;
                    $file->filename = $task_request_file->filename;
                    $file->hashname = $task_request_file->hashname;
                    $file->size = $task_request_file->size;
                    $file->save();
                }
            }
            if($limitReached) {
                return Reply::error(__('messages.storageLimitExceed', ['here' => '<a href='.route('admin.billing.packages'). '>Here</a>']));
            }
        }

        $this->taskFiles = TaskFile::where('task_id', $request->task_id)->get();

        $view = view('admin.tasks.ajax-list', $this->data)->render();

        return Reply::successWithData(__('messages.fileUploaded'), ['html' => $view, 'totalFiles' => sizeof($this->taskFiles)]);

        //        return Reply::redirect(route('admin.all-tasks.index'), __('modules.projects.projectUpdated'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //        $this->lead = Lead::findOrFail($id);
        //        return view('admin.lead.lead-files.show', $this->data);
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
     * @param Request $request
     * @param $id
     * @return array
     * @throws \Throwable
     */
    public function destroy(Request $request, $id)
    {
        $file = TaskFile::findOrFail($id);

        Files::deleteFile($file->hashname, 'task-files/'.$file->task_id);

        TaskFile::destroy($id);

        $this->taskFiles = TaskFile::where('task_id', $file->task_id)->get();

        $view = view('admin.tasks.ajax-list', $this->data)->render();

        return Reply::successWithData(__('messages.fileDeleted'), ['html' => $view, 'totalFiles' => sizeof($this->taskFiles)]);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download($id)
    {
        $file = TaskFile::findOrFail($id);
        return download_local_s3($file, 'task-files/' . $file->task_id.'/'.$file->hashname);
    }

}
