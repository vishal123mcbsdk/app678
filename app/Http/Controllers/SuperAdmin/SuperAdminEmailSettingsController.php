<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use App\Http\Requests\SmtpSetting\UpdateSmtpSetting;
use App\Notifications\TestEmail;
use App\SmtpSetting;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class SuperAdminEmailSettingsController extends SuperAdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.emailSettings';
        $this->pageIcon = 'icon-settings';
    }

    public function index()
    {
        $this->smtpSetting = SmtpSetting::first();
        return view('super-admin.email-settings.index', $this->data);
    }

    public function update(UpdateSmtpSetting $request)
    {
        $smtp = SmtpSetting::first();

        $data = $request->all();

        if ($request->mail_encryption == 'null') {
            $data['mail_encryption'] = null;
        }

        $smtp->update($data);
        $response = $smtp->verifySmtp();
        session(['smtp_setting' => $smtp]);

        if ($smtp->mail_driver == 'mail') {
            return Reply::success(__('messages.settingsUpdated'));
        }


        if ($response['success']) {
            return Reply::success($response['message']);
        }
        // GMAIL SMTP ERROR
        $message = __('messages.smtpError').'<br><br> ';

        if ($smtp->mail_host == 'smtp.gmail.com')
        {
            $secureUrl = 'https://myaccount.google.com/lesssecureapps';
            $message .= __('messages.smtpSecureEnabled');
            $message .= '<a  class="font-13" target="_blank" href="' . $secureUrl . '">' . $secureUrl . '</a>';
            $message .= '<hr>' . $response['message'];
            return Reply::error($message);
        }
        return Reply::error($message . '<hr>' . $response['message']);
    }

    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        $smtp = SmtpSetting::first();
        $response = $smtp->verifySmtp();

        if ($response['success']) {

            Notification::route('mail', \request()->test_email)->notify(new TestEmail());
            return Reply::success('Test mail sent successfully');
        }
        return Reply::error($response['message']);
    }

}
