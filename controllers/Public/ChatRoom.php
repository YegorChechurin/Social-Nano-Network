<?php 

    namespace Controllers\Pub;
    use Controllers\Pub\PublicController;
    use Skeleton\RequestHandling\Request;
    use Models\UserFactory;
    use Models\ServiceFactory;

    class ChatRoom extends PublicController {

        public function get_view_data(UserFactory $user_factory,ServiceFactory $service_factory) {

            $request = new Request();
            $data['user_id'] = $request->uri[2];
            $user = $user_factory->make_user($data['user_id']);
            $data['user_name'] = $user->showName();
            $messenger = $service_factory->make_service_instance('Messenger');
            $data['last_rec_mes_id'] = $messenger->fetch_id_of_last_received_message($data['user_id']);
            $data['chats'] = $messenger->fetch_user_chats($data['user_id']);;
            $friend_broker = $service_factory->make_service_instance('FriendBroker');
            $data['last_friendship_id'] = $friend_broker->fetch_last_friendship_id($data['user_id']);
            $data['friendship_IDs'] = $friend_broker->fetch_friendship_IDs($data['user_id']);

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