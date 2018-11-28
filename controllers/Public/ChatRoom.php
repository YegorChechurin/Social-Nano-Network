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