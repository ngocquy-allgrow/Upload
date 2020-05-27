<?php

namespace MicroService\Src\Entity\Json;

use Illuminate\Http\JsonResponse;
use MicroService\Src\Interfaces\interfaceResponse;

class BasicEntity implements interfaceResponse
{
    protected $header;
    protected $response = [];
    protected $status = JsonResponse::HTTP_OK;
    public $api_name;

    public function __construct()
    {
        $this->setMessageStatus('success');
        $this->setResponse([]);

        if(request()->route())
        {
            $this->response['api_name'] = request()->route()->getActionMethod();
        } else {
            $this->response['api_name'] = 'Api not found!';
        }
    }

    public function setMessageStatus($message)
    {
        $this->response['message'] = $message;
    }

    public function setVerifyCode($code)
    {
        $this->response['error_code'] = $code;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setResponse($response)
    {
        $this->response['data'] = $response;
    }

    public function setMessageSuccess($message)
    {
        $this->response['status'] = $message;
    }

    /**
     * responseJson. be call from controller
     * @return json
     */
    function toJson(){
        return response()->json($this->response, $this->status);
    }

    function toJsonHeader($token){
        return response()->json($this->response, $this->status)
                        ->header('Content-Type', 'application/json')
                        ->header('Authorization', 'Bearer '.$token);
    }
}