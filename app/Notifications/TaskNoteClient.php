<?php

namespace App\Notifications;

use App\Task;
use App\SlackSetting;
use Illuminate\Support\Carbon;

use App\EmailNotificationSetting;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class TaskNoteClient extends BaseNotification
{


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $task;
    private $created_at;

    public function __construct(Task $task, $created_at)
    {
        parent::__construct();
        $this->task = $task;
        $this->created_at = $created_at;
        $this->emailSetting = EmailNotificationSetting::where('setting_name', 'User Assign to Task')->first();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = ['database'];

        if ($this->emailSetting->send_email == 'yes' && $notifiable->email_notifications) {
            array_push($via, 'mail');
        }

        if ($this->emailSetting->send_slack == 'yes') {
            array_push($via, 'slack');
        }

        if ($this->emailSetting->send_push == 'yes') {
            array_push($via, OneSignalChannel::class);
        }

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('email.taskNote.subject') . ' #' . $this->task->id . ' - ' . config('app.name') . '!')
            ->greeting(__('email.hello') . ' ' . ucwords($notifiable->name) . '!')
            ->line(__('email.taskNote.subject') . ' - ' . ucfirst($this->task->heading) . ' #' . $this->task->id)
            ->line((!is_null($this->task->project)) ? __('app.project') . ' - ' . ucfirst($this->task->project->project_name) : '')
            ->action(__('email.loginDashboard'), url('/'))
            ->line(__('email.thankyouNote'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
//        return $this->task->toArray();
        return [
            'id' => $this->task->id,
            'created_at' => Carbon::parse($this->task->created_at)->format('Y-m-d H:i:s'),
            'heading' => $this->task->heading,
            'hash' => $this->task->hash,
            'completed_on' => Carbon::parse($this->task->completed_on)->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return SlackMessage
     */
    public function toSlack($notifiable)
    {
        $slack = SlackSetting::setting();
        if (count($notifiable->employee) > 0 && (!is_null($notifiable->employee[0]->slack_username) && ($notifiable->employee[0]->slack_username != ''))) {
            return (new SlackMessage())
                ->from(config('app.name'))
                ->image($slack->slack_logo_url)
                ->to('@' . $notifiable->employee[0]->slack_username)
                ->content('*' . __('email.taskNote.subject') . '*' . "\n" . ucfirst($this->task->heading) . "\n" . ' #' . $this->task->id);
        }
        return (new SlackMessage())
            ->from(config('app.name'))
            ->image($slack->slack_logo_url)
            ->content('This is a redirected notification. Add slack username for *' . ucwords($notifiable->name) . '*');
    }

    public function toOneSignal($notifiable)
    {
        return OneSignalMessage::create()
            ->subject(__('email.taskNote.subject'))
            ->body(__('email.taskNote.subject') . ' - ' . ucfirst($this->task->heading) . ' #' . $this->task->id);
    }

}
