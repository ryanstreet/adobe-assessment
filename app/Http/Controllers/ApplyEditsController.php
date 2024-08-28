<?php

namespace App\Http\Controllers;

use App\Models\AdobeApi;
use App\Models\S3Storage;
use Illuminate\Http\JsonResponse;

/**
 * Controller handling incoming /applyEdits requests.
 *
 * @see routes/web.php
 */
class ApplyEditsController extends Controller
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
                    'source' => [
                        'href' => $inputImageUrl,
                        'storage' => 'external'
                    ]
                ],
                'outputs' => [
                    [
                        'href' => $outputImageUrl,
                        'type' => $api->getMimeType($originalImageName),
                        'storage' => 'external'
                    ]
                ],
                'options' => [
                    'Saturation' => (int)request('saturation'),
                    'Contrast' => (int)request('contrast'),
                    'Vibrance' => (int)request('vibrance'),
                    'Highlights' => (int)request('highlights'),
                    'Shadows' => (int)request('shadows'),
                    'Whites' => (int)request('whites'),
                    'Blacks' => (int)request('blacks'),
                    'Clarity' => (int)request('clarity')
                ]
            ];

            $jobId = $api->postEndpoint(AdobeApi::APPLY_EDITS_URL, $body)->getJobId();
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
