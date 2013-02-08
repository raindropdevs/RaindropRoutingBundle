<?php

namespace Raindrop\RoutingBundle\Tests\Functional\Entity;

use Raindrop\RoutingBundle\Entity\Route;
use Raindrop\RoutingBundle\Routing\Base\RouteObjectInterface;
use Raindrop\RoutingBundle\Tests\Functional\BaseTestCase;

class RouteRepositoryTest extends BaseTestCase
{
    const ROUTE_ROOT = '/test/routing';

    private static $repository;

    public static function setupBeforeClass(array $options = array(), $routebase = null)
    {
        parent::setupBeforeClass(array(), basename(self::ROUTE_ROOT));
        self::$repository = self::$em->getRepository('Raindrop\RoutingBundle\Entity\Route');
    }

    public function testFindRouteByUrl()
    {
        $route = new Route;
        $route->setName('route_repo_test');
        $route->setPath('/path/to/repo');
        $route->setController('GenericBundle:Default:index');
        
        self::$em->persist($route);
        self::$em->flush();
        self::$em->clear();
        
//        $route = new Route;
//        $root = self::$dm->find(null, self::ROUTE_ROOT);
//
//        $route->setPosition($root, 'testroute');
//        self::$dm->persist($route);
//
//        // smuggle a non-route thing into the repository
//        $noroute = new Generic;
//        $noroute->setParent($route);
//        $noroute->setNodename('noroute');
//        self::$dm->persist($noroute);
//
//        $childroute = new Route;
//        $childroute->setPosition($noroute, 'child');
//        self::$dm->persist($childroute);
//
//        self::$dm->flush();
//
//        self::$dm->clear();
//
        $collection = self::$repository->findRouteByUrl('/path/to/repo');
        $this->assertInstanceOf('Symfony\Component\Routing\RouteCollection', $collection);
        $return = $collection->get('route_repo_test');
        $this->assertInstanceOf('Raindrop\\RoutingBundle\\Routing\\Base\\RouteObjectInterface', $return);
//        $this->assertCount(3, $routes);
//
//        foreach ($routes as $route) {
//            $this->assertInstanceOf('Symfony\\Cmf\\Component\\Routing\\RouteObjectInterface', $route);
//        }
    }

    /**
     * @expectedException Symfony\Component\Routing\Exception\RouteNotFoundException
     */
    public function testFindInvalidUrl()
    {
        self::$repository->findRouteByUrl('x');
    }

    public function testFindNoDbUrl()
    {
        $collection = self::$repository->findRouteByUrl('///');
        $this->assertInstanceOf('Symfony\\Component\\Routing\\RouteCollection', $collection);
        $this->assertCount(0, $collection);
    }
}
