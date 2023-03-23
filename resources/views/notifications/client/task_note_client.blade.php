<li class="top-notifications">
    <div class="message-center">
        <a href="{{route('front.task-share',[$notification->data['hash']])}}" target="_blank">
            <div class="user-img">
                <span class="btn btn-circle btn-info"><i class="fa fa-tasks"></i></span>
            </div>
            <div class="mail-contnet">
                <span class="mail-desc m-0">@lang('email.tasknote.subject')</span>
                <small>{{ ucfirst($notification->data['heading']) }}</small>
                <span class="time">@if($notification->created_at){{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $notification->created_at)->diffForHumans() }}@endif</span>
            </div>
        </a>
    </div>
</li>
