<?php

namespace Raindrop\RoutingBundle\Routing\Base;

interface ExternalRouteInterface {
    public function getUri();
    public function getPermanent();
}
