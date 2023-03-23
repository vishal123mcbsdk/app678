<?php

namespace App\Http\Controllers\Front;

use App\Company;
use App\GlobalSetting;
use App\Helper\Reply;
use App\Http\Requests\Front\Register\StoreRequest;
use App\Notifications\EmailVerificationSuccess;
use App\Role;
use App\Scopes\CompanyScope;
use App\SeoDetail;
use App\SignUpSetting;
use App\User;
use App\TrFrontDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RegisterController extends FrontBaseController
{

    public function index()
    {
        $this->global = GlobalSetting::first();

        if ($this->global->enable_register == 0) {
            return redirect(getDomainSpecificUrl(route('login')));
        }

        if (\user()) {
            return redirect(getDomainSpecificUrl(route('login'), \user()->company));
        }

        $this->seoDetail = SeoDetail::where('page_name', 'home')->first();
        $this->pageTitle = 'Sign Up';

        $view = ($this->setting->front_design == 1) ? 'saas.register' : 'front.register';


        if ($this->global->frontend_disable || $this->global->setup_homepage == 'custom') {
            $view = 'auth.register';
        }

        $trFrontDetailCount = TrFrontDetail::select('id', 'language_setting_id')->where('language_setting_id', $this->localeLanguage ? $this->localeLanguage->id : null)->count();

        $this->trFrontDetail = TrFrontDetail::where('language_setting_id', $trFrontDetailCount > 0 ? ($this->localeLanguage ? $this->localeLanguage->id : null) : null)->first();
        $this->defaultTrFrontDetail = TrFrontDetail::where('language_setting_id', null)->first();

        $signUpCount = SignUpSetting::select('id', 'language_setting_id')->where('language_setting_id', $this->localeLanguage ? $this->localeLanguage->id : null)->count();
        $this->signUpMessage = SignUpSetting::where('language_setting_id', $signUpCount > 0 ? ($this->localeLanguage ? $this->localeLanguage->id : null) : null)->first();

        $this->registrationStatus = $this->global;
        return view($view, $this->data);
    }

    public function store(StoreRequest $request)
    {
        $company = new Company();

        if (!$company->recaptchaValidate($request)) {
            return Reply::error('Recaptcha not validated.');
        }

        $superadmin = User::withoutGlobalScopes([CompanyScope::class])
            ->where('super_admin', '1')
            ->where('email', $request->email)
            ->first();

        if ($superadmin) {
            return Reply::error(__('messages.cannotUseEmail'));
        }

        DB::beginTransaction();
        try {
            $company->company_name = $request->company_name;
            $company->company_email = $request->email;

            if (module_enabled('Subdomain')) {
                $company->sub_domain = $request->sub_domain;
            }
            
            $company->save();

            $user = $company->addUser($company, $request);
            $message = $company->addEmployeeDetails($user);
            $company->assignRoles($user);

            DB::commit();
        } catch (\Swift_TransportException $e) {
            DB::rollback();
            return Reply::error('Please contact administrator to set SMTP details to add company', 'smtp_error');
        } catch (\Exception $e) {
            Log::info($e);
            DB::rollback();
            return Reply::error('Some error occurred when inserting the data. Please try again or contact support');
        }
        return Reply::success($message);
    }

    public function getEmailVerification($code)
    {
        $this->pageTitle = 'modules.accountSettings.emailVerification';
        $this->message = User::emailVerify($code);
        return view('auth.email-verification', $this->data);
    }

}
