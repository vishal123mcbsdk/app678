<?php

namespace App\Http\Requests\Admin\Contract;

use App\Company;

use App\Rules\CheckDateFormat;
use Froiden\LaravelInstaller\Request\CoreRequest;

class UpdateRequest extends CoreRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $setting = Company::withoutGlobalScope('active')->where('id', company()->id)->first();
        return [
            'client' => 'required',
            'subject' => 'required',
            'amount' => 'required',
            'contract_type' => 'required|exists:contract_types,id',
            'start_date' => ['required' , new CheckDateFormat(null, $setting->date_format)],
          //  'end_date' => 'required|date',
        ];
    }

}
