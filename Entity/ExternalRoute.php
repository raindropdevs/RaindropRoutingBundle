<?php

namespace Raindrop\RoutingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Raindrop\RoutingBundle\Routing\Base\ExternalRouteInterface;

/**
 * ExternalRoute
 * @ORM\Entity(repositoryClass="Raindrop\RoutingBundle\Entity\ExternalRouteRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="external_routes")
 */
class ExternalRoute implements ExternalRouteInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $uri;
    
    /**
     * @ORM\Column(type="boolean")
     */
    protected $permanent;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set uri
     *
     * @param string $uri
     * @return ExternalRoute
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Set permanent
     *
     * @param boolean $permanent
     * @return ExternalRoute
     */
    public function setPermanent($permanent = true)
    {
        $this->permanent = $permanent;
    
        return $this;
    }

    /**
     * Get permanent
     *
     * @return boolean 
     */
    public function getPermanent()
    {
        return $this->permanent;
    }
}