<?php

namespace App\Http\Controllers;

use App\Models\AdobeApi;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Gets job status via /getJobStatus/{jobId}.
 *
 * @see routes/web.php
 */
class GetJobStatusController extends Controller
{
    /**
     * @param Request $request
     * @param string $jobId
     * @return JsonResponse
     */
    public function __invoke(Request $request, string $jobId): JsonResponse
    {
        $api = new AdobeApi();

        try {
            $response = $api->getEndpoint(AdobeApi::JOB_STATUS_URL . $jobId)->getResponse();
            $jsonResponse = [
                'status' => $response['outputs'][0]['status']
            ];
        } catch (Exception $e) {
            $jsonResponse = [
                'error' => true,
                'errorMessage' => $e->getMessage()
            ];
        }

        return response()->json($jsonResponse);
    }
}
