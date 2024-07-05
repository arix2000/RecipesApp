<?php

namespace App\EventListener;

use App\Constants\SessionConst;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Translation\LocaleSwitcher;

final class LocaleListener
{
    private LocaleSwitcher $localeSwitcher;

    public function __construct(LocaleSwitcher $localeSwitcher)
    {
        $this->localeSwitcher = $localeSwitcher;
    }

    #[AsEventListener(event: KernelEvents::REQUEST)]
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $request->getBasePath();

        if (!$request->hasPreviousSession()) {
            return;
        }

        if ($locale = $request->getSession()->get(SessionConst::LOCALE)) {
            $this->localeSwitcher->setLocale($locale);
        } else {
            $this->localeSwitcher->setLocale('en');
        }
    }
}
