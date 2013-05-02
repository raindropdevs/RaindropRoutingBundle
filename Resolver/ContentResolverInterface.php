<?php

namespace Raindrop\RoutingBundle\Resolver;

interface ContentResolverInterface
{
    /**
     * Returns target object according to resolving strategy.
     */
    public function getContent($object);

    /**
     * Returns 'routeObject' settings according to resolve strategy.
     */
    public function getRouteContent($object);
}
