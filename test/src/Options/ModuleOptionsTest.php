<?php

/**
 * YAWIK Sentry Integration
 *
 * @see       https://github.com/yawik/sentry for the canonical source repository
 * @copyright https://github.com/yawik/sentry/blob/master/COPYRIGHT
 * @license   https://github.com/yawik/sentry/blob/master/LICENSE
 */

declare(strict_types=1);

namespace YkSentryTest\Options;

use Cross\TestUtils\TestCase\TestInheritanceTrait;
use Cross\TestUtils\TestCase\TestSetterAndGetterTrait;
use Laminas\Stdlib\AbstractOptions;
use PHPUnit\Framework\TestCase;
use YkSentry\Options\ModuleOptions;

/**
 * Tests for \YkSentry\Options\ModuleOptions
 *
 * @author Mathias Gelhausen
 * @covers \YkSentry\Options\ModuleOptions
 */
class ModuleOptionsTest extends TestCase
{
    use TestInheritanceTrait, TestSetterAndGetterTrait;

    /**
     * @var string|ModuleOptions
     */
    private $target = ModuleOptions::class;

    private $inheritance = [ AbstractOptions::class ];

    public function setterAndGetterData()
    {
        return [
            ['sentryConfig', ['dsn' => 'testdsn']],
            ['Enabled', ['getter' => 'is*', 'setter' => 'setIs*', 'value' => true, 'expect' => false]],
            ['Enabled', ['getter' => 'is*', 'setter' => 'setIs*', 'value' => false]],
            ['Enabled', ['setter' => 'setSentryConfig', 'value' => ['dsn' => 'notEmpty'], 'getter' => 'is*', 'expect' => true]],
            ['Enabled', ['setter' => 'setSentryConfig', 'value' => ['dsn' => ''], 'getter' => 'is*', 'expect' => false]],
            [
                'Enabled',
                [
                    'setter_callback' => function() {
                        $this->target->setSentryConfig(['dsn' => 'notEmpty']);
                        return false;
                    },
                    'setter' => 'setIs*',
                    'getter' => 'is*',
                    'expect' => false,
                ],
            ],
        ];
    }

}
