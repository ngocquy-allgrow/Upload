<?php

namespace App\Http\Requests\Customer;

use App\Http\Requests\AbstractApiRequest;

class UpdatePasswordRequest extends AbstractApiRequest
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
            'current_pass'            => 'required|max:80|min:8',
            'password'                => 'required|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/|max:64|min:8|confirmed',
            'password_confirmation'   => 'required',
        ];
    }

    public function messages()
    {
        return [
            'current_pass.required'                    => 'Update_Pass_Cutomer_001',
            'current_pass.max'                         => 'Update_Pass_Cutomer_002',
            'current_pass.min'                         => 'Update_Pass_Cutomer_003',
            'password.required'                        => 'Update_Pass_Cutomer_004',
            'password.max'                             => 'Update_Pass_Cutomer_005',
            'password.min'                             => 'Update_Pass_Cutomer_006',
            'password.confirmed'                       => 'Update_Pass_Cutomer_007',
            'password_confirmation.required'           => 'Update_Pass_Cutomer_008',
            'password_confirmation.max'                => 'Update_Pass_Cutomer_009',
            'password_confirmation.min'                => 'Update_Pass_Cutomer_0010',
            'password_confirmation.confirmed'          => 'Update_Pass_Cutomer_0011',
        ];
    }
}
