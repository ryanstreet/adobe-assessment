<?php

namespace App\Http\Controllers;

use App\Models\S3Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UploadPhotoController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request)
    {
        $photo = request('photo');

        S3Storage::uploadPhoto($photo->getClientOriginalName(), $photo->getContent());

        $url = S3Storage::getTemporaryUrl($photo->getClientOriginalName());

        $response = [
            'photo' => $url,
            'original_image_name' => $photo->getClientOriginalName()
        ];


        return response()->json($response);
    }
}
