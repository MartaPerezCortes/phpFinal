<?php session_start(); 

// Mostrar errores
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

// Código de inicialización del proyecto
require_once 'C_Frontal.php';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link href="Librerias/bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet" >
        <script src="Librerias/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js" ></script>
        <link href="css/app.css"  rel="stylesheet">
    </head>

    <body>
        <div class="container-fuid" id="capaEncabezado"> 
            <div class="row" id="contenedorArriba">
                <div id="containerlogo"class="col-md-2 col-sm-9 d-none d-sm-block">
                    <img src="iconos/logo2.png">
                    
                </div>
                <div id="nombreTitulo" class="col-md-8 d-none d-md-block">
                    Marta Pérez Cortés
                </div>
                <div id="loginTitulo" class="col-md-2 col-sm-3 d-none d-sm-block">
                    <img id="imgperfil" src="perfil.png" alt="Perfil" width="1vw" height="1vw" style="margin-right: 5px;" onclick="window.location.href='login.php';">
                    <?php echo $_SESSION['login']; ?>
                </div>

            </div>
        </div>
        <div class="container-fluid" id="capaMenu">
            <?php
            require_once 'controladores/C_Menu.php';
            $menuController = new C_Menu();
            $menuController->mostrarMenu();
            ?>
        </div>
       
        <div id="capaContenido">

        </div>
        

       
        <script src="app.js" async></script> 
        
    </body>
</html>