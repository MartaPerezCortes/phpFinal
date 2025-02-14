<?php

class Vista{
    static public function render($rutaVista, $datos=array()){
        extract($datos);  
        require ($rutaVista); // include($rutaVista);
    }
}

?>