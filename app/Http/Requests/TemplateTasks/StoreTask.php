<?php

namespace App\Http\Requests\TemplateTasks;

use App\Http\Requests\CoreRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreTask extends CoreRequest
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
        return [
            'title' => 'required',
            'user_id.0' => 'required',
            'priority' => 'required'
        ];
    }

    public function messages()
    {
        return [
          'project_id.required' => __('messages.chooseProject'),
          'user_id.0.required' => 'Choose an assignee'
        ];
    }

}
