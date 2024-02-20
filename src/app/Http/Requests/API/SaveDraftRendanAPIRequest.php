<?php

namespace App\Http\Requests\API;

use InfyOm\Generator\Request\APIRequest;

class SaveDraftRendanAPIRequest extends APIRequest
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
            'procurement_id' => 'required|integer',
            'procurement_type_id' => 'nullable|integer',
        ];
        // 'justifikasi_file' => 'nullable',
            // 'tor_file' => 'nullable',
            // 'rab_file' => 'required',
            // 'nodin_file' => 'required',
            // 'ba_file' => 'nullable',
    }
}
