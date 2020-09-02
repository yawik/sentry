<?php

/**
 * YAWIK Sentry Integration
 *
 * @see       https://github.com/yawik/sentry for the canonical source repository
 * @copyright https://github.com/yawik/sentry/blob/master/COPYRIGHT
 * @license   https://github.com/yawik/sentry/blob/master/LICENSE
 */

declare(strict_types=1);

namespace YkSentry\Service;

use Psr\Container\ContainerInterface;
use Sentry\ClientInterface;
use Sentry\SentrySdk;
use Sentry\State\Hub;
use Sentry\State\HubInterface;

/**
 * Factory for creating a sentry Hub instance
 *
 * @author Mathias Gelhausen
 * TODO: write tests
 */
class SentryHubFactory
{
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        ?array $options = null
    ): HubInterface {
        $hub = new Hub(
            $container->get(ClientInterface::class)
        );

        /*
         * This is needed to register the error handlers
         * to capture warnings and fatal errrors afterwards.
         */
        SentrySdk::setCurrentHub($hub);

        return $hub;
    }
}
