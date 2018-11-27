<?php
    
    namespace Skeleton\RequestHandling;
    
    class Request {
    	public $method;
    	public $raw_uri;
    	public $uri = array();
    	public $regex_uri;
    	public $query_string;
        public $GET = array(); 
        public $POST = array();

    	public function __construct() {
    		$this->raw_uri = $_SERVER['REQUEST_URI'];
    		$this->method = $_SERVER['REQUEST_METHOD'];
    		$this->query_string = $_SERVER['QUERY_STRING'];
    		$this->uri = $this->parse_uri($this->raw_uri,$this->query_string);
    		$this->regex_uri = str_replace('/SNN/', '', $this->raw_uri);
            $this->GET = $_GET;
            $this->POST = $_POST;
    	}

    	private function parse_uri($uri,$query_string) {
    		$raw_parsed_uri = str_replace('?'.$query_string, '', $uri);
    		$parsed_uri = explode('/', trim($raw_parsed_uri,'/'));
    		return $parsed_uri;
    	}
    }

?>