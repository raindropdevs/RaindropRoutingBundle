<?php

namespace Raindrop\RoutingBundle\Entity;

use Symfony\Component\Routing\Route as SymfonyRoute;
use Raindrop\RoutingBundle\Routing\Base\RouteObjectInterface;
use Symfony\Component\Routing\Exception\InvalidParameterException;


use Doctrine\ORM\Mapping as ORM;


/**
 * This class is based on default document for routing table entries from
 * symfony cmf.
 * original from david.buchmann@liip.ch can be found at https://github.com/symfony-cmf/RoutingExtraBundle
 * ORM version mcaber@gmail.com
 *
 * @ORM\Entity(repositoryClass="Raindrop\RoutingBundle\Entity\RouteRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="routes")
 */
class Route extends SymfonyRoute implements RouteObjectInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    protected $path;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $locale;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $controller;

    /**
     * @ORM\Column(nullable=true)
     */
    protected $routeContent;

    /**
     * @ORM\Column(nullable=true)
     */
    protected $format;

    /**
     * @ORM\Column(nullable=true)
     */
    protected $method;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $permanent;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated;


    /**
     * TODO: This is a wish-to-have feature :D
     * parent document
     */
    protected $parentId;


    /**
     * This property is populated at runtime.
     */
    protected $content;


    /**
     * Variable pattern part. The static part of the pattern is the id without the prefix.
     */
    protected $variablePattern;

    /**
     *
     */
    protected $defaults = array();



    protected $needRecompile = false;


    /**
     * Content resolver
     * @var type
     */
    protected $resolver;

    /**
     * Overwrite to be able to create route without pattern
     */
    public function __construct()
    {
        $this->init();
    }

    public function setResolver($resolver) {
        $this->resolver = $resolver;
    }

    /**
     * Set locale
     *
     * @param string $locale
     * @return Route
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    public function getLocale() {
        return $this->locale;
    }

    /**
     * Set controller
     *
     * @param string $controller
     * @return Route
     */
    public function setController($controller)
    {
        $this->controller = $controller;

        return $this;
    }

    public function getController() {
        return $this->controller;
    }


    /**
     * Rename a route by setting its new name.
     *
     * Note that this will change the URL this route matches.
     *
     * @param string $name the new name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setNameFromPath() {
        $name = preg_replace("/[\/\-]+/", "_", trim($this->path, '/'));
        $this->setName($name);
    }

    /**
     * Convenience method to set parent and name at the same time.
     *
     * The url will be the url of the parent plus the supplied name.
     */
    public function setPosition($parent, $name)
    {
        $this->parent = $parent;
        $this->name = $name;
    }

    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get the repository path of this url entry
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the document this url points to
     */
    public function setRouteContent($document)
    {
        $this->routeContent = $document;
    }

    /**
     * {@inheritDoc}
     */
    public function getRouteContent()
    {
        return $this->routeContent;
    }

    /**
     * {@inheritDoc}
     */
    public function getPattern()
    {
        return $this->path;
    }

    /**
     *
     * prepare hashmaps into mapped properties to store them
     */
    public function init($resolver = null)
    {
        if ($resolver) {
            $this->setResolver($resolver);
        }

        $this->setOptions(array());
    }

    public function getRequirements() {
        return array(
            'path' => $this->path,
            '_format' => $this->format,
            '_method' => $this->getMethod()
        );
    }

    /**
     *
     * @param array $array
     * @return \Raindrop\RoutingBundle\Entity\Route
     */
    public function setRequirements(array $array) {
        $reqs = array(
            'path' => 'setPath',
            '_format' => 'setFomat',
            '_method' => 'setMethod'
        );

        foreach ($reqs as $key => $method) {
            if (isset($array[$key])) {
                $this->$method($array[$key]);
            }
        }

        return $this;
    }

    public function getOptions() {
        return array();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getDefaults() {
        return array(
            '_locale' => $this->getLocale(),
            '_controller' => $this->getController(),
            'content' => $this->getContent()
        );
    }

    public function setEntityManager($entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * Returns object attached to route
     *
     * @return null
     */
    public function getContent() {
        if (!empty($this->content)) {
            return $this->content;
        }

        if (!empty($this->routeContent)) {

            $this->content = $this->resolver->getContent($this);
            return $this->content;
        }

        return null;
    }

    /**
     * Sets content as attached object and populates
     * 'routeContent' property.
     *
     * @param object $content
     * @param string $field
     */
    public function setContent($content, $field = 'id') {

        $this->content = $content;
        $this->routeContent = $this->resolver->getRouteContent($content, $field);

        return $this;
    }

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
     * Set created
     *
     * @param \DateTime $created
     * @return Route
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Route
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set format
     *
     * @param string $format
     * @return Route
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Get format
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set method
     *
     * @param string $method
     * @return Route
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get method
     *
     * @return string
     */
    public function getMethod()
    {
        if (empty($this->method)) {
            return 'GET';
        }
        return $this->method;
    }

    /**
     * Set permanent
     *
     * @param boolean $permanent
     * @return Route
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

    /**
     * @ORM\PrePersist
     */
    public function prePersist() {
        $this->setCreated(new \DateTime);
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function preUpdate() {
        $this->setUpdated(new \DateTime);
    }
}