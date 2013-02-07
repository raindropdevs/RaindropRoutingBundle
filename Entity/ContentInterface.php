<?php

namespace Raindrop\RoutingBundle\Entity;

interface ContentInterface {
    
    /**
     * Returns the template definition for current action.
     */
    public function getTemplate();
    
    /**
     * Returns the placeholder/value map for this template.
     */
    public function getArray();
    
    /**
     * Returns last update of current document/entity.
     */
    public function getUpdated();
}
