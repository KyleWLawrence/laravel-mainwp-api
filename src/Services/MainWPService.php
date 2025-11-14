<?php

namespace KyleWLawrence\MainWP\Services;

use BadMethodCallException;
use Config;
use InvalidArgumentException;
use KyleWLawrence\MainWP\Http\HttpClient;
use KyleWLawrence\MainWP\Utilities\Auth;

class MainWPService
{
    /**
     * Get auth parameters from config, fail if any are missing.
     * Instantiate API client and set auth consumer_key and consumer_secret or bearer_token
     *
     * @throws Exception
     */
    public HttpClient $client;

    public function __construct(
        private string $domain = '',
        private string $bearer_token = '',
        private string $version = HttpClient::DEFAULT_VERSION,
        private string $consumer_key = '',
        private string $consumer_secret = '',

    ) {
        $this->domain = ($this->domain) ? $this->domain : config('mainwp-laravel.domain');
        $this->bearer_token = ($this->bearer_token) ? $this->bearer_token : config('mainwp-laravel.bearer_token');
        $this->version = ($this->version) ? $this->version : config('mainwp-laravel.version');
        $this->consumer_key = ($this->consumer_key) ? $this->consumer_key : config('mainwp-laravel.consumer_key');
        $this->consumer_secret = ($this->consumer_secret) ? $this->consumer_secret : config('mainwp-laravel.consumer_secret');

        if (! $this->domain) {
            throw new InvalidArgumentException('Please set MAINWP_DOMAIN environment variable.');
        }

        // Enforce correct auth scheme per API version
        if ($this->version === HttpClient::API_VERSION_2) {
            if (!$this->bearer_token) {
                throw new InvalidArgumentException('V2 API requires MAINWP_BEARER_TOKEN.');
            }
            if ($this->consumer_key || $this->consumer_secret) {
                throw new InvalidArgumentException('V2 API does not support consumer_key/consumer_secret authentication.');
            }
            $this->client = new HttpClient($this->domain, $this->version);
            $this->client->setAuth(AUTH::BEARER_TOKEN, [AUTH::BEARER_TOKEN => $this->bearer_token]);
        } elseif ($this->version === HttpClient::API_VERSION_1) {
            if (!$this->consumer_key || !$this->consumer_secret) {
                throw new InvalidArgumentException('V1 API requires MAINWP_CONSUMER_KEY and MAINWP_CONSUMER_SECRET.');
            }
            if ($this->bearer_token) {
                throw new InvalidArgumentException('V1 API does not support bearer token authentication.');
            }
            $this->client = new HttpClient($this->domain, $this->version);
            $this->client->setAuth(AUTH::CONSUMER_AUTH, ['consumer_key' => $this->consumer_key, 'consumer_secret' => $this->consumer_secret]);
        } else {
            throw new InvalidArgumentException('Unsupported API version: ' . $this->version);
        }
    }

    /**
     * Pass any method calls onto $this->client
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (is_callable([$this->client, $method])) {
            return call_user_func_array([$this->client, $method], $args);
        } else {
            throw new BadMethodCallException("Method $method does not exist");
        }
    }

    /**
     * Pass any property calls onto $this->client
     *
     * @return mixed
     */
    public function __get($property)
    {
        if (property_exists($this->client, $property)) {
            return $this->client->{$property};
        } else {
            throw new BadMethodCallException("Property $property does not exist");
        }
    }
}
