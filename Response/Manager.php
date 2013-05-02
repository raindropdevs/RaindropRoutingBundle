<?php

namespace Raindrop\RoutingBundle\Response;

use Raindrop\RoutingBundle\Entity\ContentInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Manager implements ContainerAwareInterface
{
    protected $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Retrieves a base response with proper last-modified/expires headers
     * for http caching
     *
     * @param  \Raindrop\RoutingBundle\Entity\ContentInterface $content
     * @param  type                                            $template   template name
     * @param  type                                            $parameters array of template variables
     * @param  int                                             $expires    expiration timestamp
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function response(ContentInterface $content, $template, $parameters, $expires = null)
    {
        if (is_null($expires)) {
            $expires = time() + 86400;
        }

        $response = new Response;
        $response->setPublic();
        $response->setLastModified($content->getUpdated());
        $response->headers->set('Expires', gmdate("D, d M Y H:i:s", $expires) . " GMT");

        if ($response->isNotModified($this->container->get('request'))) {
            // return the 304 Response immediately
            return $response;
        }

        return $this->container->get('templating')->renderResponse($template, $parameters, $response);
    }
}
