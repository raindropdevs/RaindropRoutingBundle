<?php

namespace Raindrop\RoutingBundle\Routing\Generator;

use Symfony\Component\Routing\Generator\UrlGenerator as BaseUrlGenerator;

/**
 * This class gets extended just to avoid querying route content
 * which is useless for route generation but results in a lot of single
 * queries.
 * This should save resources when rendering a hundred elements menu
 * for example.
 */
class UrlGenerator extends BaseUrlGenerator
{
    /**
     * {@inheritDoc}
     */
    public function generate($name, $parameters = array(), $absolute = false)
    {
        if (null === $route = $this->routes->get($name)) {
            throw new RouteNotFoundException(sprintf('Route "%s" does not exist.', $name));
        }

        // the Route has a cache of its own and is not recompiled as long as it does not get modified
        $compiledRoute = $route->compile();

        // RaindropRoutingBundle: since the Route path is fully saved to database, there's no need of defaults.
        $routeDefaults = array(
            '_locale' => $route->getLocale()
        );

        return $this->doGenerate(
            $compiledRoute->getVariables(),
            $routeDefaults,
            $route->getRequirements(),
            $compiledRoute->getTokens(),
            $parameters,
            $name,
            $absolute,
            $compiledRoute->getHostTokens(),
            $route->getSchemes() ? $route->getSchemes() : []
            );
    }
}
