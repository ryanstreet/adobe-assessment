<?php

namespace App\Models;

use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Calls the external Adobe API
 */
class AdobeApi
{
    const APPLY_EDITS_URL = 'https://image.adobe.io/lrService/edit';
    const AUTO_STRAIGHTEN_URL = 'https://image.adobe.io/lrService/autoStraighten';
    const AUTO_TONE_URL = 'https://image.adobe.io/lrService/autoTone';
    const JOB_STATUS_URL = 'https://image.adobe.io/lrService/status/';
    const TOKEN_URL = 'https://ims-na1.adobelogin.com/ims/token/v3';

    /**
     * @var Response
     */
    protected Response $response;

    /**
     * The Bearer token
     *
     * @var string $token
     */
    private string $token;

    /**
     * @param string $endpointUrl
     * @param array $body
     * @return AdobeApi
     * @throws ConnectionException
     */
    public function postEndpoint(string $endpointUrl, array $body): AdobeApi
    {
        $request = Http::withHeader('x-api-key', config('adobe.firefly.key'))
            ->withToken($this->getToken());

        /**
         * @var $response Response
         */
        $this->response = $request
            ->asJson()
            ->post($endpointUrl, $body);

        return $this;
    }

    /**
     * @param string $endpointUrl
     * @return $this
     * @throws ConnectionException
     */
    public function getEndpoint(string $endpointUrl): AdobeApi
    {
        $request = Http::withHeader('x-api-key', config('adobe.firefly.key'))
            ->withToken($this->getToken());

        /**
         * @var $response Response
         */
        $this->response = $request
            ->asJson()
            ->get($endpointUrl);

        return $this;
    }

    /**
     * @return Response
     * @throws Exception
     */
    public function getResponse(): Response
    {
        if (!$this->response) {
            throw new Exception('No Valid Response');
        }

        return $this->response;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getJobId(): string
    {
        return Arr::last(explode('/', Arr::get($this->getResponse(), '_links.self.href')));
    }

    /**
     * @param $fileName
     * @return string
     * @throws Exception
     */
    public function getMimeType($fileName): string
    {
        $explode = explode('.', strtolower($fileName));
        $extension = end($explode);
        return match ($extension) {
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            default => throw new Exception('invalid Mime Type'),
        };
    }

    /**
     * @return string
     * @throws ConnectionException
     */
    protected function getToken(): string
    {
        if (!session('token')) {
            session(['token' => $this->getTokenFromApi()]);
        }

        return session('token');
    }

    /**
     * @return string
     * @throws ConnectionException
     */
    protected function getTokenFromApi(): string
    {
        $body = [
            'grant_type' => 'client_credentials',
            'client_id' => config('adobe.firefly.key'),
            'client_secret' => config('adobe.firefly.secret'),
            'scope' => 'openid,AdobeID,session,additional_info,read_organizations,firefly_api,ff_apis'
        ];

        try {
            $response = Http::withHeader('Content-Type', 'application/x-www-form-urlencoded')
                ->asForm()
                ->post(self::TOKEN_URL, $body);

            return $response->json('access_token');

        } catch (Exception $e) {
            Log::notice($e->getMessage());
            throw $e; // throwing on purpose to stop the application.
        }
    }
}
