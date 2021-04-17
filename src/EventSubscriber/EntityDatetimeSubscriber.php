<?php

namespace App\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class EntityDatetimeSubscriber implements EventSubscriber
{
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entity->setUpdatedAt(new \DateTime());
    }

    public function getSubscribedEvents() : array
    {
        return [
            Events::preUpdate,
        ];
    }
}
