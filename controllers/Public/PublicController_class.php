<?php 

    namespace Controllers\Pub;

    abstract class PublicController {

        abstract public function get_view_data($conn);

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