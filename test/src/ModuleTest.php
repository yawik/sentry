<?php

/**
 * YAWIK Sentry Integration
 *
 * @see       https://github.com/yawik/sentry for the canonical source repository
 * @copyright https://github.com/yawik/sentry/blob/master/COPYRIGHT
 * @license   https://github.com/yawik/sentry/blob/master/LICENSE
 */

declare(strict_types=1);

namespace YkSentryTest;

use Core\ModuleManager\Feature\VersionProviderInterface;
use Core\ModuleManager\Feature\VersionProviderTrait;
use Cross\TestUtils\TestCase\ContainerDoubleTrait;
use Cross\TestUtils\TestCase\TestInheritanceTrait;
use Cross\TestUtils\TestCase\TestUsesTraitsTrait;
use Laminas\EventManager\EventManager;
use Laminas\ModuleManager\Feature\BootstrapListenerInterface;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\Mvc\Application;
use Laminas\Mvc\MvcEvent;
use PHPUnit\Framework\TestCase;
use YkSentry\Listener\SendSentryEvent;
use YkSentry\Module;
use YkSentry\Options\ModuleOptions;

/**
 * Tests for \YkSentry\Module
 *
 * @author Mathias Gelhausen
 * @covers \YkSentry\Module
 */
class ModuleTest extends TestCase
{
    use TestInheritanceTrait, TestUsesTraitsTrait, ContainerDoubleTrait;

    private $target = Module::class;

    private $inheritance = [
        BootstrapListenerInterface::class,
        ConfigProviderInterface::class,
        VersionProviderInterface::class,
    ];

    private $usesTraits = [ VersionProviderTrait::class ];

    public function testGetConfigLoadsModuleConfig()
    {
        $config = (new Module())->getConfig();

        static::assertIsArray($config);
        static::assertArrayHasKey('service_manager', $config);
        static::assertTrue(isset($config['service_manager']['factories'][SendSentryEvent::class]), 'Listener is not in the config');
        static::assertArrayHasKey('options', $config);
        static::assertTrue(isset($config['options'][ModuleOptions::class]), 'ModuleOptions are not in the config');
    }

    public function testCorrectVersionIsset()
    {
        $version = (new Module())->getVersion();

        static::assertStringStartsWith('0.1.0', $version);
    }

    public function testDoesNotRegisterListenerIfNotEnabled()
    {
        $options = $this->prophesize(ModuleOptions::class);
        $options->isEnabled()->willReturn(false)->shouldBeCalled();

        $services = $this->createContainerDouble([
            ModuleOptions::class => [$options->reveal(), 1]
        ]);

        $application = $this->prophesize(Application::class);
        $application->getServiceManager()->willReturn($services)->shouldBeCalled();
        $application->getEventManager()->shouldNotBeCalled();

        $event = $this->prophesize(MvcEvent::class);
        $event->getApplication()->willReturn($application->reveal())->shouldBeCalled();

        (new Module())->onBootstrap($event->reveal());
    }

    public function testDoesRegisterListenerIfEnabled()
    {
        $options = $this->prophesize(ModuleOptions::class);
        $options->isEnabled()->willReturn(true)->shouldBeCalled();

        $events = $this->prophesize(EventManager::class);
        $eventsDouble = $events->reveal();

        $listener = $this->prophesize(SendSentryEvent::class);
        $listener->attach($eventsDouble)->shouldBeCalled();

        $services = $this->createContainerDouble([
            ModuleOptions::class => [$options->reveal(), 1],
            SendSentryEvent::class => [$listener->reveal(), 1]
        ]);

        $application = $this->prophesize(Application::class);
        $application->getServiceManager()->willReturn($services)->shouldBeCalled();
        $application->getEventManager()->willReturn($eventsDouble)->shouldBeCalled();

        $event = $this->prophesize(MvcEvent::class);
        $event->getApplication()->willReturn($application->reveal())->shouldBeCalled();

        (new Module())->onBootstrap($event->reveal());
    }
}
