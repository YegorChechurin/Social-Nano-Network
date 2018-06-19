<?php 

    abstract class PublicController {

        abstract public function get_request_info();

        public function load_view() {
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