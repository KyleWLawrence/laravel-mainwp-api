<?php

namespace KyleWLawrence\MainWP\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use KyleWLawrence\MainWP\Exceptions\AuthException;
use KyleWLawrence\MainWP\Http\Middleware\RetryHandler;
use KyleWLawrence\MainWP\Http\Resources\Send;
use KyleWLawrence\MainWP\Utilities\Auth;

/**
 * Client class, base level access
 *
 * @method Send send()
 */
class HttpClient
{
    const VERSION = '1.0.0';

    private array $headers = [];

    protected string $apiBasePath = '';

    protected string $apiUrl;

    protected ?string $zone = null;

    protected Auth $auth;

    /**
     * @param  \GuzzleHttp\Client  $guzzle
     */
    public function __construct(
        protected string $hostname = 'api.mainwp.com',
        protected string $scheme = 'https',
        protected string $base = 'client/v4',
        public ?Client $guzzle = null,
        protected Debug $debug = new Debug,
    ) {
        if (is_null($guzzle)) {
            $handler = HandlerStack::create();
            $handler->push(new RetryHandler(['retry_if' => function ($retries, $request, $response, $e) {
                return $e instanceof RequestException && strpos($e->getMessage(), 'ssl') !== false;
            }]), 'retry_handler');
            $this->guzzle = new \GuzzleHttp\Client(compact('handler'));
        } else {
            $this->guzzle = $guzzle;
        }

        $this->setApiUrl();
        $this->debug = new Debug();
    }

    public function getAuth(): Auth
    {
        return $this->auth;
    }

    /**
     * Configure the authorization method
     *
     * @throws AuthException
     */
    public function setAuth(string $strategy, array $options): HttpClient
    {
        $this->auth = new Auth($strategy, $options);

        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param  string  $key The name of the header to set
     * @param  string  $value The value to set in the header
     *
     * @internal param array $headers
     */
    public function setHeader($key, $value): HttpClient
    {
        $this->headers[$key] = $value;

        return $this;
    }

    /**
     * Return the user agent string
     */
    public function getUserAgent(): string
    {
        return 'MainWP API PHP '.self::VERSION;
    }

    /**
     * Returns the generated api URL
     */
    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    public function setApiUrl(): HttpClient
    {
        $zone = ($this->zone) ? "zones/$this->zone/" : '';
        $this->apiUrl = "$this->scheme://$this->hostname/$this->base/$zone";

        return $this;
    }

    public function getApiBasePath(): string
    {
        return $this->apiBasePath;
    }

    public function setZone(?string $zone = null): HttpClient
    {
        $this->zone = $zone;
        $this->setApiUrl();

        return $this;
    }

    /**
     * Set debug information as an object
     */
    public function setDebug(
        mixed $lastRequestHeaders,
        mixed $lastRequestBody,
        mixed $lastResponseCode,
        mixed $lastResponseHeaders,
        mixed $lastResponseError
    ): void {
        $this->debug->lastRequestHeaders = $lastRequestHeaders;
        $this->debug->lastRequestBody = $lastRequestBody;
        $this->debug->lastResponseCode = $lastResponseCode;
        $this->debug->lastResponseHeaders = $lastResponseHeaders;
        $this->debug->lastResponseError = $lastResponseError;
    }

    /**
     * Returns debug information in an object
     */
    public function getDebug(): Debug
    {
        return $this->debug;
    }

    /**
     * This is a helper method to do a get request.
     *
     * @throws \KyleWLawrence\MainWP\Http\Exceptions\AuthException
     * @throws \KyleWLawrence\MainWP\Http\Exceptions\ApiResponseException
     */
    public function get(string $endpoint, array $queryParams = []): ?object
    {
        $queryParams = array_merge(
            ['per_page' => 100],
            $queryParams,
        );

        $response = Http::send(
            $this,
            $endpoint,
            ['queryParams' => $queryParams]
        );

        return $response;
    }

    /**
     * This is a helper method to do a post request.
     *
     * @throws \KyleWLawrence\MainWP\Http\Exceptions\AuthException
     * @throws \KyleWLawrence\MainWP\Http\Exceptions\ApiResponseException
     */
    public function post(string $endpoint, array $postData = [], array $options = []): ?object
    {
        $extraOptions = array_merge($options, [
            'postFields' => $postData,
            'method' => 'POST',
        ]);

        $response = Http::send(
            $this,
            $endpoint,
            $extraOptions
        );

        return $response;
    }

    /**
     * This is a helper method to do a patch request.
     *
     * @throws \KyleWLawrence\MainWP\Http\Exceptions\AuthException
     * @throws \KyleWLawrence\MainWP\Http\Exceptions\ApiResponseException
     */
    public function patch(string $endpoint, array $patchData = [], array $options = []): ?object
    {
        $extraOptions = array_merge($options, [
            'postFields' => $patchData,
            'method' => 'PATCH',
        ]);

        $response = Http::send(
            $this,
            $endpoint,
            $extraOptions
        );

        return $response;
    }

    /**
     * This is a helper method to do a patch request.
     *
     * @throws \KyleWLawrence\MainWP\Http\Exceptions\AuthException
     * @throws \KyleWLawrence\MainWP\Http\Exceptions\ApiResponseException
     */
    public function delete(string $endpoint): ?object
    {
        $extraOptions = array_merge($options, [
            'postFields' => $patchData,
            'method' => 'PATCH',
        ]);

        $response = Http::send(
            $this,
            $endpoint,
            $extraOptions
        );

        return $response;
    }

    /**
     * Check that all parameters have been supplied
     */
    public function hasKeys(array $params, array $mandatory): bool
    {
        for ($i = 0; $i < count($mandatory); $i++) {
            if (! array_key_exists($mandatory[$i], $params)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check that any parameter has been supplied
     */
    public function hasAnyKey(array $params, array $mandatory): bool
    {
        for ($i = 0; $i < count($mandatory); $i++) {
            if (array_key_exists($mandatory[$i], $params)) {
                return true;
            }
        }

        return false;
    }
}
