<?php

namespace Raindrop\RoutingBundle\Routing\Base;

/**
 * Document for redirection entries with the RedirectController.
 *
 * Defines additional methods needed by the RedirectController to redirect
 * based on the route.
 *
 * This document may define (in order of precedence - the others can be empty):
 *
 * - uri: an absolute uri
 * - routeName and routeParameters: to be used with the standard symfony router
 *   or a route entry in the routeParameters for the DynamicRouter. Precedency
 *   between these is determined by the order of the routers in the chain
 *   router.
 *
 * With standard Symfony routing, you can just use uri / routeName and a
 * hashmap of parameters.
 *
 * For the dynamic router, you can return a RouteInterface instance in the
 * field 'route' of the parameters.
 *
 * Note: getRedirectContent must return the redirect route itself for the
 * integration with DynamicRouter to work.
 *
 * @author David Buchmann <david@liip.ch>
 */
interface RedirectRouteInterface extends RouteObjectInterface
{
    /**
     * Get the absolute uri to redirect to external domains.
     *
     * If this is non-empty, the other methods won't be used.
     *
     * @return string target absolute uri
     */
    public function getUri();

    /**
     * Get the name of the target route for working with the symfony standard
     * router.
     *
     * @return string target route name
     */
    function getRouteName();

    /**
     * Whether this should be a permanent or temporary redirect
     *
     * @return boolean
     */
    function isPermanent();

    /**
     * Get the parameters for the target route router::generate()
     *
     * Note that for the DynamicRouter, you return the target route
     * document as field 'route' of the hashmap.
     *
     * @return array Information to build the route
     */
    function getParameters();
}
