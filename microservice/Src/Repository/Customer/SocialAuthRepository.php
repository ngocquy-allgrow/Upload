<?php

namespace MicroService\Src\Repository\Customers;

use Hash;
use Cache;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Contracts\User as ProviderUser;
use Laravel\Socialite\Two\User;
use App\Models\SocialAccount;
use App\Models\Customer;

class SocialAuthRepository
{
    const ACTIVE_CUSTOMER = 1;

    public function createAuthSocialRepository($request)
    {
        $model = new User;
        $model->token = $request->get('token');
        $model->refreshToken = $request->get('refreshToken');
        $model->expiresIn = $request->get('expiresIn');
        $model->id = $request->get('id');
        $model->nickname = $request->get('nickname');
        $model->name = $request->get('name');
        $model->email = $request->get('email');
        $model->avatar_original = $request->get('avatar_original');
        $model->profileUrl = $request->get('profileUrl');
        
        return self::getUserRepository($model, $request->get('social'));
    }

    private function getUserRepository(ProviderUser $providerUser, $social)
    {
        $account = SocialAccount::whereProvider($social)
            ->whereProviderUserId($providerUser->getId())
            ->first();

        if ($account) {
            return $account;
        } else {
            $email = $providerUser->getEmail() ?? $providerUser->getNickname();
            $account = new SocialAccount([
                'provider_user_id' => $providerUser->getId(),
                'provider' => $social,
                'url' => $providerUser->avatar_original,
                'expires_time' => $providerUser->expiresIn,
                'token' => $providerUser->token,
            ]);
    
            $customer = Customer::whereEmail($email)->first();
            if (!$customer) {
                $customer = Customer::create([
                    'id' => customerId(Customer::count()),
                    'email' => $email,
                    'name' => $providerUser->getName(),
                    'status' => self::ACTIVE_CUSTOMER,
                    'password' => Hash::make($providerUser->getId()),
                ]);
            }

            $account->customer()->associate($customer);
            $account->save();

            $std = new \stdClass;
            $std->data = $customer;

            return $std;
        }
    }
}