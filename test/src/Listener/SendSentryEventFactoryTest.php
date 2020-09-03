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

use Cross\TestUtils\TestCase\ContainerDoubleTrait;
use PHPUnit\Framework\TestCase;
use Sentry\State\HubInterface;
use YkSentry\Listener\SendSentryEvent;
use YkSentry\Listener\SendSentryEventFactory;

/**
 * Tests for \YkSentry\Listener\SendSentryEventFactory
 *
 * @author Mathias Gelhausen
 * @covers \YkSentry\Listener\SendSentryEventFactory
 */
class SendSentryEventFactoryTest extends TestCase
{

    use ContainerDoubleTrait;

    public function testCreatesListener()
    {
        $hub = $this->prophesize(HubInterface::class);
        $container = $this->createContainerDouble([
            HubInterface::class => [$hub->reveal(), 1]
        ]);

        $listener = (new SendSentryEventFactory())($container, 'irrelevant');

        static::assertInstanceOf(SendSentryEvent::class, $listener);
    }
}
