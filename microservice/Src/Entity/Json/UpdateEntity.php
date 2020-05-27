<?php

namespace MicroService\Src\Entity\Json;

use Illuminate\Http\JsonResponse;

class UpdateEntity extends BasicEntity
{
    const NOT_CHANGE_DATA = 'DATA_NOT_CHANGE';
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
        if ($response->data === 0){
            $this->setMessageStatus(self::NOT_CHANGE_DATA);
        } else if ($response->data === null) {
            $this->setMessageStatus(self::NOT_FOUND_DATA);
        }

        $this->setResponse($dataJson);
    }
}