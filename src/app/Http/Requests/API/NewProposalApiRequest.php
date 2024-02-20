<?php

namespace App\Http\Requests\API;

use InfyOm\Generator\Request\APIRequest;

class NewProposalAPIRequest extends APIRequest
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
            'name' => 'required|string|max:255',
            'nodin' => 'required|string|max:255',
            'division_id' => 'required|integer',
            'unit_mpp_id' => 'required|string',
            'nodin_date' => 'required|string',
            'rab_amount' => 'required|integer',

        ];
        // 'justifikasi_file' => 'nullable',
            // 'tor_file' => 'nullable',
            // 'rab_file' => 'required',
            // 'nodin_file' => 'required',
            // 'ba_file' => 'nullable',
    }
}
