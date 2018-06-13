<?php

   $routes = array(
   	   array('uri'=>'public', 
   	   	'controller'=>'Public/dispatcher.php'
   	   ),
   	   array('uri'=>'ajax', 
   	   	'controller'=>'Ajax/dispatcher.php'
   	   )
   );
   
   require_once '../models/request_class.php';
   require_once '../models/Gateway_class.php';
   $request = new Request();
   $router = new Gateway($request,$routes);
   $router->dispatch();

   /*$endpoints = array(
         array('method'=>'GET', 
            'uri'=>'%public/[0-9]+/messenger%', 
            'controller'=>'Public/chat_room.php'
         ),
         array('method'=>'GET', 
            'uri'=>'%public/test%', 
            'controller'=>'Public/test.php'
         ),
         array('method'=>'GET', 
            'uri'=>'%ajax/[0-9]+/messages/[0-9]%', 
            'controller'=>'Ajax/messages_giver.php'
         ),
         array('method'=>'GET', 
            'uri'=>'%ajax/[0-9]+/chats%', 
            'controller'=>'Ajax/chats_giver.php'
         ),
         array('method'=>'GET', 
            'uri'=>'%ajax/[0-9]+/chat/[0-9]%', 
            'controller'=>'Ajax/chat_fetcher.php'
         ),
         array('method'=>'POST', 
            'uri'=>'%ajax/[0-9]+/messages', 
            'controller'=>'Ajax/message_sender.php'
         ),
         array('method'=>'GET', 
            'uri'=>'%chat_room.js%', 
            'controller'=>'Public/chat_room.js'
         )
   );

   require_once '../models/LocalDispatcher_class.php';
   $router = new LocalDispatcher($request,$endpoints); 
   $router->dispatch();*/

   //https://stackoverflow.com/questions/2363511/relative-vs-absolute-urls-in-jquery-ajax-requests?utm_medium=organic&utm_source=google_rich_qa&utm_campaign=google_rich_qa
?>