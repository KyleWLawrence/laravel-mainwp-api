<?php

namespace KyleWLawrence\MainWP\Utilities;

use KyleWLawrence\MainWP\Http\Exceptions\AuthException;
use Psr\Http\Message\RequestInterface;

/**
 * Class Auth
 * This helper would manage all Authentication related operations.
 */
class Auth
{
    /**
     * The authentication setting to use a Consumer Auth API.
     */
    const CONSUMER_AUTH = 'consumer_auth';

    /**
     * The authentication setting to use Bearer Token Auth API.
     */
    const BEARER_TOKEN = 'bearer_token';

    /**
     * @var string
     */
    protected $authStrategy;

    /**
     * @var array
     */
    protected $authOptions;

    /**
     * Returns an array containing the valid auth strategies
     *
     * @return array
     */
    protected static function getValidAuthStrategies()
    {
        return [self::CONSUMER_AUTH, self::BEARER_TOKEN];
    }

    /**
     * Auth constructor.
     *
     * @param    $strategy
     * @param  array  $options
     *
     * @throws AuthException
     */
    public function __construct(string $strategy, array $options)
    {
        if (! in_array($strategy, self::getValidAuthStrategies())) {
            throw new AuthException('Invalid auth strategy set, please use `'
                                    .implode('` or `', self::getValidAuthStrategies())
                                    .'`');
        }

        $this->authStrategy = $strategy;

        if ($strategy == self::CONSUMER_AUTH) {
            if (! array_key_exists('consumer_key', $options) || ! array_key_exists('consumer_secret', $options)) {
                throw new AuthException('Please supply `consumer_key` and `consumer_secret` for consumer_auth auth.');
            }
        } elseif ($strategy == self::BEARER_TOKEN) {
            if (! array_key_exists('bearer_token', $options)) {
                throw new AuthException('Please supply `bearer_token` for bearer_token auth.');
            }
        }

        $this->authOptions = $options;
    }

    /**
     * @param  RequestInterface  $request
     * @param  array  $requestOptions
     * @return array
     *
     * @throws AuthException
     */
    public function prepareRequest(RequestInterface $request, array $requestOptions = []): array
    {
        if ($this->authStrategy === self::CONSUMER_AUTH) {
            $consumer_auth = $this->authOptions;
            $uri = $request->getUri();
            $uri = $uri->withQueryValue($uri, 'consumer_key', $consumer_auth['consumer_key']);
            $uri = $uri->withQueryValue($uri, 'consumer_secret', $consumer_auth['consumer_secret']);
            $request = $request->withUri($uri, true);
        } elseif ($this->authStrategy === self::BEARER_TOKEN) {
            $bearer_token = $this->authOptions['bearer_token'];
            $request = $request->withHeader('Authorization', 'Bearer ' . $bearer_token);
        } else {
            throw new AuthException('Please set authentication to send requests.');
        }

        return [$request, $requestOptions];
    }
}
