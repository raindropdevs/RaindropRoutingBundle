<?php

namespace Raindrop\RoutingBundle\Entity;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Routing\Route as SymfonyRoute;
use Symfony\Component\Routing\RouteCollection;

use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Raindrop\RoutingBundle\Routing\Base\RouteRepositoryInterface;

use Doctrine\ORM\EntityRepository;

/**
 * Repository to load routes from Doctrine ORM
 *
 * This is a doctrine repository
 *
 * @author mcaber@gmail.com
 * (taken from symfony routing extra bundle. original author david.buchmann@liip.ch)
 */
class RouteRepository extends EntityRepository implements RouteRepositoryInterface
{
    /**
     * Symfony routes always need a name in the collection. We generate routes
     * based on the route object, but need to use a name for example in error
     * reporting.
     * When generating, we just use this prefix, when matching, we append
     * whatever the repository returned as ID, replacing anything but
     * [^a-z0-9A-Z_.] with "_" to get unique valid route names.
     */
    protected $routeNamePrefix = 'dynamic_route';

    /**
     * @var EntityManager
     */
    // protected $em;

    /**
     * Class name of the route class, null for phpcr-odm as it can determine
     * the class on its own.
     *
     * @var string
     */
    protected $className;

    public function setPrefix($prefix)
    {
        $this->idPrefix = $prefix;
    }

    protected function getCandidates($url)
    {
        /**
         * Clean the url by removing trailing slash
         * and multiple slash which is what most servers do
         * with directories.
         * EG: /en//contacts.json/ is cleaned to /en/contacts
         */
//        $url = rtrim($url, "/");
//        $url = preg_replace('/[\/]+/', '/', $url);

        // check for .json .html or .xml formats
        if (strpos($url, '.')) {
            $pos = strrpos($url, '.') + 1;
            $chunk = substr($url, $pos);
            if ($chunk === 'html' || $chunk === 'json' || $chunk === 'xml') {
                $url = substr($url, 0, $pos - 1);
            }
        }

        return $url;
    }

    /**
     * {@inheritDoc}
     *
     * This will return any document found at the url or up the path to the
     * prefix. If any of the documents does not extend the symfony Route
     * object, it is filtered out. In the extreme case this can also lead to an
     * empty list being returned.
     */
    public function findRouteByUrl($url)
    {
        if (! is_string($url) || strlen($url) < 1 || '/' != $url[0]) {
            throw new RouteNotFoundException("$url is not a valid route");
        }

        $candidates = $this->getCandidates($url);

        $collection = new RouteCollection();

        try {
            $route = $this->findOneByPath($candidates);
            if ($route instanceof SymfonyRoute) {
                $collection->add($route->getName(), $route);
            }
        } catch (RepositoryException $e) {
            // TODO: how to determine whether this is a relevant exception or not?
            // for example, getting /my//test (note the double /) is just an invalid path
            // and means another router might handle this.
            // but if the PHPCR backend is down for example, we want to alert the user
        }

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getRouteByName($name, $parameters = array())
    {
        $route = $this->findOneByName($name);
        if (!$route) {
            throw new RouteNotFoundException("No route found for name '$name'");
        }

        return $route;
    }
}
