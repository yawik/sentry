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
use Sentry\ClientBuilder;
use Sentry\ClientInterface;
use Sentry\Options;
use YkSentry\Options\ModuleOptions;

/**
 * Factory for creating a Sentry client interface instance.
 *
 * @author Mathias Gelhausen
 */
class SentryClientFactory
{
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        ?array $options = null
    ): ClientInterface {
        /** @var ModuleOptions $options */
        $options = $container->get(ModuleOptions::class);
        $options = new Options($options->getSentryConfig());

        return (new ClientBuilder($options))->getClient();
    }
}
