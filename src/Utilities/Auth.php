<?php

namespace KyleWLawrence\MainWP\Utilities;

use KyleWLawrence\MainWP\Api\Exceptions\AuthException;
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
        return [self::CONSUMER_AUTH];
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
            if (! array_key_exists('consumer_auth', $options)) {
                throw new AuthException('Please supply `consumer_key` and `consumer_secret` for consumer_auth auth.');
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
            $consumer_auth = $this->authOptions['consumer_auth'];

            $uri = $request->getUri();
            $uri = $uri->withQueryValue($uri, 'consumer_key', $consumer_auth['consumer_key']);
            $uri = $uri->withQueryValue($uri, 'consumer_secret', $consumer_auth['consumer_secret']);
            $request = $request->withUri($uri, true);
        } else {
            throw new AuthException('Please set authentication to send requests.');
        }

        return [$request, $requestOptions];
    }
}
