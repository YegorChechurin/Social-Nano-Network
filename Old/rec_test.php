<?php
    /* We are checking whether the user is logged in */
    session_start();
    if (empty($_SESSION['logged_in']) or $_SESSION['logged_in'] == 0):
       header('Location: log_in.php'); endif;
    /* Getting id of logged in user */
    $log_user_id = $_SESSION['user_id'];

    $last_rec_js = $_REQUEST['last_rec'];
    require_once('api.php');
    $user = new User($log_user_id);
    /* Getting ID of the last received message by the logged in user */
    $last_rec = $user->getLast();
    if ($last_rec > $last_rec_js) {
        /* Connecting to our database */
        try{
           $dsn = "mysql:dbname=my_sn;host=localhost";
           $user="root";
           $password="";
           $conn = new PDO($dsn,$user,$password);
        }
        catch(PDOException $e){
           echo 'Connection failed: '. $e->getMessage();
        }
        /* Extracting the message from the database */
        $stat = "SELECT * FROM messages WHERE message_id=$last_rec";
        $result = $conn->query($stat);
        $mes_raw = $result->fetchObject();
        $mes = json_encode($mes_raw);
        echo $mes;
    }
	
?>