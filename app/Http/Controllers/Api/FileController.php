<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use MicroService\Src\Entity\Json\GetEntity;
use MicroService\Src\Repository\FileRepository;

class FileController
{
    protected $params_request;
    protected $_fileRepository;

    public function __construct(FileRepository $fileRepository)
    {
        $this->_fileRepository = $fileRepository;
    }

    public function upload(Request $request)
    {
        $data     = $this->_fileRepository->uploadRepository($request);
        $get_json = new GetEntity($data);
        $result   = $get_json->toJson();

        return $result;
    }
}