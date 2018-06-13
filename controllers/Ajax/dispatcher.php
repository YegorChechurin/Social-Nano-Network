<?php

   $endpoints = array(
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
            'uri'=>'%ajax/[0-9]+/messages%', 
            'controller'=>'Ajax/message_sender.php'
         )
   );

   require_once '../models/request_class.php';
   require_once '../models/LocalDispatcher_class.php';
   $request = new Request();
   $router = new LocalDispatcher($request,$endpoints); 
   $router->dispatch();
   
?>