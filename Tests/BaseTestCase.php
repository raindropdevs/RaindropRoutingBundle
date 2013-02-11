<?php

namespace Raindrop\RoutingBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BaseTestCase extends WebTestCase {
    protected function buildMock($class, array $methods = array()) {
        return $this->getMockBuilder($class)
                ->disableOriginalConstructor()
                ->setMethods($methods)
                ->getMock();
    }
}