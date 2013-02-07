<?php

namespace Raindrop\RoutingBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Raindrop\RoutingBundle\Entity\Route;

class PostLoadListener {
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();
        
        if ($entity instanceof Route) {
            $entity->init($entityManager);
        }
    }
}