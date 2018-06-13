<?php 
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');

    class RequestHandler {
    	public function set_communication_method($method) {
    		if (!empty($method)) {
    			$_SESSION['communication_type'] = $method;
    			echo $_SESSION['communication_type'];
    		}
    	}
    }

    // $communication_type = $_REQUEST['communication_type'];
    // $rh = new RequestHandler();
    // (new RequestHandler())->set_communication_method($communication_type);
    // flush();

    require_once 'db_con.php';

    $query = "SELECT * FROM friends";
    $result = $conn->query($query);
    $fr = $result->fetchAll(PDO::FETCH_ASSOC);
    if ($fr) {
        $data = json_encode($fr);
        echo "data: {$data}\n\n";
    } else {
        //$array = ['message'=>'No friendships yet!'];
        $array = 'No friendships yet!';
        $data = json_encode($array);
        echo "data: {$data}\n\n";
        //header('Content-Type: text/html');
        while (1) {
            echo "event: ping\n";
            $curDate = date(DATE_ISO8601);
            echo 'data: {"time": "' . $curDate . '"}';
            echo "\n\n";
            sleep(1);
        }
    }

?>