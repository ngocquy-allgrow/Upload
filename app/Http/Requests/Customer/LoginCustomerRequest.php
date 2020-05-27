<?php

namespace App\Http\Requests\Customer;

use App\Http\Requests\AbstractApiRequest;

class LoginCustomerRequest extends AbstractApiRequest
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
            'email'       => 'required|max:80|min:8',
            'password'    => 'required|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/|max:64|min:8',
        ];
    }

    public function messages()
    {
        return [
            'email.max'              => 'Login_Customer_001',
            'email.min'              => 'Login_Customer_002',
            'email.required'         => 'Login_Customer_003',
            'password.max'           => 'Login_Customer_004',
            'password.min'           => 'Login_Customer_005',
            'password.required'      => 'Login_Customer_006',
            'password.regex'         => 'Login_Customer_007',
        ];
    }
}
