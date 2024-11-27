<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

final class RegistrationSuccessListener
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RouterInterface $router
    )
    {}

    #[AsEventListener(event: 'fos_user.registration.success')]
    public function __invoke(FormEvent $event): void
    {
        $user = $event->getForm()->getData();
        $user->addRole('ROLE_ADMIN');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $url = $this->router->generate('admin');
        $event->setResponse(new RedirectResponse($url));
    }

}
