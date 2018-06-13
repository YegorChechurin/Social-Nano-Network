<?php 
    /**
    * This script listens to events table which contains event ID,
    * event name, and an array of user IDs which are affected/involved
    * by/into this event. Alternative for the 3rd field is to store  
    * a whole json story like message sender id, name etc.
    */

    $request_body = file_get_contents('php://input');
 
    echo $request->method."<br>";
    echo $request->uri."<br>";
    var_dump($request->parsed_uri);
    echo "<br>".$request->query_string."<br>";
    var_dump($_GET);

?>