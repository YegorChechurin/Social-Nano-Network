<?php 

    use Controllers\Ajax\LongPolling\MessagesPoll;
    use Skeleton\RequestHandling\Request;
    use Skeleton\Database\Database;
    use Models\Messenger;

    $request = new Request();
    $db = new Database();
    $messenger = new Messenger($db);

    $messages_poll = new MessagesPoll($request,$messenger);
    $messages_poll->get_query_parameter();
    $start = time();
    $finish = $start + 20;
    $sleeping_interval = 5;
    $messages_poll->poll($start,$finish,$sleeping_interval);