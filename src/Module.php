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

use Core\ModuleManager\Feature\VersionProviderInterface;
use Core\ModuleManager\Feature\VersionProviderTrait;
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
class Module implements BootstrapListenerInterface, ConfigProviderInterface, VersionProviderInterface
{
    use VersionProviderTrait;

    const VERSION = '0.0.2';
 
    public function getConfig()
    {
        return ModuleConfigLoader::load(__DIR__ . '/../config/');
    }

    public function onBootstrap(EventInterface $e)
    {
        /** @var \Laminas\Mvc\MvcEvent $e */
        /** @var \YkSentry\Options\ModuleOptions $options */
        $application = $e->getApplication();
        $services    = $application->getServiceManager();
        $options     = $services->get(ModuleOptions::class);
        $config      = $options->getSentryConfig();

        if (!isset($config['dsn']) || !$config['dsn']) {
            return;
        }

        /** @var \YkSentry\Listener\SendSentryEvent $listener */
        $events      = $application->getEventManager();
        $listener    = $services->get(SendSentryEvent::class);

        \Sentry\init($config);
        $listener->attach($events);
    }
}
