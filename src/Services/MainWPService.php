<?php

namespace KyleWLawrence\MainWP\Services;

use BadMethodCallException;
use Config;
use InvalidArgumentException;
use KyleWLawrence\MainWP\Http\HttpClient;

class MainWPService
{
    /**
     * Get auth parameters from config, fail if any are missing.
     * Instantiate API client and set auth consumer_key and consumer_secret
     *
     * @throws Exception
     */
    public HttpClient $client;

    public function __construct(
        private string $domain = '',
        private string $consumer_key = '',
        private string $consumer_secret = '',
    ) {
        $this->domain = ($this->domain) ? $this->domain : config('mainwp-laravel.domain');
        $this->consumer_key = ($this->consumer_key) ? $this->consumer_key : config('mainwp-laravel.consumer_key');
        $this->consumer_secret = ($this->consumer_secret) ? $this->consumer_secret : config('mainwp-laravel.consumer_secret');

        if (! $this->domain || ! $this->consumer_key || ! $this->consumer_secret) {
            throw new InvalidArgumentException('Please set MAINWP_DOMAIN & MAINWP_CONSUMER_KEY & MAINWP_CONSUMER_SECRET environment variables.');
        }

        $this->client = new HttpClient($this->domain);
        $this->client->setAuth('consumer_auth', ['consumer_key' => $this->consumer_key, 'consumer_secret' => $this->consumer_secret]);
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
