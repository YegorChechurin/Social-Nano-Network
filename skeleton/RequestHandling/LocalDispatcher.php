<?php

    namespace Skeleton\RequestHandling;
    use Skeleton\RequestHandling\iRouter;

    class LocalDispatcher implements iRouter {

    	private $target_uri;
    	private $method;
    	private $endpoints;

    	function __construct(Request $request,$endpoints) {
    		$this->target_uri = $request->regex_uri;
    		$this->method = $request->method;
    		$this->endpoints = $endpoints;
    	}

    	function dispatch() {
    		foreach ($this->endpoints as $endpoint) {
    			if (preg_match($endpoint['uri'], $this->target_uri) && ($this->method==$endpoint['method'] || $endpoint['method']=='any')) {
    				require $endpoint['controller'];
    				break;
    			}
    		}
    	}

    }