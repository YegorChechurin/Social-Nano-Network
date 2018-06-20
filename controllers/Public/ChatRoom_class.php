<?php 

    namespace Controllers\Pub;
    use Controllers\Pub\PublicController;

    require_once 'PublicController_class.php';
    require_once '../models/db_con.php';
    require_once '../models/user_class.php';
    require_once '../models/request_class.php';

    class ChatRoom extends PublicController {

        public function get_view_data($conn) {

            $request = new Request();
            $data['user_id'] = $request->uri[2];
            $user = new User($data['user_id'],$conn);
            $data['user_name'] = $user->showName();
            $data['last_rec_mes_id'] = $user->getLast();

            if ($request->POST) {
                $data['active_id'] = $request->POST['fr_id'];
                $data['active_name'] = $request->POST['fr_name'];
            } else {
                $data['active_id'] = 0;
                $data['active_name'] = '';
            }

            return $data;

        }

    }

?>