<?php

/**
 * YAWIK Sentry Integration
 *
 * @see       https://github.com/yawik/sentry for the canonical source repository
 * @copyright https://github.com/yawik/sentry/blob/master/COPYRIGHT
 * @license   https://github.com/yawik/sentry/blob/master/LICENSE
 */

declare(strict_types=1);

namespace YkSentry\Listener;

use Core\EventManager\ListenerAggregateTrait;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\Mvc\Application;
use Laminas\Mvc\MvcEvent;
use Sentry\State\HubInterface;
use Sentry\State\Scope;

/**
 * TODO: description
 *
 * @author Mathias Gelhausen
 * TODO: write tests
 */
class SendSentryEvent implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    // phpcs:ignore
    private $events = [
        [MvcEvent::EVENT_DISPATCH_ERROR, 'execute', 10],
        [MvcEvent::EVENT_RENDER_ERROR, 'execute', 10],
    ];

    /** @var HubInterface */
    private $hub;

    public function __construct(HubInterface $hub)
    {
        $this->hub = $hub;
    }

    public function execute(MvcEvent $event)
    {
        $error = $event->getError();

        $this->hub->configureScope(function (Scope $scope) use ($error, $event) {
            $scope->setTag('type', $error);
        });

        if ($error == Application::ERROR_EXCEPTION) {
            $exception = $event->getParam('exception');
            $this->hub->captureException($exception);
            return;
        }

        $this->hub->captureMessage($this->detectErrorMessage($error));
    }

    private function detectErrorMessage($error)
    {
        switch ($error) {
            case Application::ERROR_CONTROLLER_CANNOT_DISPATCH:
                return 'Controller can not be dispatched.';

            case Application::ERROR_CONTROLLER_INVALID:
                return 'Invalid controller invoked.';

            case Application::ERROR_CONTROLLER_NOT_FOUND:
                return 'Controller can not be found.';

            case Application::ERROR_MIDDLEWARE_CANNOT_DISPATCH:
                return 'Middleware can not be dispatched';

            case Application::ERROR_ROUTER_NO_MATCH:
                return 'No route matched.';

            default:
                return 'An unknown error occured.';
        }
    }
}
