<?php

namespace MicroService\Src\Entity\Json;

use Illuminate\Http\JsonResponse;

class GetEntity extends BasicEntity
{
    public function __construct($data)
    {
        parent::__construct();
        $this->setStatus(JsonResponse::HTTP_OK);

        if(isset($data->data['message']))
            $this->setMessageStatus($data->data['message']);
        if (isset($data->data['status_response']))
            $this->setStatus($data->data['status_response']);

        if (isset($data->data))
            $this->setResponse($data->data);
    }

    /**
     * HTTP_NOT_ACCEPTABLE is not accept.
     * Param requried incorect.
     * 
     * Code 195: Missing or invalid url parameter
     */
    public function setHeaderMiddlewareResponse()
    {
        $this->setStatus(JsonResponse::HTTP_NOT_ACCEPTABLE);
        $this->setVerifyCode(195);
        $this->setMessageStatus('secret key wrong !');
    }
}