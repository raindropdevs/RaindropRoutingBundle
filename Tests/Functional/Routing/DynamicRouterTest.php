<?php

namespace Raindrop\RoutingBundle\Tests\Functional\Routing;

use Raindrop\RoutingBundle\Entity\Route;
use Raindrop\RoutingBundle\Routing\DynamicRouter;
use Raindrop\RoutingBundle\Routing\Base\RouteObjectInterface;

use Raindrop\RoutingBundle\Tests\Functional\BaseTestCase;

/**
 * The goal of these tests is to test the interoperation with DI and everything.
 * We do not aim to cover all edge cases and exceptions - that is was the unit
 * test is here for.
 */
class DynamicRouterTest extends BaseTestCase
{
    /**
     * @var \Symfony\Cmf\Component\Routing\ChainRouter
     */
    protected static $router;
    protected static $routeNamePrefix;

    const ROUTE_ROOT = '/test/routing';

    public static function setupBeforeClass(array $options = array(), $routebase = null) {
        parent::setupBeforeClass(array(), basename(self::ROUTE_ROOT));
        self::$router = self::$kernel->getContainer()->get('router');
//        self::$routeNamePrefix = self::$kernel->getContainer()->get('symfony_cmf_routing_extra.route_repository')->getRouteNamePrefix();

//        $root = self::$dm->find(null, self::ROUTE_ROOT);

        // do not set a content here, or we need a valid request and so on...
        $route = new Route;
        $route->setName('test_route');
        $route->setPath('/test/route');
        $route->setController('test_controller');

        self::$em->persist($route);
        self::$em->flush();
        self::$em->clear();

//        $route->setPosition($root, 'testroute');
//        $route->setVariablePattern('/{slug}/{id}');
//        $route->setDefault('id', '0');
//        $route->setRequirement('id', '[0-9]+');
//        $route->setDefault(RouteObjectInterface::CONTROLLER_NAME, 'testController');
        // TODO: what are the options used for? we should test them too if it makes sense
//        self::$dm->persist($route);

//        $childroute = new Route;
//        $childroute->setPosition($route, 'child');
//        $childroute->setDefault(RouteObjectInterface::CONTROLLER_NAME, 'testController');
//        self::$dm->persist($childroute);

//        self::$dm->flush();
    }

    public function testMatch() {
//        $expected = array(
//            RouteObjectInterface::CONTROLLER_NAME => 'testController',
//            '_route'        => self::$routeNamePrefix.'_test_routing_testroute_child',
//        );
//
//        $matches = self::$router->match('/testroute/child');
//        ksort($matches);
//        $this->assertEquals($expected, $matches);
        $matches = self::$router->match('/test/route');

        $this->assertEquals(array(
            '_controller' => 'test_controller',
            'content' => null,
            '_route' => 'test_route',
            '_locale' => null
        ), $matches);
    }

    /**
     * @expectedException Symfony\Component\Routing\Exception\ResourceNotFoundException
     */
    public function testNoMatch() {
        self::$router->match('/non/existing/route');
    }

    /**
     * @expectedException Symfony\Component\Routing\Exception\MethodNotAllowedException
     */
    public function testNotAllowed() {
        // do not set a content here, or we need a valid request and so on...
        $route = new Route;
        $route->setName('not_allowed');
        $route->setController('testController');
        $route->setRequirement('_method', 'GET');
        $route->setPath('/notallowed');
        self::$em->persist($route);
        self::$em->flush();

        self::$router->getContext()->setMethod('POST');
        self::$router->match('/notallowed');
    }

    public function testGenerate() {
        $route = self::$em->getRepository('Raindrop\RoutingBundle\Entity\Route')->findOneByName('test_route');
        $url = self::$router->generate('test', array('route' => $route));
        $this->assertEquals('/test/route', $url);
    }

    public function testGenerateAbsolute() {
        $route = self::$em->getRepository('Raindrop\RoutingBundle\Entity\Route')->findOneByName('test_route');
        $url = self::$router->generate('test', array('route' => $route), true);
        $this->assertEquals('http://localhost/test/route', $url);
    }

    public function testGenerateWithParameters() {
        $route = self::$em->getRepository('Raindrop\RoutingBundle\Entity\Route')->findOneByName('test_route');
        $url = self::$router->generate('test', array('route' => $route, 'param' => 'someValue'), true);
        $this->assertEquals('http://localhost/test/route?param=someValue', $url);
    }
}
