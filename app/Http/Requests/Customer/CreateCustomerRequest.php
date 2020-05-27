<?php

namespace App\Http\Requests\Customer;

use App\Http\Requests\AbstractApiRequest;

class CreateCustomerRequest extends AbstractApiRequest
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
            'email'                   => 'required|max:80|min:8',
            'password'                => 'required|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/|max:64|min:8|confirmed',
            'password_confirmation'   => 'required',
        ];
    }

    public function messages()
    {
        return [
            'email.max'                        => 'Create_Customer_001',
            'email.min'                        => 'Create_Customer_002',
            'email.required'                   => 'Create_Customer_003',
            'password.max'                     => 'Create_Customer_004',
            'password.min'                     => 'Create_Customer_005',
            'password.required'                => 'Create_Customer_006',
            'password.regex'                   => 'Create_Customer_007',
            'password_confirmation.required'   => 'Create_Customer_008',
            'password.confirmed'               => 'Create_Customer_009',
        ];
    }
}
