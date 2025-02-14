<?php
require_once 'Modelos/Modelo.php';
require_once 'Modelos/DAO.php';
class M_Usuarios extends Modelo{
    public $DAO;

    public function __construct(){
        parent::__construct(); // ejecutar constructor padre
        $this->DAO = new DAO();
    }
 
    public function buscarUsuarios($filtros=array()){
        
        $ftexto=''; //esto viene de V_Usuarios_Filtros
        $factivo='';//esto viene de V_Usuarios_Filtros
        $id_Usuario='';
        extract($filtros);
        
        $SQL="SELECT * FROM USUARIOS WHERE 1=1"; //creamos la variable sql para la consulta de los usuarios PONEMOS 1=1 PARA PODER SEGUIR CONCATENANDOLA ABAJO
        

       /* if ($ftexto != '') {

            $aPalabras=explode(' ', $ftexto); // una array de palabras en la que cada posicion tiene una de las palabras que le vienen en texto
            
            $SQL .= " AND (nombre LIKE '%$ftexto%' 
                            OR apellido_1 LIKE '%$ftexto%'
                            OR apellido_2 LIKE '%$ftexto%' )"; //el punto añade, es como el +
        }*/
       
        
        if ($ftexto != '') {
            $aPalabras = explode(' ', $ftexto); // Separamos el texto en palabras
            
            // Iniciamos la construcción de la consulta con un caso imposible "1=2" para que las siguientes condiciones sean las válidas
            $SQL .= ' AND ( 1=2 ';
            /*1=2 es una condición que siempre es falsa. Esto se hace para que luego las condiciones que se añadan sean las únicas que pueden hacer la consulta verdadera. 
            Si no hubiera 1=2, tendrías que hacer otra lógica para empezar la construcción de las condiciones. 
            Es como decir "No me des ningún resultado a menos que se cumplan las siguientes condiciones".*/
            
            // Creamos las condiciones para cada palabra
            foreach ($aPalabras as $palabra) {
                
                // Añadimos condiciones para que la palabra se busque en nombre, apellido_1, apellido_2, mail y login
                $SQL.= "OR (nombre LIKE '%$palabra%' 
                                OR apellido_1 LIKE '%$palabra%' 
                                OR apellido_2 LIKE '%$palabra%' 
                                OR mail LIKE '%$palabra%' 
                                OR login LIKE '%$palabra%')";
            }
            
            // Cerramos la condición general
            $SQL .= ' ) ';
            
        }
        
        /*************** O ASÍ **********************/
        /*
        /*
        if ($ftexto != '') {
            $aPalabras = explode(' ', $ftexto); // Separamos el texto en palabras
            
            // Comenzamos con la parte de la consulta
            $SQL .= " AND (";
            
            // Recorremos cada palabra para generar las condiciones
            $condiciones = array();
            foreach ($aPalabras as $palabra) {
                // Generamos condiciones LIKE para cada palabra en cada uno de los campos (nombre, apellido_1, apellido_2, apellido_01)
                $condiciones[] = "(nombre LIKE '%$palabra%' 
                                  OR apellido_1 LIKE '%$palabra%' 
                                  OR apellido_2 LIKE '%$palabra%')";
            }
            
            // Unimos las condiciones con AND para que todas las palabras se busquen en cualquier campo
            $SQL .= implode(' OR ', $condiciones);
            
            // Cerramos la condición general
            $SQL .= ")";

            echo($SQL);
        }*/
      
        if($factivo!=''){
            $SQL.=" AND activo='$factivo' ";
        }

        if($id_Usuario!=''){ // para editar usuario buscamos el id del usuario
            $SQL.=" AND id_Usuario='$id_Usuario'";
        }

        $SQL.=' ORDER BY apellido_1, apellido_2, nombre, login ';
        
        
        // Depuración: Log de la consulta SQL generada
        error_log("SQL Generada: $SQL");

        $usuarios= $this->DAO->consultar($SQL); //del objeto DAO cogemos la funcion consultar() y le pasamos al SQL


        // Depuración: Log de la cantidad de usuarios encontrados
        error_log("Usuarios encontrados: " . count($usuarios));

        return $usuarios;
    }

    public function insertarUsuario($datos=array()){
        error_log("📌 Datos recibidos en insertarUsuario: " . print_r($datos, true));
    
        // Verificar si la clave 'contrasena' existe en los datos
        if (!isset($datos['contrasena']) || empty($datos['contrasena'])) {
            error_log("⚠️ ERROR: La contraseña está vacía antes de encriptar.");
            return false; // Evita la inserción si la contraseña está vacía
        }
    
        $contrasena = $datos['contrasena'];
    
        error_log("🔑 Contraseña antes de encriptar: '$contrasena'");
    
        // Encriptar la contraseña con MD5
        $pass = MD5($contrasena);
    
        error_log("🔐 Contraseña encriptada para usuario {$datos['login']}: $pass");
    
        $SQL = "INSERT INTO usuarios SET
            nombre = '{$datos['nombre']}',
            apellido_1 = '{$datos['apellido_1']}',
            apellido_2 = '{$datos['apellido_2']}',
            sexo = '{$datos['sexo']}',
            fecha_Alta = '{$datos['fecha_Alta']}',
            mail = '{$datos['mail']}',
            movil = '{$datos['movil']}',
            login = '{$datos['login']}',
            pass = '$pass',
            activo = '{$datos['activo']}'";
    
        error_log("📄 SQL a ejecutar: " . $SQL);
    
        return $this->DAO->insertar($SQL);
    }
    

    public function login($datos=array()){
        $usuario='ffffffffffff'; //se ponen cosas por no ponerlo a vacio por si hay algun regristo vacio en la bd, ais no coincide con ningun dato de la bd
        $pass='ffffffffffffff';

        extract($datos);

        $usuario=addslashes($usuario); // añade una barra para que no puedan poner 1=1 en el campo usuario y contraseña, ya que haria login y con la barra ya no funciona. Esto pasa porque 1=1 siempre es cierto, entonce sis t elo meten ais y te hacen login. al poner una barra la sql seria erronea

        $SQL="SELECT * FROM usuarios WHERE login='$usuario' && pass=MD5('$pass')"; //MD5 porque está encriptada la contraseña
        
        $usuarios= $this->DAO->consultar($SQL); //del objeto DAO cogemos la funcion consultar() y le pasamos al SQL, metemos el usuario en la array usuarios
        $id_Usuario='';

        if(empty($usuarios)){
            //no encontrado, no devolvemos nada
        }else{
            //encontrado

            //nos lo guardamos en la sesion
            $_SESSION['login']=$usuario; //creamos al variable sesion para guardar al usuario una vez inicie sesion
            $_SESSION['usuario']=$usuarios[0]['nombre']; // cogemos el nombre del usuario que esta en la posicion 0 del array usuarios porque solo hemos traido un usuario en la consulta
            $_SESSION['id_Usuario']=$usuarios[0]['id_usuario'];
            $id_Usuario=$usuarios[0]['id_Usuario']; //guardamos id usuario tambien en el objeto id_Usuario y así lo devolvemos al al hacer login
            
        }
        error_log("SQL ejecutada: " . $SQL);
        error_log("Resultado de consulta: " . print_r($usuarios, true));
        return $id_Usuario;
    
    }
    public function actualizarEstadoUsuario($id_Usuario, $activo) {
        $SQL = "UPDATE usuarios SET activo = '$activo' WHERE id_Usuario = '$id_Usuario'";
        return $this->DAO->ejecutar($SQL); // Llama al método ejecutar en DAO para realizar la actualización
    }
    public function existeUsuario($nombre, $apellido_1, $apellido_2, $login, $id_Usuario = null) {
        //echo "Valor de id_Usuario recibido: " . ($id_Usuario ?? 'null') . "<br>";
        // Crear una consulta para verificar si ya existen el nombre, apellidos y login
        $SQL = "SELECT * FROM usuarios WHERE (nombre = '$nombre' AND apellido_1 = '$apellido_1' AND apellido_2 = '$apellido_2') OR login = '$login'";
        
        // Si es una edición, excluye el usuario actual para evitar conflictos con el mismo registro
        if ($id_Usuario) {
            $SQL = "SELECT * FROM usuarios 
                    WHERE ((nombre = '$nombre' 
                    AND apellido_1 = '$apellido_1' 
                    AND apellido_2 = '$apellido_2' 
                    AND id_Usuario != '$id_Usuario') 
                    OR (login = '$login' 
                    AND id_Usuario != '$id_Usuario'))";
        }
            // Para depuración: registrar la consulta SQL para verificar los datos
            //echo "Consulta SQL para existeUsuario: " . $SQL . "<br>";
        
        // Ejecutar la consulta
        $resultado = $this->DAO->consultar($SQL);
        
        // Devuelve true si encuentra resultados (usuario ya existe)
        return count($resultado) > 0;
    }
    public function actualizarUsuario($datos=array()) {
        $nombre = $datos['nombre'];
        $apellido_1 = $datos['apellido_1'];
        $apellido_2 = $datos['apellido_2'];
        $sexo = $datos['sexo'];
        $fecha_Alta = $datos['fecha_Alta'];
        $mail = $datos['mail'];
        $movil = $datos['movil'];
        $login = $datos['login'];
        $activo = $datos['activo'] ;
        $id_Usuario = $datos['id_Usuario'];
    
        $SQL = "UPDATE usuarios SET 
                nombre = '$nombre',
                apellido_1 = '$apellido_1',
                apellido_2 = '$apellido_2',
                sexo = '$sexo',
                fecha_Alta = '$fecha_Alta',
                mail = '$mail',
                movil = '$movil',
                login = '$login',
                activo = '$activo'
                WHERE id_Usuario = '$id_Usuario'";

    error_log("Consulta SQL en insertarUsuario: " . $SQL);
    
        return $this->DAO->actualizar($SQL);
    }
    public function obtenerRolesUsuario($id_Usuario) {
        $SQL = "SELECT r.id_rol, r.rol_descripcion FROM usuarios_roles ur 
                JOIN roles r ON ur.id_rol = r.id_rol 
                WHERE ur.id_usuario = '$id_Usuario'";
    
        return $this->DAO->consultar($SQL); // Devuelve un array asociativo con ID y nombre del rol
    }
    

    public function obtenerPermisosUsuario($id_Usuario) {
        $SQL = "SELECT p.id_permiso, p.descripcion_permiso FROM permisos_usuarios pu 
                JOIN permisos p ON pu.id_permiso = p.id_permiso 
                WHERE pu.id_usuario = '$id_Usuario'";
    
        return $this->DAO->consultar($SQL); // Devuelve un array con ID y descripción del permiso
    }
    

    public function obtenerPermisosRol($id_Rol) {
        $SQL = "SELECT p.id_permiso, p.descripcion_permiso FROM permisos_roles pr 
                JOIN permisos p ON pr.id_permiso = p.id_permiso 
                WHERE pr.id_rol = '$id_Rol'";
    
        return $this->DAO->consultar($SQL); // Devuelve un array con ID y descripción del permiso
    }
    
    public function asignarRolUsuario($id_Usuario, $id_Rol) {
        $SQL = "INSERT INTO usuarios_roles (id_usuario, id_rol) 
                VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE id_rol = id_rol";
        
        return $this->DAO->actualizarConParametros($SQL, [$id_Usuario, $id_Rol]);
    }
    
    

}

?>