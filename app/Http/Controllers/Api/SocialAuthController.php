<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use MicroService\Src\Entity\Json\GetEntity;
use MicroService\Src\Repository\Customers\SocialAuthRepository;

class SocialAuthController
{
    private $_socialAuthRepository;

    public function __construct(SocialAuthRepository $_socialAuthRepository)
    {
        $this->_socialAuthRepository = $_socialAuthRepository;
    }

    public function authSocial(Request $request)
    {
        $data     = $this->_socialAuthRepository->createAuthSocialRepository($request);
        $get_json = new GetEntity($data);
        $result   = $get_json->toJson();

        return $result;
    }
}