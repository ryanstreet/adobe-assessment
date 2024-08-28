<?php

namespace App\Http\Controllers;

use App\Models\AdobeApi;
use App\Models\S3Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller handling incoming /autoTone requests.
 *
 * @see routes/web.php
 */
class AutoToneController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function __invoke()
    {
        $originalImageName = request('original_image_name');

        $inputImageUrl = S3Storage::getTemporaryUrl($originalImageName);

        $outputImageUrl = S3Storage::getTemporaryUploadUrl('transformed-' . $originalImageName);

        $api = new AdobeApi();

        try {
            $body = [
                'inputs' => [
                    'href' => $inputImageUrl,
                    'storage' => 'external'
                ],
                'outputs' => [
                    [
                        'href' => $outputImageUrl,
                        'type' => $api->getMimeType($originalImageName),
                        'storage' => 'external'
                    ]
                ]
            ];

            $jobId = $api->postEndpoint(AdobeApi::AUTO_TONE_URL, $body)->getJobId();
            $jsonResponse = [
                'job_id' => $jobId,
                'edited_image_name' => 'transformed-' . $originalImageName,
            ];
        } catch (\Exception $e) {
            $jsonResponse = [
                'error' => true,
                'errorMessage' => $e->getMessage()
            ];
        }

        return response()->json($jsonResponse);

    }
}
