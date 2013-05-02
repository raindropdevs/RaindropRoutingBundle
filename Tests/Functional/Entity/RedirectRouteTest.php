<?php

namespace Raindrop\RoutingBundle\Tests\Functional\Entity;

use Raindrop\RoutingBundle\Entity\Route;
use Raindrop\RoutingBundle\Resolver\ContentResolver;

use Raindrop\RoutingBundle\Tests\Functional\BaseTestCase;

class RedirectRouteTest extends BaseTestCase
{
    const ROUTE_ROOT = '/test/redirectroute';

    protected static $resolver;

    public static function setupBeforeClass(array $options = array(), $routebase = null)
    {
        parent::setupBeforeClass(array(), basename(self::ROUTE_ROOT));
        self::$resolver = new ContentResolver;
        self::$resolver->setEntityManager(self::$em);
    }

    public function testRedirectDoctrine()
    {
        $route = new Route;
        $route->setResolver(self::$resolver);
        $route->setName('my_route');
        $route->setPath('/path/to/my/route');
        $route->setController('AcmeDemoBundle:Default:index');

        self::$em->persist($route);
        self::$em->flush();
        self::$em->clear();

        $routeRepo = self::$em->getRepository('Raindrop\RoutingBundle\Entity\Route');
        $return = $routeRepo->findOneByPath('/path/to/my/route');

        $this->assertEquals(array(
            '_locale' => null,
            '_controller' => 'AcmeDemoBundle:Default:index',
            'content' => null
        ), $return->getDefaults());

//        $root = self::$dm->find(null, self::ROUTE_ROOT);
//
//        $route = new Route;
//        $route->setRouteContent($root); // this happens to be a referenceable node
//        $route->setPosition($root, 'testroute');
//        self::$dm->persist($route);
//
//        $redirect = new RedirectRoute;
//        $redirect->setPosition($root, 'redirect');
//        $redirect->setRouteTarget($route);
//        $redirect->setDefault('test', 'toast');
//        self::$dm->persist($redirect);
//
//        self::$dm->flush();
//
//        self::$dm->clear();
//
//        $route = self::$dm->find(null, self::ROUTE_ROOT.'/testroute');
//        $redirect = self::$dm->find(null, self::ROUTE_ROOT.'/redirect');
//
//        $this->assertInstanceOf('Symfony\\Cmf\\Component\\Routing\\RedirectRouteInterface', $redirect);
//        $this->assertSame($redirect, $redirect->getRouteContent());
//        $params = $redirect->getParameters();
//        $this->assertArrayHasKey('route', $params);
//        $this->assertSame($route, $params['route']);
//        $defaults = $redirect->getDefaults();
//        $this->assertEquals(array('test' => 'toast'), $defaults);
    }

}
