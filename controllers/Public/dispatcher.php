<?php

   $endpoints = array(
         array('method'=>'GET', 
            'uri'=>'%public/[0-9]+/messenger(\?.*)?%', 
            'controller'=>'Public/chat_room.php'
         ),
         array('method'=>'GET', 
            'uri'=>'%public/[0-9]+/profile%', 
            'controller'=>'Public/profile_page.php'
         )
   );

   use Skeleton\RequestHandling\Request;
   use Skeleton\RequestHandling\LocalDispatcher;

   $request = new Request();
   $router = new LocalDispatcher($request,$endpoints); 
   $router->dispatch();
   
?>