<?php
require_once 'controladores/Controlador.php'; //carga el fichero si no lo tienes ya cargado
require_once 'modelos/M_Usuarios.php';
require_once 'vistas/Vista.php';

class C_Usuarios extends Controlador{

    private $modelo;

    public function __construct(){
        parent::__construct(); //ejecuta el constructor del padre
        $this->modelo=new M_Usuarios(); //la flecha es lo mismo que poner un punto
    }

    public function getVistaFiltros($datos=array()) { // si no lleva la variable datos, si no llegan datos,  creara una array vacia. si no le pusieramos lo de array querria decir que tiene va a recibir datos obligatoriamente
        Vista::render('vistas/Usuarios/V_Usuarios_Filtros.php');
        
    }
    public function getVistaNuevoEditar($datos=array()) { // si no lleva la variable datos, si no llegan datos,  creara una array vacia. si no le pusieramos lo de array querria decir que tiene va a recibir datos obligatoriamente
        if(!isset($datos['id']) ||$datos['id']==''){ //comprobamos si esta vacia o no existe
            //nuevo, no hay que hacer nada
            Vista::render('vistas/Usuarios/V_Usuarios_NuevoEditar.php');
        }else{

            //editado --> necesito hacer consulta a la BD a través de buscar Usuarios del M_Usuarios
            
            $filtros['id_Usuario']=$datos['id'];

            $usuarios=$this->modelo->buscarUsuarios($filtros); //buscamos el usuario al que corresponde el id
        
         
            Vista::render('vistas/Usuarios/V_Usuarios_NuevoEditar.php', array('usuario'=>$usuarios[0])); //vista que quiero pintar y el array con el usuario encontrado
        }
    }
    
   /* public function getVistaListadoUsuarios($filtros=array()){
        //var_dump($filtros); //sirve para visualizar array, echo solo visualiza texto
        $usuarios=$this->modelo->buscarUsuarios($filtros);
         // Depuración: Log de usuarios obtenidos
        error_log("Usuarios enviados a la vista: " . count($usuarios));
        Vista::render('vistas/Usuarios/V_Usuarios_Listado.php',array('usuarios'=>$usuarios)); //lo definimos como una array por si en el futuro queremos pasarle más informacion que la lista de usuarios, si quisieramos más parametros lo pasamod despues de usuarois
        
    }*/
    public function getVistaListadoUsuarios($filtros = array()) {
        session_start(); // Asegurar que la sesión está iniciada
        $id_Usuario = $_SESSION['id_Usuario'] ?? null;
    
        if (!$id_Usuario) {
            die(json_encode(['error' => 'No autorizado']));
        }
    
        $permisos_id = array_column($_SESSION['permisos'] ?? [], 'id_permiso');
        error_log("📌 Permisos del usuario: " . json_encode($permisos_id));

        // Verificar si tiene permiso para ver la lista de usuarios (Ejemplo: permiso 2)
        if (!in_array(2, $permisos_id) && !in_array(4, $permisos_id)) {
            die(json_encode(['error' => 'No tienes permisos para ver esta información']));
        }
        
    
        $usuarios = $this->modelo->buscarUsuarios($filtros);
        error_log("Usuarios enviados a la vista: " . count($usuarios));
        Vista::render('vistas/Usuarios/V_Usuarios_Listado.php', ['usuarios' => $usuarios]);
    }
    
    /*public function guardarUsuario($datos=array()) {
        error_log("Datos recibidos en guardarUsuario: " . print_r($datos, true));
        $nombre = $datos['nombre'] ?? '';
        $apellido_1 = $datos['apellido_1'] ?? '';
        $apellido_2 = $datos['apellido_2'] ?? '';
        $login = $datos['login'] ?? '';
        $id_Usuario = $datos['id_Usuario'] ?? null;
    
        $respuesta = ['correcto' => 'N', 'msj' => 'Error al guardar el usuario'];
        
        // Verificar si el usuario ya existe en la base de datos
        if ($this->modelo->existeUsuario($nombre, $apellido_1, $apellido_2, $login, $id_Usuario)) {
            $respuesta['msj'] = 'El nombre/apellidos o login ya existen. No se puede crear/editar el usuario.';
        } else {
            // Si no existe, procede a insertar o actualizar el usuario
            if ($id_Usuario) {
                // Actualización
                $resultado=$this->modelo->actualizarUsuario($datos);
                if ($resultado) {
                    $respuesta = ['correcto' => 'S', 'msj' => 'Usuario actualizado correctamente'];
                } else {
                    $respuesta['msj'] = 'Error al actualizar el usuario en la base de datos.';
                }
            } else {
                $id = $this->modelo->insertarUsuario($datos);
            if ($id > 0) {
                // ✅ Asignar el rol visitante (ID = 6)
                $rolAsignado = $this->modelo->asignarRolUsuario($id, 6);
                if ($rolAsignado) {
                    $respuesta = ['correcto' => 'S', 'msj' => 'Usuario creado y rol visitante asignado.'];
                } else {
                    $respuesta = ['correcto' => 'S', 'msj' => 'Usuario creado, pero error al asignar el rol visitante.'];
                }
            } else {
                $respuesta['msj'] = 'Error al crear el usuario.';
            }
            }
        }
        header('Content-Type: application/json');
        // Devuelve la respuesta en formato JSON
        echo json_encode($respuesta);
        exit; // Detiene cualquier salida adicional
    }*/
    public function guardarUsuario($datos=array()) {
        session_start();
    
        $id_Usuario = $_SESSION['id_Usuario'] ?? null;
        if (!$id_Usuario) {
            die(json_encode(['error' => 'No autorizado']));
        }
    
        $permisos_id = array_column($_SESSION['permisos'] ?? [], 'id_permiso');
    
        // 🔐 Verificar permisos
        if (!in_array(3, $permisos_id)) { // Permiso 3: Crear usuario
            die(json_encode(['error' => 'No tienes permisos para crear usuarios']));
        }
    
        error_log("Datos recibidos en guardarUsuario: " . print_r($datos, true));
    
        $nombre = $datos['nombre'] ?? '';
        $apellido_1 = $datos['apellido_1'] ?? '';
        $apellido_2 = $datos['apellido_2'] ?? '';
        $login = $datos['login'] ?? '';
        $id_Usuario = $datos['id_Usuario'] ?? null;
    
        $respuesta = ['correcto' => 'N', 'msj' => 'Error al guardar el usuario'];
    
        if ($this->modelo->existeUsuario($nombre, $apellido_1, $apellido_2, $login, $id_Usuario)) {
            $respuesta['msj'] = 'El nombre/apellidos o login ya existen. No se puede crear/editar el usuario.';
        } else {
            if ($id_Usuario) {
                $resultado = $this->modelo->actualizarUsuario($datos);
                $respuesta = $resultado 
                    ? ['correcto' => 'S', 'msj' => 'Usuario actualizado correctamente']
                    : ['msj' => 'Error al actualizar el usuario en la base de datos.'];
            } else {
                $id = $this->modelo->insertarUsuario($datos);
                if ($id > 0) {
                    $rolAsignado = $this->modelo->asignarRolUsuario($id, 6);
                    $respuesta = $rolAsignado
                        ? ['correcto' => 'S', 'msj' => 'Usuario creado y rol visitante asignado.']
                        : ['correcto' => 'S', 'msj' => 'Usuario creado, pero error al asignar el rol visitante.'];
                } else {
                    $respuesta['msj'] = 'Error al crear el usuario.';
                }
            }
        }
    
        header('Content-Type: application/json');
        echo json_encode($respuesta);
        exit;
    }
    
    

    public function validarUsuario($datos){ //en datos tiene que recibir el usuario y la password

        $id_Usuario=$this->modelo->login($datos); //el modelo nos devuelve un id, lo cogemos y lo guardamos en la variable id_Usuario
        error_log("ID Usuario devuelto: " . print_r($id_Usuario, true)); // Agregar log para depurar
        return $id_Usuario;
    }
    /*public function actualizarEstado($datos) {
        // Respuesta inicial
        $respuesta = ['correcto' => 'N', 'msj' => 'Parámetros faltantes para la actualización'];
        
        // Verifica que los parámetros existan y realiza la actualización
        if (!empty($datos['id_Usuario']) && isset($datos['activo'])) {
            $respuesta['correcto'] = $this->modelo->actualizarEstadoUsuario($datos['id_Usuario'], $datos['activo']) ? 'S' : 'N';
            $respuesta['msj'] = $respuesta['correcto'] === 'S' ? 'Estado actualizado correctamente' : 'Error en la consulta SQL';
        }
    
        // Envía la respuesta en formato JSON
        echo json_encode($respuesta);
    }*/
    public function actualizarEstado($datos) {
        session_start();
    
        $id_Usuario = $_SESSION['id_Usuario'] ?? null;
        if (!$id_Usuario) {
            die(json_encode(['error' => 'No autorizado']));
        }
    
        $permisos_id = array_column($_SESSION['permisos'] ?? [], 'id_permiso');
    
        // Verificamos si tiene permisos 2 o 3 para actualizar el estado
        if (!in_array(2, $permisos_id) && !in_array(3, $permisos_id)) {
            die(json_encode(['error' => 'No tienes permisos para cambiar el estado']));
        }
    
        $respuesta = ['correcto' => 'N', 'msj' => 'Parámetros faltantes para la actualización'];
    
        if (!empty($datos['id_Usuario']) && isset($datos['activo'])) {
            $respuesta['correcto'] = $this->modelo->actualizarEstadoUsuario($datos['id_Usuario'], $datos['activo']) ? 'S' : 'N';
            $respuesta['msj'] = $respuesta['correcto'] === 'S' ? 'Estado actualizado correctamente' : 'Error en la consulta SQL';
        }
    
        echo json_encode($respuesta);
    }
    
    public function obtenerRolesUsuario($id_Usuario) {
        $roles = $this->modelo->obtenerRolesUsuario($id_Usuario);
        
        return is_array($roles) ? $roles : []; // Siempre devuelve un array
    }
    
    
    public function obtenerPermisosUsuario($id_Usuario) {
        $permisos = $this->modelo->obtenerPermisosUsuario($id_Usuario);
        
        return is_array($permisos) ? $permisos : [];
    }
    
    
    public function obtenerPermisosRol($id_Rol) {
        $permisos = $this->modelo->obtenerPermisosRol($id_Rol);
        
        // Asegurar que siempre retorna un array
        return is_array($permisos) ? $permisos : [];
    }
    
}
?>