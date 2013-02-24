<?php

namespace Raindrop\RoutingBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Raindrop\RoutingBundle\Routing\Base\ExternalRouteInterface;

/**
 * Base generic controller
 */
class GenericController extends Controller {

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
            if (count($routeParams)) {
                $uri .= ((strpos($uri, '?') === false) ? '?' : '&') . http_build_query($routeParams);
            }
        } else {
            $current_route = $this->get('raindrop_routing.route_repository')->findOneByName($this->getRequest()->get('_route'));
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
    public function templateAction($content) {

        return $this->get('raindrop_routing.response_manager')->response($content, $content->getTemplate(), $content->getArray());
    }
}
