<?php

namespace wiosloCMS\HomepageBundle\Listener;

use Symfony\Component\Debug\Exception\ContextErrorException;
use Symfony\Component\Debug\Exception\DummyException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Router;

class LogoutExceptionListener extends ExceptionListener
{
    /** @var Router */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if ($exception instanceof DummyException) {
            $url = $this->router->generate('homepage');
            $response = new RedirectResponse($url);
            $event->setResponse($response);
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::EXCEPTION => array('onKernelException', 5),
        );
    }
}
