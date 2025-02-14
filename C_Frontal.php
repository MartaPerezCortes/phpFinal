<?php

    //variables predefinidas $_PODT, $__GET. son arrayas. en este caso las queremos juntar, asi sea como sea la peticion me van a llegar todos los datos
    $getPost=array_merge($_GET,$_POST,$_FILES);
    // Temporalmente para depuración
    error_log("Datos recibidos en C_Frontal: " . json_encode($getPost));

    //verificamos que nos llega en controlador y el metodo
    if(isset($getPost['controlador']) && $getPost['controlador']!=''){ // buscamos si existe la variable controlador dentro de getpost y si no esta vacio
        //recibido controlador
        $controlador='C_'.$getPost['controlador']; //cojo el nombre que me ha pasado y lo concatenido, luego miro si existe
        if(file_exists('controladores/'.$controlador.'.php')){
            //existe el fichero de controlador
            
            //ahora miramos si esta el metodo

            $metodo=$getPost['metodo'];
            require_once './controladores/'.$controlador.'.php';
            $objetoControlador=new $controlador(); //nueva instancia del objeto que tengo en la variable controlador
            //¿tengo metodo para este controlador?
            if(method_exists($objetoControlador, $metodo)){ //le pasamos el objeto y el metodo
                //ejecuta el metodo
                $objetoControlador->$metodo($getPost);
            }else{
                echo 'Error CF-03'; //no existe el metodo
            }
        }else{
            echo 'Error CF-02'; //no existe el fichero de controlador
        }

    }/*else{
        //no recibido el controlador
        echo 'Error CF-01'; // error controlador frontal 01
    }*/
    /* ESTO LO HEMOS CAMBIADO POR LO DE ARRIBA
    require_once './controladores/C_Usuarios.php'; // le indico donde esta lo que necesito

    //instanciamos la clase y creamos el objeto. **Creamos un objeto de la clase usuario.
    $objetoControlador=new C_Usuarios();

    //llamamos al metodo (lo que en java sería controlador.getPrueba())
    $objetoControlador->getPrueba();*/
    
    ?>