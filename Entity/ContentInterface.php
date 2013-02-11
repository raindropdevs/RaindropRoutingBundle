<?php

namespace Raindrop\RoutingBundle\Entity;

interface ContentInterface {
    /**
     * Returns last update of current document/entity.
     */
    public function getUpdated();
}
