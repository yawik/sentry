<?php

/**
 * YAWIK Sentry Integration
 *
 * @see       https://github.com/yawik/sentry for the canonical source repository
 * @copyright https://github.com/yawik/sentry/blob/master/COPYRIGHT
 * @license   https://github.com/yawik/sentry/blob/master/LICENSE
 */

declare(strict_types=1);

namespace YkSentry;

return [

    'service_manager' => [
        'factories' => [
            \Sentry\State\HubInterface::class => Service\SentryHubFactory::class,
            \Sentry\ClientInterface::class => Service\SentryClientFactory::class,
            Listener\SendSentryEvent::class => Listener\SendSentryEventFactory::class,
        ],
    ],

    'options' => [
        Options\ModuleOptions::class => []
    ],
];
