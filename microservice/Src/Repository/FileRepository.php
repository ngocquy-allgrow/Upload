<?php

namespace MicroService\Src\Repository;

use Illuminate\Http\JsonResponse;
use Image as ImageIntervention;

class FileRepository
{
    public $data = [];
    public $folder;

    public function __construct($folder = 'screenshot')
    {
        $this->folder = $folder . '/';
    }

    public function uploadRepository($request)
    {
        $folderUser = $request->input('folder');
        if ($request->file('file') && $folderUser) {
            $originalImage= $request->file('file');
            $thumbnailImage = ImageIntervention::make($originalImage);
            if (!\File::exists(public_path().'/storage/'. $this->folder .'/'. $folderUser)) {
                mkdir(public_path().'/storage/'. $this->folder .'/'. $folderUser, 0777, TRUE);
            }
            $fileName = $originalImage->getClientOriginalName();
            $pathPublic = '/storage/'. $this->folder. '/'. $folderUser .'/'. $fileName;
            $path = public_path(). $pathPublic;
            $thumbnailImage->save($path);
        }
        
        try {
            $this->data = $pathPublic;
        } catch(\Exception $e) {
            $this->data['message'] = $e->getMessage();
            $this->data['status_response'] =  JsonResponse::HTTP_UNAVAILABLE_FOR_LEGAL_REASONS;
        }
        
        return $this;
    }
}