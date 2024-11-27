<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

final class RegistrationConfirmedListener
{
    public function __construct(private readonly RouterInterface $router)
    {
    }

    #[AsEventListener(event: 'fos_user.registration.confirmed')]
    public function __invoke(FilterUserResponseEvent $event): void
    {

        dd($event);

    }
}
