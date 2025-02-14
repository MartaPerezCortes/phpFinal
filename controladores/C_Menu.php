<?php

// Mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'controladores/Controlador.php';
require_once 'Modelos/M_Menu.php';
require_once 'vistas/Vista.php';




class C_Menu extends Controlador {
    private $modelo;

    public function __construct() {
        parent::__construct();
        $this->modelo = new M_Menu();
    }

    // Obtener y renderizar el menú
    public function mostrarMenu() {
        $menu = $this->modelo->obtenerMenu(); // Llama al modelo para obtener los datos del menú
        Vista::render('vistas/V_Menu.php', array('menu' => $menu)); // Pasa los datos a la vista
    }
    public function mostrarMenuFiltros() {
        $usuarios = $this->modelo->obtenerTodosLosUsuarios();
        $roles = $this->modelo->obtenerTodosLosRoles();
    
        Vista::render('vistas/V_MenuFiltros.php', [
            'usuarios' => $usuarios,
            'roles' => $roles
        ]);
    }
    public function obtenerVistaMenuPermisos() {
        // Cargar usuarios y roles para los filtros
        $usuarios = $this->modelo->obtenerTodosLosUsuarios();
        $roles = $this->modelo->obtenerTodosLosRoles();
        
            Vista::render('vistas/V_MenuFiltros.php', [
                'usuarios' => $usuarios,
                'roles' => $roles,
                'filtro_aplicado' => true
            ]);
        
    }
    

    public function mostrarMenuVertical() {
        $usuario = $_GET['usuario'] ?? null;
        $rol = $_GET['rol'] ?? null;
    
        $menu = $this->modelo->obtenerMenuVetical();
    
        if ($usuario || $rol) {
            $permisos = $this->modelo->obtenerPermisosFiltrados($usuario, $rol);
            error_log("Permisos filtrados para Usuario: {$usuario}, Rol: {$rol}: " . print_r($permisos, true));
    
            Vista::render('vistas/V_Menu_Vertical.php', [
                'menu' => $menu,
                'permisos' => $permisos,
                'filtro_aplicado' => true
            ]);
        } else {
            Vista::render('vistas/V_Menu_Vertical.php', [
                'menu' => $menu,
                'filtro_aplicado' => false
            ]);
        }
    }
    
    
  /*  public function mostrarMenuVertical() {
        $usuarios = [['id_usuario' => 1, 'nombre' => 'Prueba Usuario']];
        $roles = [['id_rol' => 1, 'rol_descripcion' => 'Prueba Rol']];
    
        // Verificar si los datos funcionan de forma estática
        Vista::render('vistas/V_Menu_Vertical.php', [
            'usuarios' => $usuarios,
            'roles' => $roles
        ]);
    }*/
    
    
    public function obtenerOpcion($datos) {
        $id_menu = $datos['id_menu'];
        $opcion = $this->modelo->obtenerOpcion($id_menu);
       // echo json_encode($opcion); // Retornar la opción como JSON
       echo json_encode([
        'id_menu' => $opcion['id_menu'],
        'id_padre' => $opcion['id_padre'],
        'posicion' => $opcion['posicion'],
        'nivel' => $opcion['nivel'],
        'nombre' => $opcion['nombre'],
        'url' => $opcion['url'] // Incluye la URL
    ]);
    }
   
    public function guardarOpcion($datos) {
        try {
            // Validar y normalizar los datos recibidos
            $datos['id_menu'] = !empty($datos['id_menu']) ? $datos['id_menu'] : null;
            $datos['id_padre'] = ($datos['id_padre'] === 'null' || $datos['id_padre'] === null || $datos['id_padre'] === '') ? null : (int) $datos['id_padre'];
            $datos['posicion'] = !empty($datos['posicion']) && is_numeric($datos['posicion']) ? (int) $datos['posicion'] : null;
            $datos['nivel'] = !empty($datos['nivel']) ? (int) $datos['nivel'] : 1;
            $datos['nombre'] = !empty($datos['nombre']) ? $datos['nombre'] : null;
            $datos['url'] = !empty($datos['url']) ? $datos['url'] : null; // Validar y normalizar URL

            // Log para depuración
            error_log("Datos normalizados: " . print_r($datos, true));
    
            // Verificar campos obligatorios
            if (empty($datos['nombre']) || $datos['posicion'] === null) {
                throw new Exception("Faltan valores obligatorios: 'nombre' o 'posicion'.");
            }
    
            // Insertar o actualizar según sea el caso
            $resultado = false;
            if (!empty($datos['id_menu'])) {
                // Si hay un ID, se trata de una actualización
                $resultado = $this->modelo->actualizarOpcion($datos['id_menu'], $datos);
            } else {
                // Si no hay ID, se trata de una inserción
                $resultado = $this->modelo->insertarOpcion($datos);
            }
    
            echo json_encode(['success' => $resultado]);
        } catch (Exception $e) {
            error_log("Error al guardar opción: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'datos' => $datos
            ]);
        }
    }
    
    
  
    
    
    public function eliminarOpcion($datos) {
        $id_menu = $datos['id_menu'];
        $resultado = $this->modelo->eliminarOpcion($id_menu);
        echo json_encode(['success' => $resultado]);
    }
    
    
    
    /********************PERMISOS********************/

    /*  public function obtenerPermisos($datos) {
        $id_menu = $datos['id_menu'];
        $permisos = $this->modelo->obtenerPermisos($id_menu);
        echo json_encode($permisos);
    }*/

    public function obtenerPermisos($datos) {
        $id_menu = $datos['id_menu'];
        $usuario = $_GET['usuario'] ?? null;
        $rol = $_GET['rol'] ?? null;

        // Si hay un filtro de usuario o rol, obtener permisos filtrados
        if ($usuario || $rol) {
            $permisos = $this->modelo->obtenerPermisosFiltrados($id_menu, $usuario, $rol);
        } else {
            // Si no hay filtros, obtener todos los permisos normalmente
            $permisos = $this->modelo->obtenerPermisos($id_menu);
        }
        
        echo json_encode($permisos);
    }


    public function guardarPermiso($datos) {
        $resultado = false;
        if (!empty($datos['id_permiso'])) {
            $resultado = $this->modelo->actualizarPermiso($datos['id_permiso'], $datos);
        } else {
            $resultado = $this->modelo->insertarPermiso($datos);
        }

        echo json_encode(['success' => $resultado]);
    }

    /*public function eliminarPermiso($datos) {
        $id_permiso = $datos['id_permiso'];
        $resultado = $this->modelo->eliminarPermiso($id_permiso);
       
        if ($resultado) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar el permiso']);
        }
    }*/

    public function eliminarPermiso($datos) {
        header('Content-Type: application/json'); // 📌 Asegurar respuesta JSON
        $id_permiso = $datos['id_permiso'];
    
        error_log("📌 Intentando eliminar permiso con ID: $id_permiso");
    
        // Eliminar el permiso en el modelo
        $resultado = $this->modelo->eliminarPermiso($id_permiso);
    
        if ($resultado) {
            error_log("✅ Permiso ID $id_permiso eliminado correctamente.");
            echo json_encode(['success' => true]);
        } else {
            error_log("❌ Error al eliminar permiso ID $id_permiso.");
            echo json_encode(['success' => false, 'message' => 'Error al eliminar el permiso.']);
        }
    
        exit; // 🚨 Evita que el servidor envíe más contenido después de la respuesta JSON
    }
    

   

    public function actualizarPermiso() {
        $idPermiso = $_POST['id_permiso'];
        $asignado = $_POST['asignado'];
        $idUsuario = $_POST['id_usuario'] ?? null;
        $idRol = $_POST['id_rol'] ?? null;

        // Cambiar el orden en la llamada al modelo
        $resultado = $this->modelo->asignarPermiso($idPermiso, $asignado, $idUsuario, $idRol);

        echo json_encode(['success' => $resultado]);
    }


/***************ROLES*********** */
    public function obtenerTodosLosRoles() {
         $idUsuario = $_GET['usuario'] ?? null; // O selecciona un usuario específico para la prueba
    if ($idUsuario) {
        $rolesAsignados = $this->modelo->obtenerRolesUsuario($idUsuario);
        error_log("📌 Roles asignados al usuario ID $idUsuario: " . json_encode($rolesAsignados));
    }
        $roles = $this->modelo->obtenerTodosLosRoles();
        error_log("🔎 Roles enviados: " . print_r($roles, true));
        echo json_encode($roles);
    }

    public function guardarRol() {
        $descripcion = $_POST['descripcion'] ?? null;
        $idRol = $_POST['id_rol'] ?? null;

        if (!$descripcion) {
            echo json_encode(['success' => false, 'message' => '⚠️ La descripción del rol es obligatoria.']);
            return;
        }

        $datos = [
            'descripcion' => $descripcion,
        ];

        if ($idRol) {
            $resultado = $this->modelo->actualizarRol($idRol, $datos);
        } else {
            $resultado = $this->modelo->insertarRol($datos);
        }

        echo json_encode(['success' => $resultado]);
    }

    /*public function eliminarRol() {
        $idRol = $_POST['id_rol'] ?? null;
        
        if (!$idRol) {
            echo json_encode(['success' => false, 'message' => '⚠️ ID del rol no válido.']);
            return;
        }

        $resultado = $this->modelo->eliminarRol($idRol);
        echo json_encode(['success' => $resultado]);
    }*/
    public function eliminarRol() {
        error_log("📥 Se recibió solicitud para eliminar un rol.");
    
        // Asegurar que el ID del rol se está recibiendo
        $idRol = $_POST['id_rol'] ?? null;
    
        if (!$idRol) {
            error_log("❌ No se recibió un ID de rol válido.");
            echo json_encode(['success' => false, 'message' => '⚠️ ID del rol no válido.']);
            return;
        }
    
        error_log("🗑️ Eliminando permisos del rol con ID: $idRol");
        
        // Llamamos al modelo para eliminar los permisos y el rol
        $resultado = $this->modelo->eliminarRol($idRol);
    
        if ($resultado) {
            error_log("✅ Rol y permisos eliminados con éxito.");
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => true, 'message' => 'Permiso eliminado']);
            
        }
    }
    /*************ROLES ASOCIADOS*************** */
        // Obtener roles asignados a un usuario
    public function obtenerRolesUsuario() {
        $idUsuario = $_GET['id_usuario'] ?? null;

        if (!$idUsuario) {
            echo json_encode([]);
            return;
        }

        $roles = $this->modelo->obtenerRolesUsuario($idUsuario);
        echo json_encode($roles);
    }

    // Asignar un rol a un usuario
    public function asignarRolUsuario() {
        $idUsuario = $_POST['id_usuario'] ?? null;
        $idRol = $_POST['id_rol'] ?? null;

        if (!$idUsuario || !$idRol) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
            return;
        }

        $resultado = $this->modelo->asignarRolUsuario($idUsuario, $idRol);
        echo json_encode(['success' => $resultado]);
    }

    // Quitar un rol a un usuario
    public function quitarRolUsuario() {
        $idUsuario = $_POST['id_usuario'] ?? null;
        $idRol = $_POST['id_rol'] ?? null;

        if (!$idUsuario || !$idRol) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
            return;
        }

        $resultado = $this->modelo->quitarRolUsuario($idUsuario, $idRol);
        echo json_encode(['success' => $resultado]);
    }
    
    private function actualizarSesionUsuario($id_Usuario) {
        // Obtener los nuevos roles y permisos del usuario
        $roles = $this->modelo->obtenerRolesUsuario($id_Usuario);
        $permisos = $this->modelo->obtenerPermisosFiltrados($id_Usuario);
    
        // Guardar en sesión
        $_SESSION['roles'] = array_column($roles, 'id_rol'); // Solo IDs de roles
        $_SESSION['permisos'] = array_column($permisos, 'id_permiso'); // Solo IDs de permisos
    
        error_log("🔄 Sesión actualizada para usuario $id_Usuario. Roles: " . json_encode($_SESSION['roles']) . " | Permisos: " . json_encode($_SESSION['permisos']));
    }

}
?>
