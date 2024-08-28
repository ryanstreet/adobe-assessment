<?php

namespace App\Http\Controllers;

use App\Models\S3Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Returns edited image URL from S3 Bucket via getEditedImageName/{originalImageName}
 *
 * @see routes/web.php
 */
class GetEditedImageUrlController extends Controller
{
    /**
     * @param Request $request
     * @param $editedImageName
     * @return JsonResponse
     */
    public function __invoke(Request $request, $editedImageName)
    {
        return response()->json(['image_url' => S3Storage::getTemporaryUrl($editedImageName)]);
    }
}
