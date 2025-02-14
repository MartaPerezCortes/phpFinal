<?php
require_once 'Modelos/Modelo.php';
require_once 'Modelos/DAO.php';

class M_Menu extends Modelo {
    public $DAO;

    public function __construct() {
        parent::__construct();
        $this->DAO = new DAO();
    }

    // Obtener todas las opciones del menú
    /*public function obtenerMenu() {
        $SQL = "SELECT * FROM menu ORDER BY nivel, id_padre, posicion";
        $result = $this->DAO->consultar($SQL);
        
        if (!is_array($result)) {
            error_log("Error: El resultado de obtenerMenu no es un array. Tipo recibido: " . gettype($result));
        }
        
        return $result;
    }*/

    public function obtenerMenu() {
        $SQL = "SELECT m.*, p.id_permiso, p.cod_permiso 
                FROM menu m
                LEFT JOIN permisos p ON m.id_menu = p.id_menu
                WHERE p.cod_permiso <> 0  -- Excluir permisos inválidos
                ORDER BY m.nivel, m.id_padre, m.posicion";
    
        $result = $this->DAO->consultar($SQL);
    
        if (!is_array($result)) {
            error_log("Error: El resultado de obtenerMenu no es un array. Tipo recibido: " . gettype($result));
        }
    
        return $result;
    }
    public function obtenerMenuVetical() {
        $SQL = "SELECT * FROM menu ORDER BY nivel, id_padre, posicion";
        $result = $this->DAO->consultar($SQL);
        
        if (!is_array($result)) {
            error_log("Error: El resultado de obtenerMenu no es un array. Tipo recibido: " . gettype($result));
        }
        
        return $result;
    }
    
    
    public function obtenerOpcion($id_menu) {
        $SQL = "SELECT * FROM menu WHERE id_menu = $id_menu";
        return $this->DAO->consultar($SQL)[0];
    }
       
    
    public function actualizarOpcion($id_menu, $datos) {
        // Actualiza la URL en la consulta SQL
        $nombre = $datos['nombre'];
        $url = $datos['url'] ?? null;
        $SQL = "UPDATE menu 
                SET nombre = '$nombre', url = " . ($url ? "'$url'" : "NULL") . " 
                WHERE id_menu = $id_menu";
        return $this->DAO->actualizar($SQL);
    }
    
    
    public function insertarOpcion($datos) {
        // La posición ya viene calculada desde el frontend
        $posicion = $datos['posicion'];
        $nivel = $datos['nivel'];
        $id_padre = $datos['id_padre'];
    
        // Desplazar elementos existentes a partir de la nueva posición
        $SQL_ajustar = "UPDATE menu 
                        SET posicion = posicion + 1 
                        WHERE nivel = $nivel 
                        AND " . ($id_padre ? "id_padre = $id_padre" : "id_padre IS NULL") . 
                        " AND posicion >= $posicion";
    
        // Ejecutar ajuste de posiciones
        $this->DAO->actualizar($SQL_ajustar);
    
        // Inserta URL en la consulta SQL
        $url = $datos['url'] ?? null;
        $SQL_insertar = "INSERT INTO menu (nombre, nivel, id_padre, posicion, url) 
                        VALUES ('{$datos['nombre']}', $nivel, " . 
                        ($id_padre ? $id_padre : "NULL") . 
                        ", $posicion, " . ($url ? "'$url'" : "NULL") . ")";
    
        // Log para depuración
        error_log("Consulta SQL de ajuste: $SQL_ajustar");
        error_log("Consulta SQL de inserción: $SQL_insertar");
    
        // Ejecutar inserción
        return $this->DAO->insertar($SQL_insertar);
    }
    
    public function eliminarOpcion($id_menu) {
        // Obtener los submenús asociados al elemento a eliminar
        $submenus = $this->obtenerSubmenus($id_menu);
        foreach ($submenus as $submenu) {
            $this->eliminarOpcion($submenu['id_menu']); // Llamada recursiva para eliminar submenús
        }
    
        // Obtener nivel, posición e ID del padre antes de eliminar
        $SQL_datos = "SELECT nivel, posicion, id_padre FROM menu WHERE id_menu = $id_menu";
        $datos = $this->DAO->consultar($SQL_datos);
    
        if (!empty($datos)) {
            $nivel = $datos[0]['nivel'];
            $posicion = $datos[0]['posicion'];
            $id_padre = $datos[0]['id_padre'];
    
            // Ajustar posiciones de los elementos posteriores
            $SQL_ajustar = "UPDATE menu 
                            SET posicion = posicion - 1 
                            WHERE nivel = $nivel 
                            AND " . ($id_padre ? "id_padre = $id_padre" : "id_padre IS NULL") . 
                            " AND posicion > $posicion";
    
            $this->DAO->actualizar($SQL_ajustar);
        }
    
        // Eliminar la opción actual
        $SQL_eliminar = "DELETE FROM menu WHERE id_menu = $id_menu";
        return $this->DAO->borrar($SQL_eliminar);
    }
/*************  ✨ Codeium Command ⭐  *************/
    /**
     * Retrieve all submenu items associated with a given parent menu ID.
     *
     * @param int $id_padre The ID of the parent menu item for which to retrieve submenus.
     * @return array An array of submenu items associated with the specified parent menu ID.
     */

/******  9c2c9ac8-de10-4c58-8abc-60345596cf9e  *******/
    private function obtenerSubmenus($id_padre) {
        $SQL = "SELECT * FROM menu WHERE id_padre = $id_padre";
        return $this->DAO->consultar($SQL);
    }
    
    /********************PERMISOS********************/
    public function obtenerPermisos($id_menu) {
        $SQL = "SELECT * FROM permisos WHERE id_menu = $id_menu";
        return $this->DAO->consultar($SQL);
    }
    
    /*public function insertarPermiso($datos) {
        $SQL = "INSERT INTO permisos (id_menu, descripcion_permiso, cod_permiso) 
                VALUES ('{$datos['id_menu']}', '{$datos['descripcion_permiso']}', '{$datos['cod_permiso']}')";
        return $this->DAO->insertar($SQL);
    }*/
    public function insertarPermiso($datos) {
        // Insertar el nuevo permiso en la tabla permisos
        $SQL = "INSERT INTO permisos (id_menu, descripcion_permiso, cod_permiso) 
                VALUES ('{$datos['id_menu']}', '{$datos['descripcion_permiso']}', '{$datos['cod_permiso']}')";
        
        $idPermiso = $this->DAO->insertar($SQL);
    
        if ($idPermiso > 0) {
            error_log("✅ Permiso insertado con éxito, ID: $idPermiso");
            
            // Asignar este permiso al rol Administrador (ID 1)
            $SQL_rol = "INSERT INTO permisos_roles (id_rol, id_permiso) VALUES (1, $idPermiso)";
            $this->DAO->insertar($SQL_rol);
            
            error_log("🔹 Permiso ID $idPermiso asignado al rol Administrador (ID 1)");
        } else {
            error_log("❌ Error al insertar el permiso");
        }
    
        return $idPermiso;
    }
    
    
    public function actualizarPermiso($id_permiso, $datos) {
        $SQL = "UPDATE permisos 
                SET descripcion_permiso = '{$datos['descripcion_permiso']}', cod_permiso = '{$datos['cod_permiso']}' 
                WHERE id_permiso = $id_permiso";
        return $this->DAO->actualizar($SQL);
    }
    
    /*public function eliminarPermiso($id_permiso) {
        $SQL = "DELETE FROM permisos WHERE id_permiso = $id_permiso";
        return $this->DAO->borrar($SQL);
    }*/

    public function eliminarPermiso($id_permiso) {
        header('Content-Type: application/json'); // 📌 Asegurar respuesta JSON
    
        $this->DAO->actualizarConParametros("DELETE FROM permisos_usuarios WHERE id_permiso = ?", [$id_permiso]);
        $this->DAO->actualizarConParametros("DELETE FROM permisos_roles WHERE id_permiso = ?", [$id_permiso]);
    
        $SQL_eliminar = "DELETE FROM permisos WHERE id_permiso = ?";
        $resultado = $this->DAO->actualizarConParametros($SQL_eliminar, [$id_permiso]);
    
        if ($resultado) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => true, 'message' => 'Permiso eliminado']);
        }
        exit;
    }
    
     
    
    
    
    public function obtenerPermisosFiltrados($usuario = null, $rol = null) {
        error_log("🔍 [DEBUG] Usuario recibido: " . json_encode($usuario));
        error_log("🔍 [DEBUG] Rol recibido: " . json_encode($rol));
    
        $params = [];
        $types = "";
    
        // **Si estamos filtrando por usuario**
        if (!empty($usuario)) {
            $SQL = "SELECT 
                        p.id_permiso, 
                        p.id_menu, 
                        p.descripcion_permiso, 
                        p.cod_permiso,
                        -- Se asigna 1 si el usuario tiene el permiso directamente o por rol
                        CASE 
                            WHEN pu.id_usuario IS NOT NULL THEN 1 
                            ELSE 0 
                        END AS asignado,
                        -- Se agrupan los roles que otorgan este permiso
                        GROUP_CONCAT(DISTINCT r.rol_descripcion SEPARATOR ', ') AS roles_asignados,
                        -- Se agrupan los roles del usuario
                        GROUP_CONCAT(DISTINCT ru.rol_descripcion SEPARATOR ', ') AS roles_usuario
                    FROM permisos p
                    -- Permisos asignados directamente al usuario
                    LEFT JOIN permisos_usuarios pu ON p.id_permiso = pu.id_permiso AND pu.id_usuario = ?
                    -- Permisos asignados a roles
                    LEFT JOIN permisos_roles pr ON p.id_permiso = pr.id_permiso
                    -- Relación permisos-roles
                    LEFT JOIN roles r ON pr.id_rol = r.id_rol
                    -- Relación usuario → roles
                    LEFT JOIN usuarios_roles ur ON ur.id_usuario = ? 
                    LEFT JOIN roles ru ON ur.id_rol = ru.id_rol
                    GROUP BY p.id_permiso, p.id_menu, p.descripcion_permiso, p.cod_permiso";
    
            $params[] = $usuario;
            $params[] = $usuario;
            $types .= "ii";
        
        // **Si estamos filtrando por rol**
        } elseif (!empty($rol)) {
            $SQL = "SELECT 
                        p.id_permiso, 
                        p.id_menu, 
                        p.descripcion_permiso, 
                        p.cod_permiso,
                        -- Se asigna 1 si el permiso pertenece al rol
                        CASE 
                            WHEN pr.id_rol IS NOT NULL THEN 1 
                            ELSE 0 
                        END AS asignado
                    FROM permisos p
                    -- Permisos asignados a roles
                    LEFT JOIN permisos_roles pr ON p.id_permiso = pr.id_permiso AND pr.id_rol = ?
                    GROUP BY p.id_permiso, p.id_menu, p.descripcion_permiso, p.cod_permiso";
    
            $params[] = $rol;
            $types .= "i";
    
        // **Si no se filtra por usuario ni por rol, traemos todo sin marcar**
        } elseif(!empty($rol)&&!empty($usuario)){
            $SQL = "SELECT 
                        p.id_permiso, 
                        p.id_menu, 
                        p.descripcion_permiso, 
                        p.cod_permiso,
                        -- Se asigna 1 si el usuario tiene el permiso directamente o por rol
                        CASE 
                            WHEN pu.id_usuario IS NOT NULL THEN 1 
                            ELSE 0 
                        END AS asignado,
                        -- Se agrupan los roles que otorgan este permiso
                        GROUP_CONCAT(DISTINCT r.rol_descripcion SEPARATOR ', ') AS roles_asignados,
                        -- Se agrupan los roles del usuario
                        GROUP_CONCAT(DISTINCT ru.rol_descripcion SEPARATOR ', ') AS roles_usuario
                    FROM permisos p
                    -- Permisos asignados directamente al usuario
                    LEFT JOIN permisos_usuarios pu ON p.id_permiso = pu.id_permiso AND pu.id_usuario = ?
                    -- Permisos asignados a roles
                    LEFT JOIN permisos_roles pr ON p.id_permiso = pr.id_permiso
                    -- Relación permisos-roles
                    LEFT JOIN roles r ON pr.id_rol = r.id_rol
                    -- Relación usuario → roles
                    LEFT JOIN usuarios_roles ur ON ur.id_usuario = ? 
                    LEFT JOIN roles ru ON ur.id_rol = ru.id_rol
                    GROUP BY p.id_permiso, p.id_menu, p.descripcion_permiso, p.cod_permiso";
    
            $params[] = $usuario;
            $params[] = $usuario;
            $types .= "ii";

        }else {
            $SQL = "SELECT 
                        p.id_permiso, 
                        p.id_menu, 
                        p.descripcion_permiso, 
                        p.cod_permiso,
                        0 AS asignado
                    FROM permisos p
                    GROUP BY p.id_permiso, p.id_menu, p.descripcion_permiso, p.cod_permiso";
        }
    
        error_log("🔍 [DEBUG] Consulta SQL: " . $SQL);
        error_log("🔍 [DEBUG] Parámetros: " . print_r($params, true));
        error_log("🔍 [DEBUG] Types: " . $types);
    
        // **Ejecutar la consulta**
        if (!empty($params)) {
            $resultado = $this->DAO->consultarConParametros($SQL, $params, $types);
        } else {
            $resultado = $this->DAO->consultar($SQL);
        }
    
        error_log("📌 Permisos obtenidos para usuario {$usuario} o rol {$rol}: " . print_r($resultado, true));
        return $resultado;
    }
    
    

    
    
    
    // Nuevo método para asignar o quitar permisos a usuarios o roles (con filtros)
   
    // Cambiar el orden de los parámetros para que $asignado esté antes de los opcionales
public function asignarPermiso($idPermiso, $asignado, $idUsuario = null, $idRol = null) {
    if ($idUsuario) {
        if ($asignado) {
            // Asignar permiso al usuario
            $SQL = "INSERT INTO permisos_usuarios (id_usuario, id_permiso) 
                    VALUES (?, ?) 
                    ON DUPLICATE KEY UPDATE id_permiso = id_permiso";
        } else {
            // Quitar permiso al usuario
            $SQL = "DELETE FROM permisos_usuarios WHERE id_usuario = ? AND id_permiso = ?";
        }
        return $this->DAO->actualizarConParametros($SQL, [$idUsuario, $idPermiso]);
    } elseif ($idRol) {
        if ($asignado) {
            // Asignar permiso al rol
            $SQL = "INSERT INTO permisos_roles (id_rol, id_permiso) 
                    VALUES (?, ?) 
                    ON DUPLICATE KEY UPDATE id_permiso = id_permiso";
        } else {
            // Quitar permiso al rol
            $SQL = "DELETE FROM permisos_roles WHERE id_rol = ? AND id_permiso = ?";
        }
        return $this->DAO->actualizarConParametros($SQL, [$idRol, $idPermiso]);
    }
    return false;
}


    public function obtenerTodosLosUsuarios() {
        $SQL = "SELECT id_usuario, nombre, apellido_1, apellido_2 FROM usuarios";
        $resultado = $this->DAO->consultar($SQL);
        //error_log("Usuarios cargados: " . print_r($resultado, true));  // Verifica en los logs
        return $resultado;
    }

/***************ROLES*********** */

     // Obtener todos los roles
    public function obtenerTodosLosRoles() {
        $SQL = "SELECT id_rol, rol_descripcion FROM roles";
        $resultado = $this->DAO->consultar($SQL);
        error_log("Roles cargados: " . print_r($resultado, true));  // Verifica en los logs
        return $resultado;
    }

   // Insertar un nuevo rol
    public function insertarRol($datos) {
        $SQL = "INSERT INTO roles (rol_descripcion) VALUES ('{$datos['descripcion']}')";
        return $this->DAO->insertar($SQL);
    }

    // Actualizar un rol existente
    public function actualizarRol($idRol, $datos) {
        $SQL = "UPDATE roles SET rol_descripcion = '{$datos['descripcion']}' WHERE id_rol = {$idRol}";
        return $this->DAO->actualizar($SQL);
    }

    // Eliminar un rol
    /*public function eliminarRol($idRol) {
        $SQL = "DELETE FROM roles WHERE id_rol = {$idRol}";
        return $this->DAO->borrar($SQL);
    }*/
    public function eliminarRol($idRol) {
        error_log("🗑️ Eliminando rol con ID: " . $idRol);
    
        // 🔹 1️⃣ Eliminar permisos asociados al rol antes de eliminarlo
        $SQL_ELIMINAR_PERMISOS = "DELETE FROM permisos_roles WHERE id_rol = {$idRol}";
        $this->DAO->borrar($SQL_ELIMINAR_PERMISOS);
        error_log("✅ Permisos del rol eliminados correctamente.");
    
        // 🔹 2️⃣ Ahora eliminar el rol
        $SQL = "DELETE FROM roles WHERE id_rol = {$idRol}";
        $resultado = $this->DAO->borrar($SQL);
    
        if ($resultado) {
            error_log("✅ Rol eliminado correctamente.");
        } else {
            error_log("❌ Error al eliminar el rol en la base de datos.");
        }
    
        return $resultado;
    }
   
    /***********ROLES ASOCIADOS********** */
    // Obtener todos los roles de un usuario
    public function obtenerRolesUsuario($idUsuario) {
        $SQL = "SELECT id_rol FROM usuarios_roles WHERE id_usuario = ?";
        $result = $this->DAO->consultarConParametros($SQL, [$idUsuario]);
    
        if (!$result) {
            error_log("⚠️ No se encontraron roles para el usuario ID: $idUsuario");
            return [];
        }
        
        error_log("📌 Consulta SQL ejecutada: $SQL con ID usuario = $idUsuario");
        error_log("✅ Roles encontrados para el usuario ID: $idUsuario: " . json_encode($result));
    
        return array_column($result, 'id_rol'); // Devuelve solo los IDs de los roles
    }
    
    // Asignar un rol a un usuario (usando parámetros seguros)
    public function asignarRolUsuario($idUsuario, $idRol) {
        $SQL = "INSERT INTO usuarios_roles (id_usuario, id_rol) VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE id_rol = id_rol";
        
        $resultado = $this->DAO->actualizarConParametros($SQL, [$idUsuario, $idRol]);

        if ($resultado > 0) {
            error_log("✅ Rol ID: $idRol asignado al usuario ID: $idUsuario");
            return true;
        } else {
            error_log("❌ Error al asignar el rol ID: $idRol al usuario ID: $idUsuario");
            return false;
        }
    }

    // Quitar un rol a un usuario (con validación)
    public function quitarRolUsuario($idUsuario, $idRol) {
        $SQL = "DELETE FROM usuarios_roles WHERE id_usuario = ? AND id_rol = ?";
        
        $resultado = $this->DAO->actualizarConParametros($SQL, [$idUsuario, $idRol]);

        if ($resultado > 0) {
            error_log("✅ Rol ID: $idRol eliminado del usuario ID: $idUsuario");
            return true;
        } else {
            error_log("⚠️ No se pudo eliminar el rol ID: $idRol del usuario ID: $idUsuario (posiblemente no existía)");
            return false;
        }
    }

    


}

