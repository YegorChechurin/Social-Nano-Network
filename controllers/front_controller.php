<?php

   require '../vendor/autoload.php';

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
   
   $request = new Request();
   $router = new Gateway($request,$routes);
   $router->dispatch();