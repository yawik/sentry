<?php

/**
 * YAWIK Sentry Integration
 *
 * @see       https://github.com/yawik/sentry for the canonical source repository
 * @copyright https://github.com/yawik/sentry/blob/master/COPYRIGHT
 * @license   https://github.com/yawik/sentry/blob/master/LICENSE
 */

declare(strict_types=1);

namespace YkSentry\Listener;

use Psr\Container\ContainerInterface;
use Sentry\State\HubInterface;

/**
 * Factory for \YkSentry\Listener\SendSentryEvent
 *
 * @author Mathias Gelhausen
 */
class SendSentryEventFactory
{
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        ?array $options = null
    ): SendSentryEvent {
        return new SendSentryEvent(
            $container->get(HubInterface::class)
        );
    }
}
