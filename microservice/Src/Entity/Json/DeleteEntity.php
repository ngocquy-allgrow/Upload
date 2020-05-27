<?php

namespace MicroService\Src\Entity\Json;

use Illuminate\Http\JsonResponse;

class DeleteEntity extends BasicEntity
{
    const NOT_FOUND_DATA = 'DATA_NOT_FOUND';

    public function __construct()
    {
        parent::__construct();
        $this->setStatus(JsonResponse::HTTP_OK);
    }

    public function setParamByResponse($response)
    {
        $dataJson = (object)$response->data;
        if (isset($dataJson->status_response))
        {
            $this->setStatus($dataJson->status_response);
            $this->setMessageStatus($dataJson->message);
        }
        
        $dataJson = [];
        if ($response->data === false) {
            $this->setMessageStatus(self::NOT_FOUND_DATA);
        }

        $this->setResponse($dataJson);
    }
}