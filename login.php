<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'controladores/C_Usuarios.php';

$objCont = new C_Usuarios();
// Si no hay sesión iniciada, asignar usuario invitado y rol visitante
if (!isset($_SESSION['id_Usuario'])) {
    $_SESSION['login'] = 'Usuario Invitado';
    $_SESSION['id_Usuario'] = null; // No tiene ID

    // Asignar rol Visitante (ID = 6, ajústalo según tu BD)
    $_SESSION['roles'] = [['id_rol' => 6, 'rol_descripcion' => 'Visitante']];

    // Obtener los permisos asociados al rol Visitante
    $_SESSION['permisos'] = $objCont->obtenerPermisosRol(6); // ID del rol Visitante
}

// Verificar si el usuario está logueado y actualizar la sesión
if (isset($_SESSION['id_Usuario'])) {
    $_SESSION['roles'] = $objCont->obtenerRolesUsuario($_SESSION['id_Usuario']);
    $_SESSION['permisos'] = $objCont->obtenerPermisosUsuario($_SESSION['id_Usuario']);

    // Obtener permisos de cada rol
    foreach ($_SESSION['roles'] as $rol) {
        $_SESSION['permisos'] = array_merge($_SESSION['permisos'], $objCont->obtenerPermisosRol($rol['id_rol']));
    }

    $_SESSION['permisos'] = array_unique($_SESSION['permisos'], SORT_REGULAR); // Evitar duplicados
    // Extraemos solo los IDs de los permisos y los guardamos en una nueva variable de sesión
    $_SESSION['permisos_id'] = array_column($_SESSION['permisos'], 'id_permiso');

}
// Cerrar sesión
if (isset($_GET['logout'])) {
    session_unset();    // 🔹 Elimina todas las variables de sesión
    session_destroy();  // 🔹 Destruye la sesión completamente
    setcookie(session_name(), '', time() - 3600, '/'); // 🔹 Borra la cookie de sesión
    header("Location: login.php"); // 🔹 Redirige al login
    exit();
}

$usuario = $pass = $msj = '';

// Procesar el login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $usuario = trim($_POST['usuario'] ?? '');
    $pass = trim($_POST['pass'] ?? '');

    if ($usuario === '' || $pass === '') {
        $msj = "⚠️ Debes completar los campos.";
    } else {
        $id_Usuario = $objCont->validarUsuario(array('usuario'=>$usuario, 'pass'=>$pass));

        if ($id_Usuario) {
            session_regenerate_id(true); // 🔹 Genera un nuevo ID de sesión
            $_SESSION['login'] = $usuario;
            $_SESSION['id_Usuario'] = $id_Usuario;
        
            // Obtener roles y permisos
            $_SESSION['roles'] = $objCont->obtenerRolesUsuario($id_Usuario);
            $_SESSION['permisos'] = $objCont->obtenerPermisosUsuario($id_Usuario);
        
            // Obtener permisos de cada rol
            foreach ($_SESSION['roles'] as $rol) {
                $_SESSION['permisos'] = array_merge($_SESSION['permisos'], $objCont->obtenerPermisosRol($rol));
            }
        
            $_SESSION['permisos'] = array_unique($_SESSION['permisos'], SORT_REGULAR); // 🔹 Evitar duplicados
        
            header('Location: login.php'); // Redirige para actualizar la vista
            exit();
                
        } else {
            $msj = "❌ Usuario o contraseña incorrectos.";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="Librerias/bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="Librerias/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    <link href="css/app.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid" id="capaEncabezado">
        <div class="row">
            <div id="containerlogo" class="col-md-2 col-sm-9 d-none d-sm-block">
                <img src="iconos/logo2.png">
            </div>
            <div id="nombreTitulo" class="col-md-8 d-none d-md-block">
                Marta Pérez Cortés
            </div>
            <div id="loginTitulo" class="col-md-2 col-sm-3 d-none d-sm-block">
                <img id="imgperfil" src="perfil.png" alt="Perfil" width="1vw" height="1vw" style="margin-right: 5px;">
            </div>
        </div>
    </div>

    <div id="capaContenido" class="container mt-4">
    <?php if (!isset($_SESSION['id_Usuario']) || $_SESSION['id_Usuario'] === null): ?>
            <!-- 🔹 FORMULARIO LOGIN SI NO ESTÁ LOGUEADO -->
            <form id="formularioLogin" method="post" action="login.php" class="p-4 shadow">
                <h2 class="text-center">Login</h2>

                <?php if (!empty($msj)): ?>
                    <div class="alert alert-warning text-center"><?php echo $msj; ?></div>
                <?php endif; ?>

                <div class="mb-3">
                    <label for="usuario" class="form-label">Usuario</label>
                    <input type="text" class="form-control" id="usuario" name="usuario">
                    <span class="error-message" id="nombreError"></span>
                </div>
                <div class="mb-3">
                    <label for="pass" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="pass" name="pass">
                    <span class="error-message" id="contrasenaError"></span>
                </div>

                <div class="d-flex justify-content-start align-items-center gap-4" style="margin-top: 1rem;">
    
                <button type="submit" class="btn btn-primary px-4 py-2" name="login" onclick="validar();" class="btn btn-primary">Login</button>
    <a href="index.php" style="text-decoration: none; color: #6c757d; font-family: 'Poppins', sans-serif; font-size: 1rem;">
        Volver
    </a>
</div>

            </form>

        <?php else: ?>
            <!-- 🔹 MENSAJE DE BIENVENIDA SI ESTÁ LOGUEADO -->
            <?php
            session_status(); // Asegurar que la sesión está iniciada

            if (!isset($_SESSION['login'])) {
                echo "Acceso denegado. Debes iniciar sesión.";
                exit();
            }
            ?>

            <div style="width:70%; margin:auto;"class="p-4 shadow text-center">
                <h2 style="font-size: 2rem; font-weight: bold;" >Hola, <?php echo htmlspecialchars($_SESSION['login']); ?> 👋</h2>
                <div style="width:90%; margin:auto; margin-left:10%;">
                <!-- Mostrar Roles del Usuario -->
                <h4 style="font-size: 1.2rem; text-align: left;" >🎭 Roles:</h4>
                <ul style="list-style: none; color: #555; padding-left: 2%; width:70%; text-align: left;">
                    <?php
                    if (!empty($_SESSION['roles'])) {
                        foreach ($_SESSION['roles'] as $rol) {
                            echo "<li>{$rol['rol_descripcion']}</li>";
                        }
                    } else {
                        echo "<li>No tiene roles asignados</li>";
                    }
                    ?>
                </ul>

                <!-- Mostrar Permisos del Usuario -->
                <h4 style="font-size: 1.2rem;text-align: left;">🔑 Permisos:</h4>
                <ul style="list-style: none; color: #555;padding-left: 2%; width:90%; text-align: left;">
                    <?php
                    if (!empty($_SESSION['permisos'])) {
                        foreach ($_SESSION['permisos'] as $permiso) {
                            echo "<li>{$permiso['descripcion_permiso']}</li>";
                        }
                    } else {
                        echo "<li>No tiene permisos asignados</li>";
                    }
                    ?>
                </ul>
                </div>
                <!-- Botones Unificados -->
                <div class="d-flex justify-content-center mt-4">
                    <a href="index.php" class="btn btn-primary custom-btn mx-3 px-4">Ir al Inicio</a>
                    <a href="login.php?logout" class="btn btn-danger custom-btn mx-3 px-4">Cerrar Sesión</a>
                </div>
            </div>

        <?php endif; ?>
    </div>

    <script src="app.js" async></script>
</body>
</html>