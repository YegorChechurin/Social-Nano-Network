<?php 

    namespace Controllers\Pub;
    use Controllers\Pub\PublicController;
    use Skeleton\RequestHandling\Request;

    class ChatRoom extends PublicController {

        public function get_view_data() {

            $request = new Request();
            $data['user_id'] = $request->uri[2];
            $data['user_name'] = $this->user_tracker->fetch_user_name($data['user_id']);
            $data['last_rec_mes_id'] = $this->messenger->fetch_id_of_last_received_message($data['user_id']);
            $data['chats'] = $this->messenger->fetch_user_chats($data['user_id']);;
            $data['friendship_IDs'] = $this->friend_broker->fetch_friendship_IDs($data['user_id']);

            if ($request->query_string) {
                parse_str($request->query_string,$partner);
                $data['active_id'] = $partner['id'];
                $data['active_name'] = $partner['name'];
            } else {
                $data['active_id'] = 0;
                $data['active_name'] = '';
            }

            return $data;

        }

    }