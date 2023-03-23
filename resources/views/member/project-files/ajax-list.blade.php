@forelse($project->files as $file)
    <li class="list-group-item">
        <div class="row">
            <div class="col-md-9">
                {{ $file->filename }}
            </div>
            <div class="col-md-3">

                    <a target="_blank" href="{{ $file->file_url }}"
                       data-toggle="tooltip" data-original-title="View"
                       class="btn btn-info btn-circle"><i
                                class="fa fa-search"></i></a>


                @if(is_null($file->external_link))
                <a href="{{ route('member.files.download', $file->id) }}"
                   data-toggle="tooltip" data-original-title="Download"
                   class="btn btn-default btn-circle"><i
                            class="fa fa-download"></i></a>
                @endif

                @if($file->user_id == $user->id || $project->isProjectAdmin || $user->cans('edit_projects'))
                    &nbsp;&nbsp;
                    <a href="javascript:;" data-toggle="tooltip" data-original-title="Delete" data-file-id="{{ $file->id }}" class="btn btn-danger btn-circle sa-params" data-pk="list"><i class="fa fa-times"></i></a>
                @endif
        <span class="m-l-10">{{ $file->created_at->diffForHumans() }}</span>
            </div>
        </div>
    </li>
@empty
    <li class="list-group-item">
        <div class="row">
            <div class="col-md-10">
                @lang('messages.noFileUploaded')
            </div>
        </div>
    </li>
@endforelse
