<?php

   $endpoints = array(
         array('method'=>'GET', 
            'uri'=>'%public/[0-9]+/messenger%', 
            'controller'=>'Public/chat_room.php'
         ),
         array('method'=>'GET', 
            'uri'=>'%public/test%', 
            'controller'=>'Public/test.php'
         )
   );

   use Skeleton\RequestHandling\Request;
   use Skeleton\RequestHandling\LocalDispatcher;

   require_once '../skeleton/Request_class.php';
   require_once '../skeleton/LocalDispatcher_class.php';
   $request = new Request();
   $router = new LocalDispatcher($request,$endpoints); 
   $router->dispatch();
   
?>