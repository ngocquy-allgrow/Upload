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
}