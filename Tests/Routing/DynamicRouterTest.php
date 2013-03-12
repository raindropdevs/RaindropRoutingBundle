<?php

namespace Raindrop\RoutingBundle\Tests\Routing;

use Raindrop\RoutingBundle\Tests\BaseTestCase;
use Raindrop\RoutingBundle\Routing\DynamicRouter;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Raindrop\RoutingBundle\Resolver\ContentResolver;


class DynamicRouterTest extends BaseTestCase {

    public function testGetMatcher()
    {
        $repository = $this->buildMock("Raindrop\\RoutingBundle\\Routing\\Base\\RouteRepositoryInterface", array('findRouteByUrl', 'getRouteByName'));
        $router = new DynamicRouter($repository);
        $context = $this->buildMock('Symfony\\Component\\Routing\\RequestContext');
        $router->setContext($context);
        $matcher = $router->getMatcher(new \Symfony\Component\Routing\RouteCollection());
        $this->assertInstanceOf('Symfony\Component\Routing\Matcher\UrlMatcherInterface', $matcher);
    }

    public function testMatch() {
        $url = '/test-route';
        $name = '_test_route';
        $controller = 'NameSpace\\Controller::action';
        $locale = 'en';

        $routeEntity = $this->getMockRoute($url, $controller, $locale);


        $collection = new RouteCollection;
        $collection->add($name, $routeEntity);

        $repository = $this->buildMock("Raindrop\\RoutingBundle\\Routing\\Base\\RouteRepositoryInterface", array('findRouteByUrl', 'getRouteByName'));
        $repository->expects($this->once())
            ->method('findRouteByUrl')
            ->with($url)
            ->will($this->returnValue($collection));

        $router = new DynamicRouter($repository);
        $context = $this->buildMock('Symfony\\Component\\Routing\\RequestContext');
        $router->setContext($context);

        $result = $router->match($url);
        $expected = array(
            '_locale' => $locale,
            '_controller' => $controller,
            '_route' => $name,
            'content' => null
        );
        $this->assertEquals($expected, $result);


        $url2 = '/test-route/2';
        $name2 = '_test_route_2';
        $controller2 = 'NameSpace\\Controller::action';
        $locale2 = 'it';

        $routeEntity2 = $this->getMockRoute($url2, $controller2, $locale2);


        $collection2 = new RouteCollection;
        $collection2->add($name2, $routeEntity2);

        $repository2 = $this->buildMock("Raindrop\\RoutingBundle\\Routing\\Base\\RouteRepositoryInterface", array('findRouteByUrl', 'getRouteByName'));
        $repository2->expects($this->once())
            ->method('findRouteByUrl')
            ->with($url2)
            ->will($this->returnValue($collection2));

        $router2 = new DynamicRouter($repository2);
        $context2 = $this->buildMock('Symfony\\Component\\Routing\\RequestContext');
        $router2->setContext($context2);


        $result2 = $router2->match($url2);
        $expected2 = array(
            '_locale' => $locale2,
            '_controller' => $controller2,
            '_route' => $name2,
            'content' => null
        );
        $this->assertEquals($expected2, $result2);
    }

    public function testMatchWithContent() {

        $url = '/test-route';
        $name = '_test_route';
        $controller = 'NameSpace\\Controller::action';
        $locale = 'en';
        $content = new ContentMock;
        $content->setId(1);

        $repositoryMock = $this->buildMock('\\Doctrine\\ORM\\EntityRepository');
        $repositoryMock->expects($this->once())
            ->method('find')
            ->with(1)
            ->will($this->returnValue($content));

        $entityManagerMock = $this->buildMock('\\Doctrine\\ORM\\EntityManager');
        $entityManagerMock->expects($this->once())
            ->method('getRepository')
            ->with('Raindrop\RoutingBundle\Tests\Routing\ContentMock')
            ->will($this->returnValue($repositoryMock));

        $resolver = new ContentResolver;
        $resolver->setEntityManager($entityManagerMock);

        $routeEntity = $this->getMockRoute($url, $controller, $locale, false);
        $routeEntity->setRouteContent('Raindrop\RoutingBundle\Tests\Routing\ContentMock::1');
        $routeEntity->setEntityManager($entityManagerMock);
        $routeEntity->setResolver($resolver);


        $collection = new RouteCollection;
        $collection->add($name, $routeEntity);

        $repository = $this->buildMock("Raindrop\\RoutingBundle\\Routing\\Base\\RouteRepositoryInterface", array('findRouteByUrl', 'getRouteByName'));
        $repository->expects($this->once())
            ->method('findRouteByUrl')
            ->with($url)
            ->will($this->returnValue($collection));

        $router = new DynamicRouter($repository);
        $container = $this->buildMock("Symfony\\Component\\DependencyInjection\\Container");
        $container
            ->expects($this->once())
            ->method('get')
            ->with('request')
            ->will($this->returnValue(new RequestStub));
        $router->setContainer($container);
        $context = $this->buildMock('Symfony\\Component\\Routing\\RequestContext');
        $router->setContext($context);

        $result = $router->match($url);
        $expected = array(
            '_locale' => $locale,
            '_controller' => $controller,
            '_route' => $name,
            'content' => $content
        );
        $this->assertEquals($expected, $result);
    }

    /**
    * @expectedException \Symfony\Component\Routing\Exception\ResourceNotFoundException
    */
    public function testNoMatch() {
        $url = '/test-route';
        $url2 = '/test-route2';
        $name = '_test_route';
        $controller = 'NameSpace\\Controller::action';
        $locale = 'en';

        $routeEntity = $this->getMockRoute($url, $name, $controller, $locale);

        $collection = new RouteCollection;
        $collection->add($name, $routeEntity);

        $repository = $this->buildMock("Raindrop\\RoutingBundle\\Routing\\Base\\RouteRepositoryInterface", array('findRouteByUrl', 'getRouteByName'));
        $repository->expects($this->once())
            ->method('findRouteByUrl')
            ->with($url2)
            ->will($this->returnValue($collection));

        $router = new DynamicRouter($repository);
        $context = $this->buildMock('Symfony\\Component\\Routing\\RequestContext');
        $router->setContext($context);

        $router->match($url2);
    }

    public function testGenerateFromName() {
        $url = '/test-route';
        $name = '_test_route';
        $controller = 'NameSpace\\Controller::action';
        $locale = 'en';

        $routeEntity = $this->getMockRoute($url, $name, $controller, $locale);

        $repository = $this->buildMock("Raindrop\\RoutingBundle\\Routing\\Base\\RouteRepositoryInterface", array('findRouteByUrl', 'getRouteByName'));
        $repository->expects($this->once())
            ->method('getRouteByName')
            ->with($name, array())
            ->will($this->returnValue($routeEntity));

        $router = new DynamicRouter($repository);
        $context = $this->buildMock('Symfony\\Component\\Routing\\RequestContext');
        $context->expects($this->once())
            ->method('getParameters')
            ->will($this->returnValue(array()));
        $router->setContext($context);

        $result = $router->generate($name);
        $this->assertEquals($url, $result);
    }


    /**
     * @expectedException \Symfony\Component\Routing\Exception\RouteNotFoundException
     */
    public function testGenerateFromNameNotFound() {

        $name2 = '_test_route2';

        $repository = $this->buildMock("Raindrop\\RoutingBundle\\Routing\\Base\\RouteRepositoryInterface", array('findRouteByUrl', 'getRouteByName'));
        $repository->expects($this->once())
            ->method('getRouteByName')
            ->with($name2, array())
            ->will($this->returnValue(null));

        $router = new DynamicRouter($repository);
        $context = $this->buildMock('Symfony\\Component\\Routing\\RequestContext');
        $router->setContext($context);

        $router->generate($name2);
    }


    public function getMockRoute($url, $controller, $locale, $withRouteContentMock = true) {

        // cant figure out why it fails without at least 1 method
        $routeEntity = $this->buildMock('Raindrop\\RoutingBundle\\Entity\\Route', array('randomMethod'));

        $routeEntity->setOptions(array());
        $routeEntity->setPath($url);
        $routeEntity->setLocale($locale);
        $routeEntity->setController($controller);

        return $routeEntity;
    }
}


class RouteMock extends Route implements \Raindrop\RoutingBundle\Routing\Base\RouteObjectInterface
{
    private $locale;

    public function __construct($locale = null)
    {
        $this->locale = $locale;
    }
    public function getRouteContent()
    {
        return null;
    }
    public function getDefaults()
    {
        $defaults = array();
        if (! is_null($this->locale)) {
            $defaults['_locale'] = $this->locale;
        }
        return $defaults;
    }
}

class ContentMock {
    protected $id;
    public function setId($id) {
        $this->id = $id;

        return $this;
    }

    public function getId() {
        return $this->id;
    }
}

class AttributesStub
{
    public function set($p, $q) {
        //
    }
}

class RequestStub {

    public $attributes;

    public function __construct() {
        $this->attributes = new AttributesStub;
    }
}
