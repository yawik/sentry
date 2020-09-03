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
use YkSentry\Options\ModuleOptions;
use YkSentry\Service\SentryClientFactory;

/**
 * Tests for \YkSentry\Service\SentryClientFactory
 *
 * @author Mathias Gelhausen
 * @covers \YkSentry\Service\SentryClientFactory
 */
class SentryClientFactoryTest extends TestCase
{
    use ContainerDoubleTrait;

    public function testCreatesSentryClient()
    {
        $options = new ModuleOptions();

        $container = $this->createContainerDouble([
            ModuleOptions::class => [$options, 1]
        ]);

        $client = (new SentryClientFactory())($container, 'irrelevant');

        static::assertInstanceOf(ClientInterface::class, $client);
    }
}
