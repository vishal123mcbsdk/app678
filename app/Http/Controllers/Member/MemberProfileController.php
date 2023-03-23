<?php

namespace App\Http\Controllers\Member;

use App\EmployeeDetails;
use App\GoogleCalendarModules;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\User\UpdateProfile;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class MemberProfileController extends MemberBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.profileSettings';
        $this->pageIcon = 'icon-user';
    }

    public function index()
    {
        $this->userDetail = auth()->user();
        $this->employeeDetail = EmployeeDetails::where('user_id', '=', $this->userDetail->id)->first();
        return view('member.profile.edit', $this->data);
    }

    public function update(UpdateProfile $request, $id)
    {
        config(['filesystems.default' => 'local']);

        $user = User::withoutGlobalScope('active')->findOrFail($id);
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->gender = $request->input('gender');
        if ($request->password != '') {
            $user->password = Hash::make($request->input('password'));
        }
        $user->mobile = $request->input('mobile');
        $user->email_notifications = $request->email_notifications;

        if ($request->hasFile('image')) {
            Files::deleteFile($user->image, 'avatar');
            $user->image = Files::upload($request->image, 'avatar', 300);
        }

        $user->save();

        $validate = Validator::make(['address' => $request->address], [
            'address' => 'required'
        ]);

        if ($validate->fails()) {
            return Reply::formErrors($validate);
        }

        $employee = EmployeeDetails::where('user_id', '=', $user->id)->first();
        if (empty($employee)) {
            $employee = new EmployeeDetails();
            $employee->user_id = $user->id;
        }
        $employee->address = $request->address;
        $employee->save();

        // Setting for google calendar setting
        $userCalendarModule = ($user->calendar_module) ? $user->calendar_module : new GoogleCalendarModules();
        $userCalendarModule->user_id            = user()->id;
        $userCalendarModule->lead_status        = ($request->has('lead_status') && $request->lead_status == 'yes') ? 1 : 0;
        $userCalendarModule->leave_status       = ($request->has('leave_status') && $request->leave_status == 'yes') ? 1 : 0;
        $userCalendarModule->invoice_status     = ($request->has('invoice_status') && $request->invoice_status == 'yes') ? 1 : 0;
        $userCalendarModule->contract_status    = ($request->has('contract_status') && $request->contract_status == 'yes') ? 1 : 0;
        $userCalendarModule->task_status        = ($request->has('task_status') && $request->task_status == 'yes') ? 1 : 0;
        $userCalendarModule->event_status       = ($request->has('event_status') && $request->event_status == 'yes') ? 1 : 0;
        $userCalendarModule->holiday_status     = ($request->has('holiday_status') && $request->holiday_status == 'yes') ? 1 : 0;
        $userCalendarModule->save();

        session()->forget('user');
        $this->logUserActivity($user->id, __('messages.updatedProfile'));
        return Reply::redirect(route('member.profile.index'), __('messages.profileUpdated'));
    }

    public function updateOneSignalId(Request $request)
    {
        $user = User::find($this->user->id);
        $user->onesignal_player_id = $request->userId;
        $user->save();
    }

    public function changeLanguage(Request $request)
    {
        $setting = User::findOrFail($this->user->id);
        $setting->locale = $request->input('lang');
        $setting->save();
        session()->forget('user');
        return Reply::success('Language changed successfully.');
    }

}
