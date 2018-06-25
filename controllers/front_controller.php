<?php

   $routes = array(
   	   array('uri'=>'public', 
   	   	'controller'=>'Public/dispatcher.php'
   	   ),
   	   array('uri'=>'ajax', 
   	   	'controller'=>'Ajax/dispatcher.php'
   	   )
   );

   use Skeleton\RequestHandling\Request;
   use Skeleton\RequestHandling\Gateway;
   
   require_once '../skeleton/Request_class.php';
   require_once '../skeleton/Gateway_class.php';
   $request = new Request();
   $router = new Gateway($request,$routes);
   $router->dispatch();

?>