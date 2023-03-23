<?php

namespace App\Http\Requests\Admin\Client;

use App\Http\Requests\CoreRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends CoreRequest
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
        $rules = [
            'name' => 'required',
            'password' => 'required',
            'email' => 'required|email',
            // 'slack_username' => 'nullable|unique:employee_details,slack_username',
           // 'website' => 'nullable',
        //            'facebook' => 'nullable|regex:/http(s)?:\/\/(www\.)?(facebook|fb)\.com\/(A-z 0-9)?/',
        //            'twitter' => 'nullable|regex:/http(s)?://(.*\.)?twitter\.com\/[A-z 0-9 _]+\/?/',
        //            'linkedin' => 'nullable|regex:/((http(s?)://)*([www])*\.|[linkedin])[linkedin/~\-]+\.[a-zA-Z0-9/~\-_,&=\?\.;]+[^\.,\s<]/',

        ];
        if (!is_null(request()->get('website'))) {
            $type = request()->get('website');
            if(str_contains($type, 'http://') || str_contains($type, 'http://')){
           
            }else{
                if(is_null(request()->get('hyper_text'))){
                    $rules['website'] = 'url';
                    $rules['hyper_text'] = 'required';
                }else{
                    $rules['website'] = 'required';
                }
               
            }
        }elseif(!is_null(request()->get('hyper_text'))){
            $rules['website'] = 'required';
        }
        if (request()->get('custom_fields_data')) {
            $fields = request()->get('custom_fields_data');
            foreach ($fields as $key => $value) {
                $idarray = explode('_', $key);
                $id = end($idarray);
                $customField = \App\CustomField::findOrFail($id);
                if ($customField->required == 'yes' && (is_null($value) || $value == '')) {
                    $rules["custom_fields_data[$key]"] = 'required';
                }
            }
        }

        return $rules;
    }

    public function attributes()
    {
        $attributes = [];
        if (request()->get('custom_fields_data')) {
            $fields = request()->get('custom_fields_data');
            foreach ($fields as $key => $value) {
                $idarray = explode('_', $key);
                $id = end($idarray);
                $customField = \App\CustomField::findOrFail($id);
                if ($customField->required == 'yes') {
                    $attributes["custom_fields_data[$key]"] = $customField->label;
                }
            }
        }
        return $attributes;
    }

}
