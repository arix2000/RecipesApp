<?php

namespace App\EventSubscriber;

use App\Constants\SessionConst;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutSubscriber implements EventSubscriberInterface
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function onLogoutEvent(LogoutEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $locale = $request->getSession()->get(SessionConst::LOCALE);
        $request->getSession()->invalidate();
        $request->getSession()->set(SessionConst::LOCALE, $locale);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'onLogoutEvent',
        ];
    }
}
