# Raindrop Routing Bundle


This bundle is based on routing extra bundle for symfony-cmf.
It replaces symfony router with a chain router, appends standard router and dynamic router to it (as specified into configuration). This allows you to save routes to database and continue using symfony standard routing.

All routers attached to chain router can be sorted using a priority key specified into configuration.

This bundle also offers the option to link the route to any other entity, this object will be served when controller method gets invoked, as parameter.


### **INSTALLATION**:

Add raindrop/rounting-bundle into composer.json and run 'composer update'.

### **CONFIGURATION**:

Add following lines to app/config/config.yml

		raindrop_routing:
	    	chain:
	        	routers_by_id:
	            	router.default: 100
	            	raindrop_routing.dynamic_router: 10
	        	replace_symfony_router: true	        

This will instruct the configuration to detach symfony router, attach chain router
and append symfony standard router and the dynamic one.

The dynamic router fetches and stores routes from/to database using Doctrine ORM.

### **USAGE**:

#### Bind routes to controllers:

    $route = new Route;
    $route->setName('my_route');
    $route->setPath('/path/to/my/route');
    $route->setController('AcmeDemoBundle:Default:index');
										    
A get request with '/path/to/my/route' will now point to AcmeDemoBundle:Default:index

Into twig templates:

	{{ url('my_route') }}

This will resolve name to url as defined into database.

#### Bind routes to controller as service:

    $route = new Route;
    $route->setName('my_route');
    $route->setPath('/path/to/my/route');
    $route->setController('my_service.generic_controller:indexAction');

#### Bind routes to controller with target object(s):

In this case the controller must implement method signature as following. The parameter name 'content' is mandatory.

	class myController {

    	public function indexAction($content) {
			// your code here
    	}
    }

Content will be passed to the controller when invoked.

	$route = new Route;
    $route->setName('my_route');
    $route->setPath('/path/to/my/route');
	$route->setController('AcmeDemoBundle:Default:index');
	$route->setRouteContent('Acme\DemoBundle\Entity\SampleEntity:id:1');

When the reference field is 'id', it can be omitted as following.

	$route->setRouteContent('Acme\DemoBundle\Entity\SampleEntity::1');

This will retrieve Acme\DemoBundle\Entity\SampleEntity entity record with id 1.

You can also reference objects using other database fields:

	$route->setRouteContent('Acme\DemoBundle\Entity\SampleEntity:entity_id:1');

Or use the setter method:

	$route->setContent($object); // sets routeContent to 'Path\To\Entity:id:1'

	$route->setContent($object, 'entity_id'); // sets routeContent 'Path\To\Entity:entity_id:1'

To bind a collection use the following syntax:

	$route->setRouteContent('Acme\DemoBundle\Entity\Route:locale(s):en');
	
All Acme\DemoBundle\Entity\Route with locale 'en' will be returned.

To use the setter pass an array and the other parameters:

	$route->setContent($array, $field, $value);


#### Bind routes to other routes:

To obtain this, point the redirecting route to the target one and set the redirection controller (or another one that does the job).
Optionally set 301 status code (default is 302) with 'permanent' property.

	$route = new Route;
    $route->setName('my_route');
    $route->setPath('/path/to/my/route');
	$route->setPermanent(); // this for 301 status
	$route->setController('raindrop_routing.generic_controller:redirectRouteAction');
	$route->setRouteContent('Raindrop\RoutingBundle\Entity\Route:id:2');
	
or

	$route->setContent($anotherRoute); // instead of $route->setRouteContent('…');


#### Bind routes to external uri:

The target entity must implement Raindrop\RoutingBundle\Routing\Base\ExternalRouteInterface methods.

	$externalRoute = new ExternalRoute();
	$externalRoute->setUri('http://www.domain.com/page/');
	$externalRoute->setPermanent(); // this is for 301 status
	
	$route = new Route;
    $route->setName('my_route');
    $route->setPath('/path/to/my/route');
	$route->setController('RaindropRoutingBundle:Default:RedirectRoute');
	$route->setContent($externalRoute);

#### LIMITATIONS:

Routes path have a maximum length of 255 characters.