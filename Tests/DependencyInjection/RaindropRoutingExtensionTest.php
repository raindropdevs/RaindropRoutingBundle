<?php

namespace Raindrop\RoutingBundle\Tests\DependencyInjection;

use Raindrop\RoutingBundle\DependencyInjection\RaindropRoutingExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Processor;

class RaindropRoutingExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $config
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    protected function getBuilder(array $config = array())
    {
        $builder = new ContainerBuilder();

        $loader = new RaindropRoutingExtension();
        $loader->load($config, $builder);

        return $builder;
    }

    public function testLoadDefault()
    {
        $builder = $this->getBuilder();

        $this->assertTrue($builder->hasAlias('router'));
        $alias = $builder->getAlias('router');
        $this->assertEquals('raindrop_routing.router', $alias->__toString());

        $this->assertTrue($builder->hasDefinition('raindrop_routing.router'));
        $methodCalls = $builder->getDefinition('raindrop_routing.router')->getMethodCalls();
        $addMethodCalls = array_filter(
            $methodCalls,
            function ($call) {
                return 'add' == $call[0];
            }
        );

        $this->assertCount(1, $addMethodCalls);
        $addMethodCall = reset($addMethodCalls);

        $params = $addMethodCall[1];
        $this->assertCount(2, $params);

        /** @var $reference \Symfony\Component\DependencyInjection\Reference */
        list($reference, $priority) = $params;

        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $reference);
        $this->assertEquals(100, $priority);

        $this->assertEquals('router.default', $reference->__toString());
    }

    public function testLoadConfigured()
    {
        $config = array(
            array(
                'chain' => array(
                    'routers_by_id' => $providedRouters = array(
                        'router.custom' => 200,
                        'router.default' => 300
                    ),
                    'replace_symfony_router' => false
                )
            )
        );

        $builder = $this->getBuilder($config);

        $this->assertFalse($builder->hasAlias('router'));

        $this->assertTrue($builder->hasDefinition('raindrop_routing.router'));
        $methodCalls = $builder->getDefinition('raindrop_routing.router')->getMethodCalls();
        $addMethodCalls = array_filter(
            $methodCalls,
            function ($call) {
                return 'add' == $call[0];
            }
        );

        $this->assertCount(2, $addMethodCalls);

        $routersAdded = array();

        foreach ($addMethodCalls as $addMethodCall) {
            $params = $addMethodCall[1];
            $this->assertCount(2, $params);
            /** @var $reference \Symfony\Component\DependencyInjection\Reference */
            list($reference, $priority) = $params;

            $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $reference);

            $routersAdded[$reference->__toString()] = $priority;
        }

        $this->assertEquals($providedRouters, $routersAdded);
    }

}
