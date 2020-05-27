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
        if ($request->file('file')) {
            $originalImage= $request->file('file');
            $thumbnailImage = ImageIntervention::make($originalImage);
            if (!\File::exists(public_path().'/storage/'. $this->folder)) {
                mkdir(public_path().'/storage/'.$this->folder, 0777, TRUE);
            }
            $fileName = time().'_'.$originalImage->getClientOriginalName();
            $path = public_path().'/storage/'.$this->folder. $fileName;
            $thumbnailImage->save($path);
        }
        
        try {
            $this->data = $path;
        } catch(\Exception $e) {
            $this->data['message'] = $e->getMessage();
            $this->data['status_response'] =  JsonResponse::HTTP_UNAVAILABLE_FOR_LEGAL_REASONS;
        }
        
        return $this;
    }
}