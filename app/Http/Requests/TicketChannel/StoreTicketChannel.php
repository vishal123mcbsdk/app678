<?php

namespace App\Http\Requests\TicketChannel;

use App\Http\Requests\CoreRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreTicketChannel extends CoreRequest
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
            'channel_name' => 'required|unique:ticket_channels,channel_name,null,null,company_id,' . company_setting()->id
        ];
    }

}
