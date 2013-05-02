<?php

namespace Raindrop\RoutingBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Raindrop\RoutingBundle\Entity\Route;

class PostLoadListener
{
    protected $emBound = false;
    protected $resolver;

    public function __construct($resolver)
    {
        $this->resolver = $resolver;
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        if ($entity instanceof Route) {

            /**
             * This binding is here to avoid circular dependency.
             * Check if resolver is already bound to an entityManager
             * and, in case, assign it.
             */
            if (!$this->emBound) {
                $this->resolver->setEntityManager($entityManager);
                $this->emBound = true;
            }

            $entity->init($this->resolver);
        }
    }
}
