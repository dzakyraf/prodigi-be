<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

/**
* @summary Update user
*
* @description
* This request should be used for updating the user data
*
* @_204 Successful
*
* @is_active will indicate whether the user is active or not
*/
class RegisterRequest extends FormRequest
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
     * Validation Rules
     *
     * @return array
     */
    public function rules()
    {
        return[
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ];
    }
}
