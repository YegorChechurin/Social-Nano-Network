<?php

    namespace Skeleton\RequestHandling;
    use Skeleton\RequestHandling\iRouter;

    require_once 'Router_interface.php';

    class Gateway implements iRouter {

    	private $target_uri;
    	private $routes;

    	function __construct(Request $request,$routes) {
    		$this->target_uri = $request->uri[1];
    		$this->routes = $routes;
    	}

    	function dispatch() {
    		foreach ($this->routes as $route) {
    			if ($this->target_uri==$route['uri']) {
    				require $route['controller'];
    				break;
    			}
    		}
    	}

    }
?>