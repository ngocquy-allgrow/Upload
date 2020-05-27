<?php

namespace MicroService\Src\Entity\Json;

use Illuminate\Http\JsonResponse;

class CreateEntity extends BasicEntity
{
    public function __construct()
    {
        parent::__construct();
        $this->setStatus(JsonResponse::HTTP_CREATED);
    }

    /**
     * Set params infor error
     * Error 500 is error server
     * JsonResponse::HTTP_INTERNAL_SERVER_ERROR: 500
     */
    public function setParamByResponse($response)
    {
        $dataJson = (object)$response->data;
        if (isset($dataJson->status_response))
        {
            $this->setStatus($dataJson->status_response);
            $this->setMessageStatus($dataJson->message);
            $dataJson = [];
        }
            
        $this->setResponse($dataJson);
    }
}