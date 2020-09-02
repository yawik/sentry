<?php

/**
 * YAWIK Sentry Integration
 *
 * @see       https://github.com/yawik/sentry for the canonical source repository
 * @copyright https://github.com/yawik/sentry/blob/master/COPYRIGHT
 * @license   https://github.com/yawik/sentry/blob/master/LICENSE
 */

declare(strict_types=1);

namespace YkSentry\Options;

use Laminas\Stdlib\AbstractOptions;

/**
 * TODO: description
 *
 * @author Mathias Gelhausen
 * TODO: write tests
 */
class ModuleOptions extends AbstractOptions
{

    /** @var bool */
    private $isEnabled = true;

    /**
     * @var array
     */
    private $sentryConfig = [];

    /**
     * Get sentry configuration array
     *
     * @return array
     */
    public function getSentryConfig()
    {
        return $this->sentryConfig;
    }

    /**
     * Set sentry configuration
     *
     * @param array $sentryConfig
     */
    public function setSentryConfig($sentryConfig): void
    {
        $this->sentryConfig = $sentryConfig;
    }

    /**
     * Returns true, if Sentry should be enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return
            $this->isEnabled
            && isset($this->sentryConfig['dsn'])
            && !empty($this->sentryConfig['dsn'])
        ;
    }

    /**
     * Set wether Sentry should be enabled.
     *
     * @param bool $isEnabled
     */
    public function setIsEnabled($isEnabled): void
    {
        $this->isEnabled = $isEnabled;
    }
}
