<?php

   $endpoints = array(
         array('method'=>'GET', 
            'uri'=>'%ajax/[0-9]+/messages/[0-9]+%', 
            'controller'=>'Ajax/LongPolling/messages_giver.php'
         ),
         array('method'=>'GET', 
            'uri'=>'%ajax/[0-9]+/chats%', 
            'controller'=>'Ajax/ShortPolling/chats_giver.php'
         ),
         array('method'=>'GET', 
            'uri'=>'%ajax/[0-9]+/chat/[0-9]+%', 
            'controller'=>'Ajax/ShortPolling/chat_fetcher.php'
         ),
         array('method'=>'POST', 
            'uri'=>'%ajax/[0-9]+/messages%', 
            'controller'=>'Ajax/ShortPolling/message_sender.php'
         ),
         array('method'=>'GET', 
            'uri'=>'%ajax/[0-9]+/all_friends%', 
            'controller'=>'Ajax/ShortPolling/all_friends_giver.php'
         ),
         array('method'=>'GET', 
            'uri'=>'%ajax/[0-9]+/friend_removal/[0-9]+%', 
            'controller'=>'Ajax/ShortPolling/friend_remover.php'
         ),
         array('method'=>'GET', 
            'uri'=>'%ajax/[0-9]+/all_users%', 
            'controller'=>'Ajax/ShortPolling/inventory_giver.php'
         ),
         array('method'=>'GET', 
            'uri'=>'%ajax/[0-9]+/friend_addition/[0-9]+(\?.*)?%', 
            'controller'=>'Ajax/ShortPolling/friend_adder.php'
         ),
         array('method'=>'GET', 
            'uri'=>'%ajax/[0-9]+/friends(\?.*)?%', 
            'controller'=>'Ajax/LongPolling/new_friends_giver.php'
         )
   );

   use Skeleton\RequestHandling\Request;
   use Skeleton\RequestHandling\LocalDispatcher;

   $request = new Request();
   $router = new LocalDispatcher($request,$endpoints); 
   $router->dispatch();