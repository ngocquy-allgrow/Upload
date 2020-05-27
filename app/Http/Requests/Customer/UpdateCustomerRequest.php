<?php

namespace App\Http\Requests\Customer;

use App\Http\Requests\AbstractApiRequest;

class UpdateCustomerRequest extends AbstractApiRequest
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
            'first_name'            => 'required|min:2|max:20',
            'last_name'             => 'required|min:2|max:20',
            'address'               => 'required|min:8|max:64',
            'phone'                 => 'regex:/^[0-9]+$/'
        ];
    }

    public function messages()
    {
        return [
            'first_name.required'     => 'Update_Cutomer_001',
            'first_name.min'          => 'Update_Cutomer_002',
            'first_name.max'          => 'Update_Cutomer_003',
            'last_name.required'      => 'Update_Cutomer_004',
            'last_name.min'           => 'Update_Cutomer_005',
            'last_name.max'           => 'Update_Cutomer_006',
            'address.required'        => 'Update_Cutomer_007',
            'address.min'             => 'Update_Cutomer_008',
            'address.max'             => 'Update_Cutomer_009',
            'phone.regex'             => 'Update_Cutomer_0010',
        ];
    }
}
