<?php 

    namespace Controllers\Pub;
    use Controllers\Pub\PublicController;
    use Skeleton\RequestHandling\Request;

    class ProfilePage extends PublicController {

        public function get_view_data() {

            $request = new Request();
            $data['user_id'] = $request->uri[2];
            $data['user_name'] = $this->user_tracker->fetch_user_name($data['user_id']);
            $data['last_rec_mes_id'] = $this->messenger->fetch_id_of_last_received_message($data['user_id']);
            $data['friendship_IDs'] = $this->friend_broker->fetch_friendship_IDs($data['user_id']);
            return $data;

        }

    }