<?php
    function route($needle,$map,$check_key,$return_key) {
    	foreach ($map as $m) {
            if ($m[$check_key]==$needle) {
                require $m[$return_key];
                break;
            }
        }
    }
?>