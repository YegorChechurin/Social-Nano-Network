<?php
   //require_once 'controllers/event_listener.php';
   var_dump($_SERVER);
   $endpoint = $_SERVER['REQUEST_URI'];
   if ($endpoint=='/SNN/about') {
       require 'controllers/event_listener.php';
   	   echo $endpoint;
   }
?>