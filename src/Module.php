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

use Core\ModuleManager\ModuleConfigLoader;
use Laminas\EventManager\EventInterface;
use Laminas\ModuleManager\Feature\BootstrapListenerInterface;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use YkSentry\Listener\SendSentryEvent;
use YkSentry\Options\ModuleOptions;

/**
 * TODO: description
 *
 * @author Mathias Gelhausen
 * TODO: write tests
 */
class Module implements BootstrapListenerInterface, ConfigProviderInterface
{
    public function getConfig()
    {
        return ModuleConfigLoader::load(__DIR__ . '/../config/');
    }

    public function onBootstrap(EventInterface $e)
    {
        /** @var \Laminas\Mvc\MvcEvent $e */
        /** @var \YkSentry\Options\ModuleOptions $options */
        /** @var \YkSentry\Listener\SendSentryEvent $listener */
        $application = $e->getApplication();
        $services    = $application->getServiceManager();
        $options     = $services->get(ModuleOptions::class);
        $events      = $application->getEventManager();
        $listener    = $services->get(SendSentryEvent::class);

        \Sentry\init($options->getSentryConfig());
        $listener->attach($events);
    }
}
