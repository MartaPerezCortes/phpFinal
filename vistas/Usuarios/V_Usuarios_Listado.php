<?php

//vamos a usar al variable datos ya que es la que hemos definido en la Vista, es la que llevara los datos de los usuarios
//los usuarios nos vienen de una array que contiene $datos
//$usuarios=$datos['usuarios'];
 //predefinimos la variable que quiero recibir y asi me aseguro que existen en la array de datos que nos viene
 //definimos la array de usuarios como una array vacia
 $usuarios=array();
 //Extraigo las variables que contengan datos
 if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
 $permisos_id = array_column($_SESSION['permisos'] ?? [], 'id_permiso');
 
 extract($datos); //coge una array y los campos que le vengan los extrae con el valor que tengan, así si añado más parametros los predefinimos y extraemos
 
 $html='';
 $html.='<div class="table-responsive">
         <table class="table table-sm table-striped">';
 $html.='<thead>
                <tr>';
                  // Verificamos si el usuario tiene permiso para editar
                  if (in_array(4, $permisos_id)) {
                     $html .= '<th>Editar</th>';
                  }
$html .= '<th>Apellidos, Nombre</th>
             <th>mail</th>
             <th>login</th>
             <th>¿Activo?</th>
            </tr>
         </thead>
         <tbody>';
         
        

 foreach($usuarios as $posicion=>$fila){ //lo de posicion no hace falta ponerlo si no vamos a usar la posicion, esta puesto pero sobra aqui, no lo usamos

    /*echo$fila['nombre']; //mostramos el nombre y ponemos un salto de linea
    echo(' ');
    echo$fila['apellido_1'];
    echo'<br>';*/
   

      $estilo = '';
      $activo = ($fila['activo'] == 'S') ? 'Activo' : 'Inactivo';
  
                   // Configuración del botón deslizante
    $isChecked = ($fila['activo'] == 'S') ? 'checked' : '';
    $colorClass = ($fila['activo'] == 'S') ? 'toggle-active' : 'toggle-inactive';
   
  
      $html .= '<tr>';
      // Mostrar botón de editar si el usuario tiene el permiso 4 (Editar Usuarios)
    if (in_array(4, $permisos_id)) {
        $html .= '<td><img src="iconos/edit.png" id="lapiz" style="height:1.5em;width:1.5em;" 
        onclick="obtenerVista_EditarCrear(\'Usuarios\',\'getVistaNuevoEditar\',\'capaEditarCrear\',\'' . $fila['id_Usuario'] . '\');mostrarBotonEditar()"></td>';
    }
  
    $html .= '<td nowrap>' . $fila['apellido_1'] . ' ' . $fila['apellido_2'] . ', ' . $fila['nombre'] . '</td>
              <td>' . $fila['mail'] . '</td>
              <td>' . $fila['login'] . '</td>';

     
// Si no tiene permiso 4 o 2, el checkbox estará deshabilitado
$isDisabled = (!in_array(4, $permisos_id)) ? 'disabled="disabled"' : '';

// Si está deshabilitado, añadimos un onclick en el LABEL para mostrar advertencia
$onClickWarning = ($isDisabled) ? 'onclick="mostrarAdvertencia(event)"' : '';

$html .= '<td>
            <label class="switch" ' . $onClickWarning . '>
                <input type="checkbox" class="toggle-status" data-id="' . $fila['id_Usuario'] . '" ' . $isChecked . ' ' . $isDisabled . '>
                <span class="slider ' . $colorClass . '"></span>
            </label>
          </td>';

$html .= '</tr>';
}

 
 $html .= '</tbody>
          </table></div>';
 
 // Agregar script para mostrar advertencia
 
 echo $html;

 


   
   /*
   <table>: Esta etiqueta HTML define el inicio de una tabla. Todo el contenido dentro de esta etiqueta será parte de una tabla.
   <thead> contiene la cabecera de la tabla, que define los títulos de las columnas.
   <tbody> contiene los datos de la tabla, las filas con información que será mostrada dinámicamente.
   <tr>: Define una fila dentro de la tabla.
   <th>: Define una celda de encabezado dentro de esa fila (es decir, el título de la columna).+
   <td>: Define una celda dentro de una fila.
   */


?>