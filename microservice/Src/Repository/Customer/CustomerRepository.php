<?php

namespace MicroService\Src\Repository\Customer;

use Hash;
use JWTAuth;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use MicroService\Src\Repository\AbstractEloquentRepository;

class CustomerRepository extends AbstractEloquentRepository
{
    const USER_ACTIVED     = 1;
    const PERMISSION_ADMIN = 2;
    const PERMISSION_USER  = 1;

    public $data = [];

    use \MicroService\Src\Traits\Singleton;

     /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\Customer::class;
    }

    public function createRepository($request)
    {
        $data = [
            'id'            => customerId($this->count()),
            'email'         => $request->input('email'),
            'first_name'    => $request->input('first_name'),
            'last_name'     => $request->input('last_name'),
            'password'      => Hash::make($request->input('password')),
            'status'        => self::USER_ACTIVED,
        ];
        try {
            $this->data = $this->create($data);
        } catch(\Exception $e) {
            $this->data['message'] = $e->getMessage();
            $this->data['status_response'] =  JsonResponse::HTTP_UNAVAILABLE_FOR_LEGAL_REASONS;
        }
        
        return $this;
    }

    public function getAllRepository($request)
    {
        $limit = $request->get('limit', LIMIT_PAGE);
        $this->data = $this->paginate($limit);

        return $this;
    }

    public function detailCustomerRepository($request)
    {
        try {
            $userId = JWTAuth::user()->id;
            $this->data = $this->find($userId);
        } catch(\Exception $e) {
            $this->data['message'] = $e->getMessage();
            $this->data['status_response'] = JsonResponse::HTTP_NOT_FOUND;
        }

        return $this;
    }

    public function loginRepository($request)
    {   
        $option = [
            'email' => $request->get('email'),
            'password' => urldecode($request->get('password')),
        ];
        if (!$token = JWTAuth::attempt($option))
        {
            $this->data['status_response'] = JsonResponse::HTTP_NOT_FOUND;
            $this->data['message'] =  'Login_404';
        } else {
            $this->data['token'] = $token;
        }

        return $this;
    }

    public function logoutRepository($request)
    {
        $token = $request->header('Authorization');

        try {
            JWTAuth::invalidate($token);
            $this->data = "Logout_Success_002";
        } catch (JWTException $e) {
            $this->data['status_response'] = JsonResponse::UNAUTHORIZED;
            $this->data['message'] = "Logout_Failed_001";
        }

        return $this;
    }

    public function updateRepository($request)
    {
        $data = $request->only('last_name', 'first_name', 'address', 'phone');
        try {
            $checkPhoneExist = Customer::Phone($request->get('phone'))->differentId(JWTAuth::user()->id)->first();
            if (!empty($checkPhoneExist)) {
                $this->data['message'] = 'Customer_Exists_451';
                $this->data['status_response'] =  JsonResponse::HTTP_CONFLICT;
            } else {
                $this->data = $this->update(['id' => JWTAuth::user()->id], $data);
            }
        } catch(\Exception $e) {
            $this->data['message'] =  $e->getMessage();
            $this->data['status_response'] =  JsonResponse::HTTP_UNAVAILABLE_FOR_LEGAL_REASONS;
        }

        return $this;
    }

    public function deleteRepository($request)
    {
        try {
            $this->data = $this->delete($request->get('id'));
        } catch (\Exception $e) {
            $this->data['status_response'] = JsonResponse::HTTP_UNAVAILABLE_FOR_LEGAL_REASONS;
            $this->data['message'] =  $e->getMessage();
        }

        return $this;
    }

    public function refreshRepository($request)
    {
        try {
            $this->data['token'] = JWTAuth::refresh(JWTAuth::getToken());
        } catch(\JWTException $e) {
            $this->data['status_response'] = $e->getStatusCode();
            $this->data['message'] =  $e->getMessage();
        }

        return $this;
    }

    public function changePassword($request)
    {
        $option = [
            'password' => urldecode($request->input('current_pass')),
            'email' => JWTAuth::user()->email,
        ];
        if (!JWTAuth::attempt($option))
        {
            $this->data['status_response']  = JsonResponse::HTTP_UNAUTHORIZED;
            $this->data['message'] = "Update_Password_401";
        } else {
            $option['password'] = urldecode($request->input('password'));
            if (JWTAuth::attempt($option))
            {
                $this->data['status_response']  = JsonResponse::HTTP_CONFLICT;
                $this->data['message'] = "Update_Password_409";
            } else {
                try {

                    $option = ['password' => Hash::make(urldecode($request->input('password')))];
                    $this->data = $this->update(['id' => JWTAuth::user()->id], $option);
                } catch (JWTException $e) {
                    $this->data['status_response']  = JsonResponse::HTTP_UNAVAILABLE_FOR_LEGAL_REASONS;
                    $this->data['message'] = $e->getMessage();
                }
            }
        }

        return $this;
    }

    public function fogotPassword($request)
    {
        $user = Customer::findEmail($request->get('email'))->active()->sampleForgot()->get();
        if (count($user->toArray()) == 0)
        {
            $this->data['message'] = 'Forgot_Password_404';
            $this->data['status_response'] = JsonResponse::HTTP_NOT_FOUND;
        } else if(count($user->toArray()) > 1) {
            $this->data['message'] = 'Forgot_Password_409';
            $this->data['status_response'] = JsonResponse::HTTP_CONFLICT;
        } else {

            $user = (object)$user->toArray()[0];
            $newPassword = generatePassword(8);
            $option = ['password' => Hash::make($newPassword)];
            try {
                $this->data = Customer::GetId($user->id)->updatePassword($option);
            } catch (\Exception $e) {
                $this->data['status_response']  = JsonResponse::HTTP_UNAVAILABLE_FOR_LEGAL_REASONS;;
                $this->data['message'] = $e->getMessage();
            }
            if ((boolean)$this->data === true)
                Customer::sendMailForgotPassword($this->data, $request, $user, $newPassword);
        }

        return $this;
    }
}