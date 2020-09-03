<?php

/**
 * YAWIK Sentry Integration
 *
 * @see       https://github.com/yawik/sentry for the canonical source repository
 * @copyright https://github.com/yawik/sentry/blob/master/COPYRIGHT
 * @license   https://github.com/yawik/sentry/blob/master/LICENSE
 */

declare(strict_types=1);

namespace YkSentryTest\Listener;

use Core\EventManager\ListenerAggregateTrait;
use Cross\TestUtils\TestCase\SetupTargetTrait;
use Cross\TestUtils\TestCase\TestInheritanceTrait;
use Cross\TestUtils\TestCase\TestUsesTraitsTrait;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\Mvc\Application;
use Laminas\Mvc\MvcEvent;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Sentry\Event;
use Sentry\Severity;
use Sentry\State\HubInterface;
use Sentry\State\Scope;
use YkSentry\Listener\SendSentryEvent;

/**
 * Tests for \YkSentry\Listener\SendSentryEvent
 *
 * @author Mathias Gelhausen
 * @covers \YkSentry\Listener\SendSentryEvent
 */
class SendSentryEventTest extends TestCase
{
    use SetupTargetTrait, TestInheritanceTrait, TestUsesTraitsTrait;

    /**
     * @var array|SendSentryEvent
     */
    private $target = [
        'create' => [
            [
                'for' => ['testInheritance', 'testUsesTraits'],
                'reflection' => SendSentryEvent::class,
            ],
            [
                'callback' => 'createTarget',
            ]
        ]
    ];

    private $inheritance = [ ListenerAggregateInterface::class ];

    private $usesTraits = [ ListenerAggregateTrait::class ];

    /**
     * Test double for the sentry hub
     * @var \Prophecy\Prophecy\ObjectProphecy
     */
    private $hub;

    private function createTarget()
    {
        $this->hub = $this->prophesize(HubInterface::class);

        return [SendSentryEvent::class, $this->hub->reveal()];
    }

    public function testRegistersToTheCorrectEventsWithCorrectPriority()
    {
        $events = $this->prophesize(EventManagerInterface::class);
        $events
            ->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$this->target, 'execute'], -100)
            ->willReturn('handle1')
            ->shouldBeCalled()
        ;
        $events
            ->attach(MvcEvent::EVENT_RENDER_ERROR, [$this->target, 'execute'], -100)
            ->willReturn('handle2')
            ->shouldBeCalled()
        ;

        $this->target->attach($events->reveal());
    }

    public function testScopeIsConfiguredCorrectlyForExceptionErrors()
    {
        $exception = new \Exception('dummyexception');

        $event = new MvcEvent();
        $event->setError(Application::ERROR_EXCEPTION);
        $event->setParam('exception', $exception);

        $this->hub->configureScope(Argument::that(function ($cb) {
            $scope = new Scope();
            $event = new Event();
            $cb($scope);
            $scope->applyToEvent($event, []);
            $tags = $event->getTagsContext();
            $level = $event->getLevel()->__toString();

            return
                isset($tags['type'])
                && $tags['type'] === Application::ERROR_EXCEPTION
                && $level === Severity::ERROR
            ;
        }))->shouldBeCalled();

        $this->hub->captureException(Argument::any());

        $this->target->execute($event);
    }

    public function testScopeIsConfiguredCorrectlyForRouteNotMatchErrors()
    {
        $event = new MvcEvent();
        $event->setError(Application::ERROR_ROUTER_NO_MATCH);

        $this->hub->configureScope(Argument::that(function ($cb) {
            $scope = new Scope();
            $event = new Event();
            $cb($scope);
            $scope->applyToEvent($event, []);
            $tags = $event->getTagsContext();
            $level = $event->getLevel()->__toString();

            return
                isset($tags['type'])
                && $tags['type'] === Application::ERROR_ROUTER_NO_MATCH
                && $level === Severity::WARNING
            ;
        }))->shouldBeCalled();

        $this->hub->captureMessage(Argument::any());

        $this->target->execute($event);
    }

    public function testSendExceptionEvent()
    {
        $exception = new \Exception('Test Exception');

        $event = new MvcEvent();
        $event->setError(Application::ERROR_EXCEPTION);
        $event->setParam('exception', $exception);

        $this->hub->configureScope(Argument::any());
        $this->hub->captureException(Argument::is($exception))->shouldBeCalled();

        $this->target->execute($event);
    }

    public function provideSendMessageEventTestData()
    {
        return [
            [Application::ERROR_CONTROLLER_CANNOT_DISPATCH, 'Controller can not be dispatched.'],
            [Application::ERROR_CONTROLLER_INVALID, 'Invalid controller invoked.'],
            [Application::ERROR_CONTROLLER_NOT_FOUND, 'Controller can not be found.'],
            [Application::ERROR_MIDDLEWARE_CANNOT_DISPATCH, 'Middleware can not be dispatched'],
            [Application::ERROR_ROUTER_NO_MATCH, 'No route matched.'],
            ['unknown', 'An unknown error occured.'],
        ];
    }

    /**
     * @dataProvider provideSendMessageEventTestData()
     */
    public function testSendMessageEvent($error, $expectedMessage)
    {
        $event = new MvcEvent();
        $event->setError($error);

        $this->hub->configureScope(Argument::any());
        $this->hub->captureException(Argument::any())->shouldNotBeCalled();
        $this->hub->captureMessage($expectedMessage)->shouldBeCalled();

        $this->target->execute($event);
    }
}
