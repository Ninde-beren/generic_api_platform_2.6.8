<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Person;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Type;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class PostInjectedPersonSubscriber implements EventSubscriberInterface
{
    private User|null $user;

    public function __construct(private Security $security)
    {
        $this->user = $this->security->getUser();
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['PreDoIt', EventPriorities::PRE_WRITE]
        ];
    }

    public function PreDoIt(ViewEvent $event)
    {
        $value = $event->getControllerResult();
        if (gettype($value) === 'object') {
            $method = $event->getRequest()->getMethod();
            $classMethods = get_class_methods($value);

            if (!$value instanceof User && $method === 'POST' && in_array('setPerson', $classMethods)) {
                $value->setPerson($this->user->getPerson());
            }

        }
    }
}
