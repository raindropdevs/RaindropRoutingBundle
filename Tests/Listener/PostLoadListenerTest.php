<?php

namespace Raindrop\RoutingBundle\Tests\Listener;

use Raindrop\RoutingBundle\Tests\BaseTestCase;
use Raindrop\RoutingBundle\Listener\PostLoadListener;

class PostLoadListenerTest extends BaseTestCase
{
    public function setUp()
    {
        $this->lifecycle = $this->buildMock('Doctrine\ORM\Event\LifecycleEventArgs');
        $this->entity = $this->buildMock('Raindrop\RoutingBundle\Entity\Route');
        $this->fakeEntity = $this->buildMock('Raindrop\RoutingBundle\Entity\FakeRoute');
        $this->em = $this->buildMock('Doctrine\ORM\EntityManager');
    }

    public function testPostLoadWithRouteEntity()
    {
        $listener = new PostLoadListener();
        
        $this->lifecycle->expects($this->once())
                ->method('getEntity')
                ->will($this->returnValue($this->entity));
        
        $this->lifecycle->expects($this->once())
                ->method('getEntityManager')
                ->will($this->returnValue($this->em));
        
        $this->entity->expects($this->once())
                ->method('init')
                ->with($this->em);
        
        $listener->postLoad($this->lifecycle);
    }
    
    public function testPostLoadWithoutRouteEntity()
    {
        $listener = new PostLoadListener();
        
        $this->lifecycle->expects($this->once())
                ->method('getEntity')
                ->will($this->returnValue($this->fakeEntity));
        
        $this->lifecycle->expects($this->once())
                ->method('getEntityManager')
                ->will($this->returnValue($this->em));
        
        $this->entity->expects($this->never())
                ->method('init');
        
        $listener->postLoad($this->lifecycle);
    }
}
