<?php 

    namespace Controllers\Pub;
    use Models\UserTracker;
    use Models\Messenger;
    use Models\FriendBroker;

    abstract class PublicController {

        protected $user_tracker;

        protected $messenger;

        protected $friend_broker;

        public function __construct(UserTracker $user_tracker, Messenger $messenger, FriendBroker $friend_broker) {
            $this->user_tracker = $user_tracker;
            $this->messenger = $messenger;
            $this->friend_broker = $friend_broker;
        }

        abstract public function get_view_data();

        public function load_view($view_map,$data) {
            $controller = get_class($this);
            foreach ($view_map as $map) {
                if ($map['controller']==$controller) {
                    require $map['view'];
                    break;
                }
            }
        }
    }