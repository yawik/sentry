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

use Laminas\ServiceManager\Factory\InvokableFactory;

return [

    'service_manager' => [
        'factories' => [
            Listener\SendSentryEvent::class => InvokableFactory::class,
        ],
    ],

    'options' => [
        Options\ModuleOptions::class => []
    ],
];
