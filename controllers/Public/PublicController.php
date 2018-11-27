<?php 

    namespace Controllers\Pub;
    use Models\UserFactory;
    use Models\ServiceFactory;

    abstract class PublicController {

        abstract public function get_view_data(UserFactory $user_factory,ServiceFactory $service_factory);

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

?>