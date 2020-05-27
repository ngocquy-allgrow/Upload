<?php

namespace MicroService\Src\Interfaces;

interface interfaceResponse
{
    function setMessageStatus($message);

    function setStatus($status);

    function setResponse($body);

    public function toJson();
}