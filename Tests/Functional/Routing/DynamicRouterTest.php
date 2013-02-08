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

    public static function setupBeforeClass(array $options = array(), $routebase = null)
    {
        parent::setupBeforeClass(array(), basename(self::ROUTE_ROOT));
        self::$router = self::$kernel->getContainer()->get('router');
//        self::$routeNamePrefix = self::$kernel->getContainer()->get('symfony_cmf_routing_extra.route_repository')->getRouteNamePrefix();

//        $root = self::$dm->find(null, self::ROUTE_ROOT);

        // do not set a content here, or we need a valid request and so on...
        $route = new Route;
//        $route->setPosition($root, 'testroute');
//        $route->setVariablePattern('/{slug}/{id}');
//        $route->setDefault('id', '0');
//        $route->setRequirement('id', '[0-9]+');
        $route->setDefault(RouteObjectInterface::CONTROLLER_NAME, 'testController');
        // TODO: what are the options used for? we should test them too if it makes sense
//        self::$dm->persist($route);

        $childroute = new Route;
        $childroute->setPosition($route, 'child');
        $childroute->setDefault(RouteObjectInterface::CONTROLLER_NAME, 'testController');
//        self::$dm->persist($childroute);

//        self::$dm->flush();
    }

    public function testMatch()
    {
//        $expected = array(
//            RouteObjectInterface::CONTROLLER_NAME => 'testController',
//            '_route'        => self::$routeNamePrefix.'_test_routing_testroute_child',
//        );
//
//        $matches = self::$router->match('/testroute/child');
//        ksort($matches);
//        $this->assertEquals($expected, $matches);
    }

    public function testMatchParameters()
    {
//        $expected = array(
//            RouteObjectInterface::CONTROLLER_NAME   => 'testController',
//            '_route'        => self::$routeNamePrefix.'_test_routing_testroute',
//            'id'            => '123',
//            'slug'          => 'child',
//        );
//
//        $matches = self::$router->match('/testroute/child/123');
//        ksort($matches);
//        $this->assertEquals($expected, $matches);
    }

    /**
     * @expectedException Symfony\Component\Routing\Exception\ResourceNotFoundException
     */
    public function testNoMatch()
    {
        self::$router->match('/random/route');
    }

    /**
     * @expectedException Symfony\Component\Routing\Exception\MethodNotAllowedException
     */
    public function testNotAllowed()
    {
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

    public function testGenerate()
    {
//        $route = self::$dm->find(null, self::ROUTE_ROOT.'/testroute/child');
//        $url = self::$router->generate('', array('route' => $route, 'test' => 'value'));
//        $this->assertEquals('/testroute/child?test=value', $url);
    }

    public function testGenerateAbsolute()
    {
//        $route = self::$dm->find(null, self::ROUTE_ROOT.'/testroute/child');
//        $url = self::$router->generate('', array('route' => $route, 'test' => 'value'), true);
//        $this->assertEquals('http://localhost/testroute/child?test=value', $url);
    }

    public function testGenerateParameters()
    {
//        $route = self::$dm->find(null, self::ROUTE_ROOT.'/testroute');
//
//        $url = self::$router->generate('', array('route' => $route, 'slug' => 'gen-slug', 'test' => 'value'));
//        $this->assertEquals('/testroute/gen-slug?test=value', $url);
    }

//    /**
//     * @expectedException Symfony\Component\Routing\Exception\InvalidParameterException
//     */
//    public function testGenerateParametersInvalid()
//    {
//        $route = self::$dm->find(null, self::ROUTE_ROOT.'/testroute');
//
//        self::$router->generate('', array('route' => $route, 'slug' => 'gen-slug', 'id' => 'nonumber'));
//    }
}
