<?php /*echo json_encode($datos);  //recibimos los datos con el array de usuario



// Verificar la estructura de $datos
/*echo '<pre>';
print_r($datos);
echo '</pre>';*/
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$permisos_id = array_column($_SESSION['permisos'] ?? [], 'id_permiso');

//$usuario=$datos['usuario'];

    $nombre='';
    $apellido_1='';
    $apellido_2='';
    $sexo='';
    $fecha_Alta=date('Y-m-d');
    $mail='';
    $movil='';
    $login='';
    $activo='';
   
    if (isset($datos['usuario'])){
        extract ($datos['usuario']);// definimos todas la variables y las extraemos--> abajo en el value de cada uno ponemos la variable que hemos creado y que contiene los datos

    }

    //para dejar el sexo marcado, checkeado cuando pongamos los datos    
    $cHombre=$sexo=='H' ? ' checked ': ''; //esto es un if si es igual a H lo marca como checked, si no lo deja vacio
    $cMujer=$sexo=='M' ? ' checked ': '';
    $cOtro=$sexo=='O' ? ' checked ': '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Registro</title>
    
    <!-- Incluye el archivo CSS -->
    <link rel="stylesheet" href="css/app.css">
</head>
<body>
<div class="container-fluid" id="containerLogin">
    <h2>Formulario de Registro</h2>
    <form id="formularioEdicion" class="row gx-4 gy-2 mt-2 needs-validation" name="formularioEdicion" novalidate>
        <input type="hidden" id="id_Usuario" name="id_Usuario" value="<?php echo $id_Usuario ?? ''; ?>">


        <div class="col-12 col-md-6">
            <label for="nombre" class="form-label">Nombre:</label>
            <input type="text" class="form-control form-control-sm" id="nombre" name="nombre" value="<?php echo $nombre; ?>" required>
            <div id="nombreError" class="invalid-feedback" style="display: none;">Por favor ingrese su nombre.</div>
        </div>

        <div class="col-12 col-md-6">
            <label for="primerApellido" class="form-label">Primer apellido:</label>
            <input type="text" class="form-control form-control-sm" id="apellido_1" name="apellido_1" value="<?php echo $apellido_1; ?>" required>
            <div id="apellido1Error" class="invalid-feedback" style="display: none;">Ingrese su primer apellido.</div>
        </div>
        <div class="col-12 col-md-6">
            <label for="apellido_2" class="form-label">Segundo apellido:</label>
            <input type="text" class="form-control form-control-sm" id="apellido_2" name="apellido_2" value="<?php echo $apellido_2; ?>" required>
            <div id="segundoApellidoError" class="invalid-feedback" style="display: none;">Ingrese su segundo apellido.</div>
        </div>

        <div class="col-12 col-md-6">
            <label for="login" class="form-label">Login:</label>
            <input type="text" class="form-control form-control-sm" id="login" name="login" value="<?php echo $login; ?>" required>
            <div id="loginError" class="invalid-feedback" style="display: none;">Ingrese su login.</div>
        </div>

        <div class="col-12 col-md-6">
            <label class="form-label">Género:</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="sexo" value="H" id="sexoH" <?php echo $cHombre; ?> required>
                <label class="form-check-label" for="sexoH">Hombre</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="sexo" value="M" id="sexoM" <?php echo $cMujer; ?> required>
                <label class="form-check-label" for="sexoM">Mujer</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="sexo" value="O" id="sexoO" <?php echo $cOtro; ?> required>
                <label class="form-check-label" for="sexoO">Otro</label>
            </div>
            <div id="generoError" class="invalid-feedback" style="display: none;">Por favor seleccione su género.</div>
        </div>

        <div class="col-12 col-md-6">
            <label for="mail" class="form-label">Correo Electrónico:</label>
            <input type="email" class="form-control form-control-sm" id="mail" name="mail" value="<?php echo $mail; ?>" required>
            <div id="mailError" class="invalid-feedback" style="display: none;">Por favor ingrese un correo válido.</div>
        </div>

        

        <div class="col-12 col-md-6">
            <label for="contrasena" class="form-label">Contraseña:</label>
            <input type="password" class="form-control form-control-sm" id="contrasena" name="contrasena" required>
            <div id="contrasenaError" class="invalid-feedback" style="display: none;">Por favor ingrese una contraseña.</div>
        </div>

        <div class="col-12 col-md-6">
            <label for="movil" class="form-label">Móvil:</label>
            <input type="text" class="form-control form-control-sm" id="movil" name="movil" value="<?php echo $movil; ?>" required>
            <div id="movilError" class="invalid-feedback" style="display: none;">Por favor ingrese su móvil.</div>
        </div>

        <div class="col-12 col-md-6">
            <label for="fecha_Alta" class="form-label">Fecha de Alta:</label>
            <input type="date" class="form-control form-control-sm" id="fecha_Alta" name="fecha_Alta" value="<?php echo $fecha_Alta; ?>" required>
            <div id="fechaAltaError" class="invalid-feedback" style="display: none;">Por favor seleccione la fecha de alta.</div>
        </div>

        <div class="col-12 col-md-6">
            <label for="estado" class="form-label">Estado:</label>
            <select class="form-select form-select-sm" id="estado" name="activo" required>
                <option value="">Seleccione un estado</option>
                <option value="S" <?php if ($activo == 'S') echo ' selected '; ?>>Activo</option>
                <option value="N"<?php if ($activo == 'N') echo ' selected '; ?>>No Activo</option>
            </select>
            <div id="estadoError" class="invalid-feedback" style="display: none;">Por favor seleccione un estado.</div>
        </div>

        <div class="col-12 text-left">
        
            <?php if (in_array(3, $permisos_id) ): ?> 
                <button id="btnNuevo" type="button" onclick="validarFormulario();" class="btn btn-primary btn-sm">Guardar Nuevo</button>
            <?php endif; ?>

            <?php if (in_array(4, $permisos_id)): ?> 
                <button id="btnEditar" type="button" onclick="validarFormularioEditar();" class="btn btn-primary btn-sm">Guardar Editado</button>
            <?php endif; ?>  
                <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('capaEditarCrear').innerHTML='';">Cancelar</button>
       
        </div>
        <div id="mensajeResultado" style="display:none;" class="alert"></div>

    </form>

</div>
<script src="app.js"></script>
</body>
</html>
