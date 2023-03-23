<?php

namespace App\Notifications;

use App\Task;
use App\SlackSetting;
use Illuminate\Support\Carbon;
use App\PushNotificationSetting;
use App\EmailNotificationSetting;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class TaskReminder extends BaseNotification
{

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $task;
    private $emailSetting;

    public function __construct(Task $task)
    {
        parent::__construct();
        $this->task = $task;
        $this->emailSetting = EmailNotificationSetting::where('setting_name', 'Task Completed')->first();
        $this->pushNotification = PushNotificationSetting::first();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = array();
        if ($notifiable->email_notifications) {
            array_push($via, 'mail');
        }

        if ($this->emailSetting->send_slack == 'yes') {
            array_push($via, 'slack');
        }

        if ($this->emailSetting->send_push == 'yes' && $this->pushNotification->status == 'active') {
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
        $url = url('/login');
        if($this->task->due_date != null){
            $content = ucfirst($this->task->heading) . '<p>
            <b style="color: green">' . __('app.dueOn') . ' : ' . $this->task->due_date->format('d M, Y') . '</b>
        </p>';
        }else{
            $content = ucfirst($this->task->heading) . '<p>
            <b style="color: green">' . __('app.dueOn') . ' : ' . '--' . '</b>
        </p>';
        }

        return (new MailMessage)
            ->subject(__('email.reminder.subject') . ' - ' . config('app.name') . '!')
            ->greeting(__('email.hello') . ' ' . ucwords($notifiable->name) . '!')
            ->markdown('mail.task.reminder', ['url' => getDomainSpecificUrl($url, $notifiable->company), 'content' => $content]);
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
        $slack = SlackSetting::first();
        if (count($notifiable->employee) > 0 && (!is_null($notifiable->employee[0]->slack_username) && ($notifiable->employee[0]->slack_username != ''))) {
            return (new SlackMessage())
                ->from(config('app.name'))
                ->image($slack->slack_logo_url)
                ->to('@' . $notifiable->employee[0]->slack_username)
                ->content('*' . __('email.reminder.subject') . '*' . "\n" . ucfirst($this->task->heading) . "\n" . __('app.dueDate') . ': ' . $this->task->due_date->format('d M, Y'));
        }
        return (new SlackMessage())
            ->from(config('app.name'))
            ->image($slack->slack_logo_url)
            ->content('This is a redirected notification. Add slack username for *' . ucwords($notifiable->name) . '*');
    }

    public function toOneSignal($notifiable)
    {
        return OneSignalMessage::create()
            ->subject(__('email.reminder.subject'))
            ->body($this->task->heading);
    }

}
