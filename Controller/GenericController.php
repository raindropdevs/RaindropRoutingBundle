<?php

namespace Raindrop\RoutingBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Raindrop\RoutingBundle\Entity\ContentInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Raindrop\RoutingBundle\Routing\Base\ExternalRouteInterface;

/**
 * Base generic controller
 */
class GenericController extends Controller {
    
    /**
     * Retrieve base response and set some headers for http caching.
     * This relies on the getUpdated method, so the object must implement
     * a ContentInterface.
     * 
     * @param \Raindrop\RoutingBundle\Entity\ContentInterface $object
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getBaseResponse(ContentInterface $object) {

        $response = new Response;
        $response->setPublic();
        $response->setLastModified($object->getUpdated());
        $response->headers->set('Expires', gmdate("D, d M Y H:i:s", time() + 86400) . " GMT");
        
        return $response;
    }

    /**
     * Returns a 301/302 redirect response based on content parameters.
     * 
     * @param \Raindrop\RoutingBundle\Routing\Base\ExternalRouteInterface $content
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectRouteAction($content) {

        $routeParams = $this->get('request')->query->all(); // do not lose eventual get parameters

        if ($content instanceof ExternalRouteInterface) {
            $http_status = $content->getPermanent() ? 301 : 302;
            $uri = $content->getUri();
            $uri .= ((strpos($uri, '?') === false) ? '?' : '&') . http_build_query($routeParams);
        } else {
            $current_route = $this->get('raindrop_routing.route_repository')->findOneByName($this->get('request')->get('_route'));
            $http_status = $current_route->getPermanent() ? 301 : 302;
            $uri = $this->get('router')->generate($content->getName(), $routeParams, true);
        }

        $response = new RedirectResponse($uri, $http_status);
        $response->setVary('accept-language');
        return $response;
    }
    

    /**
     * Renders a template given an object.
     * 
     * @param type $content
     * @return type
     */
    public function templateAction(ContentInterface $content) {

        $response = $this->getBaseResponse($content);

        if ($response->isNotModified($this->getRequest())) {
            // return the 304 Response immediately
            return $response;
        }
        
        return $this->render($content->getTemplate(), $content->getArray(), $response);
    }
}
