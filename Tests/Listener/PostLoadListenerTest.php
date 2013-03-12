<?php

namespace Raindrop\RoutingBundle\Tests\Listener;

use Raindrop\RoutingBundle\Tests\BaseTestCase;
use Raindrop\RoutingBundle\Listener\PostLoadListener;
use Raindrop\RoutingBundle\Resolver\ContentResolver;

class PostLoadListenerTest extends BaseTestCase
{
    public function setUp()
    {
        $this->lifecycle = $this->buildMock('Doctrine\ORM\Event\LifecycleEventArgs');
        $this->entity = $this->buildMock('Raindrop\RoutingBundle\Entity\Route');
        $this->fakeEntity = $this->buildMock('Raindrop\RoutingBundle\Entity\FakeRoute');
        $this->em = $this->buildMock('Doctrine\ORM\EntityManager');
        $this->resolver = new ContentResolver;
    }

    protected function getListener() {
        return new PostLoadListener($this->resolver);
    }

    public function testPostLoadWithRouteEntity()
    {

        $listener = $this->getListener();

        $this->lifecycle->expects($this->once())
                ->method('getEntity')
                ->will($this->returnValue($this->entity));

        $this->lifecycle->expects($this->once())
                ->method('getEntityManager')
                ->will($this->returnValue($this->em));

        $this->entity->expects($this->once())
                ->method('init')
                ->with($this->resolver);

        $listener->postLoad($this->lifecycle);
    }

    public function testPostLoadWithoutRouteEntity()
    {
        $listener = $this->getListener();

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
