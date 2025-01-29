<?php

namespace App\EventSubscriber;

use App\Entity\User;
use KevinPapst\AdminLTEBundle\Event\NavbarUserEvent;
use KevinPapst\AdminLTEBundle\Event\ShowUserEvent;
use KevinPapst\AdminLTEBundle\Event\SidebarUserEvent;
use KevinPapst\AdminLTEBundle\Model\UserModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;

class NavbarUserSubscriber implements EventSubscriberInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SidebarUserEvent::class => ['onShowUser', 100],
        ];
    }

    public function onShowUser(ShowUserEvent $event)
    {
        if (null === $this->security->getUser()) {
            return;
        }

        /* @var $user User */
        $user = $this->security->getUser();

        $userModel = new UserModel();
        $userModel
            ->setId($user->getIdUser())
            ->setName(sprintf('%s %s', $user->getName(), $user->getSurname()))
            ->setUsername($user->getEmail())
            ->setIsOnline(true)
            ->setTitle('4465')
            ->setAvatar('')
            ->setMemberSince($user->getCreatedAt());

        $event
            ->setShowProfileLink(false)
            ->setUser($userModel);
    }
}