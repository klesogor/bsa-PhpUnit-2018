<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddLotRequest extends FormRequest
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
            'price' => 'required|numeric|min:0',
            'date_time_open' => 'required|date_format:Y/m/d H:i:s',
            'date_time_close' => 'required|date_format:Y/m/d H:i:s|after:date_time_open',
            'currency_id' => 'required|integer|min:0'
        ];
    }
}
