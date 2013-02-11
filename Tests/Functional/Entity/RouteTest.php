<?php

namespace Raindrop\RoutingBundle\Tests\Functional\Entity;

use Raindrop\RoutingBundle\Entity\Route;
use Raindrop\RoutingBundle\Tests\Functional\BaseTestCase;
use Raindrop\RoutingBundle\Entity\SampleEntity;

class RouteTest extends BaseTestCase
{
    const ROUTE_ROOT = '/test/routing';

    public static function setupBeforeClass(array $options = array(), $routebase = null)
    {
        parent::setupBeforeClass(array(), basename(self::ROUTE_ROOT));
    }

    public function testPersist()
    {
        $route = new Route;
        $route->setName('a_route');
        $route->setPath('/path/to/a/route');
        $route->setController('AcmeDemoBundle:Default:index');
        self::$em->persist($route);
        
        self::$em->flush();
        self::$em->clear();
        
        $return = self::$em->getRepository('Raindrop\RoutingBundle\Entity\Route')->findOneByPath('/path/to/a/route');
        $this->assertEquals('a_route', $return->getName());
        $this->assertEquals('/path/to/a/route', $return->getPath());
        $this->assertEquals('AcmeDemoBundle:Default:index', $return->getController());
        
        $defaults = $return->getDefaults();
        $this->assertEquals($route->getDefaults(), $defaults);
        $this->assertArrayHasKey('_locale', $defaults);
        $this->assertArrayHasKey('_controller', $defaults);
        $this->assertArrayHasKey('content', $defaults);

        $requirements = $return->getRequirements();
        $this->assertEquals($route->getRequirements(), $requirements);
        $this->assertArrayHasKey('_method', $requirements);
        $this->assertArrayHasKey('_format', $requirements);
        $this->assertArrayHasKey('path', $requirements);
        
//        $options = $route->getOptions();
//        $this->assertArrayHasKey('compiler_class', $options);
    }

    public function testPersistEmptyOptions()
    {
        $route = new Route;
        $route->setName('my_route');
        $route->setPath('/path/to/my/route');
        $route->setController('AcmeDemoBundle:Default:index');

        self::$em->persist($route);
        self::$em->flush();

        self::$em->clear();

        $return = self::$em->getRepository('Raindrop\RoutingBundle\Entity\Route')->findOneByPath('/path/to/my/route');

        $defaults = $return->getDefaults();
        $this->assertCount(3, $defaults);

        $requirements = $return->getRequirements();
        $this->assertCount(3, $requirements);

        $options = $return->getOptions();
        $this->assertTrue(1 >= count($options)); // there is a default option for the compiler
    }


    public function testSetContent() {
        $content = new Route;
        $content->setName('my_route_2');
        $content->setPath('/path/to/my/route2');
        $content->setController('AcmeDemoBundle:Default:hello');
        self::$em->persist($content);
        self::$em->flush();
        self::$em->clear();
        
        $id = $content->getId();
        
        $route = new Route;
        $route->setContent($content);
        $this->assertEquals('Raindrop\RoutingBundle\Entity\Route::' . $id, $route->getRouteContent());
    }
    
    public function testSetContentWithField() {        
        $content = new RecordStub;

        $route = new Route;
        $route->setContent($content, 'locale');
        $this->assertEquals('Raindrop\RoutingBundle\Tests\Functional\Entity\RecordStub:locale:en', $route->getRouteContent());
    }
    
    public function testSetContentArray() {
        $content = new RecordStub;
        $content2 = new RecordStub;

        $collection = array($content, $content2);
        $route = new Route;
        $route->setContent($collection, 'locale', 'en');

        $this->assertEquals('Raindrop\RoutingBundle\Tests\Functional\Entity\RecordStub:locale(s):en', $route->getRouteContent());
    }
}


class RecordStub {
    public function getLocale() {
        return 'en';
    }
    
    public function getId() {
        return 1;
    }
}