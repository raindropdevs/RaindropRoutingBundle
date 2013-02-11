<?php

namespace Raindrop\RoutingBundle\Tests\Controller;

use Raindrop\RoutingBundle\Tests\Functional\BaseTestCase as WebTestCase;
use Raindrop\RoutingBundle\Controller\GenericController;

class DefaultControllerTest extends WebTestCase
{
    
    /**
     * @var \Raindrop\RoutingBundle\Controller\GenericController
     */
    protected static $controller;

    public static function setupBeforeClass(array $options = array())
    {
        parent::setupBeforeClass(array());
        self::$controller = new GenericController;
        self::$controller->setContainer(self::$kernel->getContainer());
    }
    
    public function testIndex()
    {
//        $client = static::createClient();
//        $crawler = $client->request('GET', '/');
//        $this->assertTrue($crawler->filter('html:contains("a")')->count() > 0);
    }
}
