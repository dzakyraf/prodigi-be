<?php

namespace App\Http\Requests\API\Auth;

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
class ForgotPasswordRequest extends FormRequest
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
            'email' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ];
    }
}
