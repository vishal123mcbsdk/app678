<li class="top-notifications">
    <div class="message-center">
        <a href="{{ route('member.tickets.edit',  $notification->data['id']) }}">
            <div class="user-img">
                <span class="btn btn-circle btn-warning"><i class="ti-ticket"></i></span>
            </div>
            <div class="mail-contnet">
                <span class="mail-desc m-0">{{ __('email.ticketAgent.subject') }}</span> 
                <small>{{ ucfirst($notification->data['subject']) }}</small>
                <span class="time">{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $notification->created_at)->diffForHumans() }}</span>
            </div>
        </a>
    </div>
</li>
