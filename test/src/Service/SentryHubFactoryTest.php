<?php

/**
 * YAWIK Sentry Integration
 *
 * @see       https://github.com/yawik/sentry for the canonical source repository
 * @copyright https://github.com/yawik/sentry/blob/master/COPYRIGHT
 * @license   https://github.com/yawik/sentry/blob/master/LICENSE
 */

declare(strict_types=1);

namespace YkSentryTest\Service;

use Cross\TestUtils\TestCase\ContainerDoubleTrait;
use PHPUnit\Framework\TestCase;
use Sentry\ClientInterface;
use Sentry\SentrySdk;
use Sentry\State\HubInterface;
use YkSentry\Service\SentryHubFactory;

/**
 * Tests for \YkSentry\Service\SentryHubFactory
 *
 * @author Mathias Gelhausen
 * @covers \YkSentry\Service\SentryHubFactory
 */
class SentryHubFactoryTest extends TestCase
{
    use ContainerDoubleTrait;

    /**
     * @runInSeparateProcess
     */
    public function testCreatesSentryHub()
    {
        $client = $this->prophesize(ClientInterface::class);

        $container = $this->createContainerDouble([
            ClientInterface::class => [$client->reveal(), 1]
        ]);

        $hub = (new SentryHubFactory())($container, 'irrelevant');

        static::assertInstanceOf(HubInterface::class, $hub);
        static::assertSame($hub, SentrySdk::getCurrentHub());
    }
}
