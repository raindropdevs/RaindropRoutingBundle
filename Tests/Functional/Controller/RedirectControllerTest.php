<?php

namespace Raindrop\RoutingBundle\Tests\Functional\Controller;

use Raindrop\RoutingBundle\Tests\Functional\BaseTestCase;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Raindrop\RoutingBundle\Entity\Route;
use Raindrop\RoutingBundle\Controller\GenericController;
use Raindrop\RoutingBundle\Entity\ExternalRoute;
use Symfony\Component\HttpFoundation\Request;
use Raindrop\RoutingBundle\Resolver\ContentResolver;

class RedirectControllerTest extends BaseTestCase
{
    const ROUTE_ROOT = '/test/routing';

    /**
     * @var \Raindrop\RoutingBundle\Controller\GenericController
     */
    protected static $controller;
    protected static $resolver;

    public static function setupBeforeClass(array $options = array())
    {
        parent::setupBeforeClass(array(), basename(self::ROUTE_ROOT));
        self::$controller = new GenericController();
        self::$controller->setContainer(self::$kernel->getContainer());
        self::$resolver = new ContentResolver;
        self::$resolver->setEntityManager(self::$em);
    }

    public function testInnerRedirect() {

        $route = new Route;
        $route->setResolver(self::$resolver);
        $route->setName('my_route');
        $route->setPath('/path/to/redirecting/route');
        $route->setController('raindrop_routing.generic_controller:redirectRouteAction');

        $anotherRoute = new Route;
        $route->setResolver(self::$resolver);
        $anotherRoute->setName('another_route');
        $anotherRoute->setPath('/path/to/target/route');
        $anotherRoute->setController('AcmeDemoBundle:Default:index');

        self::$em->persist($anotherRoute);
        self::$em->flush();

        $route->setContent($anotherRoute);

        self::$em->persist($route);
        self::$em->flush();

        $request = new RequestStub;
        $request->param = 'my_route';
        $request->query = new FakeClass();
        $request->query->all = array();
        self::$kernel->getContainer()->set('request', $request);

        $response = self::$controller->redirectRouteAction($anotherRoute);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertSame('http://localhost/path/to/target/route', $response->getTargetUrl());
    }

    public function testInnerPermanentRedirect() {

        // recreate kernel at each test...
        self::setupBeforeClass();

        $route = new Route;
        $route->setResolver(self::$resolver);
        $route->setName('my_route');
        $route->setPath('/path/to/redirecting/route2');
        $route->setPermanent(); // this for 301 status
        $route->setController('raindrop_routing.generic_controller:redirectRouteAction');

        $anotherRoute = new Route;
        $route->setResolver(self::$resolver);
        $anotherRoute->setName('another_inner_route');
        $anotherRoute->setPath('/path/to/target/route2');
        $anotherRoute->setController('AcmeDemoBundle:Default:index');

        self::$em->persist($anotherRoute);
        self::$em->flush();

        $route->setContent($anotherRoute);

        self::$em->persist($route);
        self::$em->flush();

        $request = new RequestStub;
        $request->param = 'my_route';
        $request->query = new FakeClass();
        $request->query->all = array();
        self::$kernel->getContainer()->set('request', $request);

        $response = self::$controller->redirectRouteAction($anotherRoute);
        $this->assertSame(301, $response->getStatusCode());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertSame('http://localhost/path/to/target/route2', $response->getTargetUrl());
    }

    public function testExternalRedirectWithParameters()
    {
        self::setupBeforeClass();

        $externalRoute = new ExternalRoute;
        $externalRoute->setUri('http://www.google.com/');

        self::$em->persist($externalRoute);
        self::$em->flush();

        $route = new Route;
        $route->setResolver(self::$resolver);
        $route->setName('my_redirect_to_external');
        $route->setPath('/path/to/external/route');
        $route->setController('raindrop_routing.generic_controller:redirectRouteAction');
        $route->setContent($externalRoute);

        self::$em->persist($route);
        self::$em->flush();


        $request = new RequestStub;
        $request->param = 'my_route';
        $request->query = new FakeClass();
        $request->query->setAll(array('a' => '1', 'b' => '2'));
        self::$kernel->getContainer()->set('request', $request);


        $response = self::$controller->redirectRouteAction($externalRoute);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertSame('http://www.google.com/?a=1&b=2', $response->getTargetUrl());
    }

    public function testExternalPermanentRedirect() {

        self::setupBeforeClass();


        $externalRoute = new ExternalRoute;
        $externalRoute->setPermanent();
        $externalRoute->setUri('http://www.twitter.com/');

        self::$em->persist($externalRoute);
        self::$em->flush();

        $route = new Route;
        $route->setResolver(self::$resolver);
        $route->setName('my_redirect_to_external_2');
        $route->setPath('/path/to/external/route2');
        $route->setController('raindrop_routing.generic_controller:redirectRouteAction');
        $route->setContent($externalRoute);

        self::$em->persist($route);
        self::$em->flush();


        $request = new RequestStub;
        $request->param = 'my_route';
        $request->query = new FakeClass();
        $request->query->all = array();
        self::$kernel->getContainer()->set('request', $request);


        $response = self::$controller->redirectRouteAction($externalRoute);
        $this->assertSame(301, $response->getStatusCode());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertSame('http://www.twitter.com/', $response->getTargetUrl());
    }
}


class FakeClass {

    public $all;

    public function setAll($all) {
        $this->all = $all;
    }

    public function all() {
        return $this->all;
    }
}

class ResolverStub {
    public function getContent() {
        return 'ok';
    }
}

class RequestStub {

    public $query, $param;
    protected $resolver;

    public function __contruct($param) {
        $this->param = $param;
        $this->query = new FakeClass;
        $this->resolver = new ResolverStub;
    }

    public function get($name) {
        return $this->param;
    }

    public function all() {
        return array();
    }
}