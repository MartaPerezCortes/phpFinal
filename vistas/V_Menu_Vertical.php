<?php
// Asegúrate de que el controlador pasa los datos del menú en $datos['menu']
$menu = $datos['menu']; // Opciones del menú obtenidas del controlador
// Función para generar el menú vertical
$permisos = $datos['permisos'] ?? [];
$filtro_aplicado = $datos['filtro_aplicado'] ?? false;
if (!is_array($menu)) {
    echo "Error: \$menu no es un array. Tipo recibido: " . gettype($menu);
    $menu = []; // Evita que el foreach falle
}

if (!is_array($permisos)) {
    echo "Error: \$permisos no es un array. Tipo recibido: " . gettype($permisos);
    $permisos = []; // Evita que el foreach falle
}
function generarMenuVertical($menu, $permisos = [], $filtro_aplicado = false,$nivel = 1, $id_padre = null,$visited=[]) {
    if ($nivel > 10) {
        error_log("Nivel máximo alcanzado en la recursión con id_padre: $id_padre");
        return '';
    }

    // Verificar si ya visitamos este menú para evitar ciclos infinitos
    if (in_array($id_padre, $visited)) {
        error_log("Ciclo detectado, el id_padre {$id_padre} ya fue visitado.");
        return '';
    }
    $visited[] = $id_padre;
   
    $html = '';
    $items = array_filter($menu, function ($item) use ($nivel, $id_padre) {
        return $item['nivel'] == $nivel && 
               (($id_padre === null && $item['id_padre'] === null) || $item['id_padre'] == $id_padre);
    });
    

    if (!empty($items)) {
        $html .= '<ul class="menu-vertical" style="list-style: none; padding: 0; width: 100%;">';

        foreach ($items as $index => $item) {
            // Botón para agregar encima
           
           
             if (empty($_GET['usuario']) && empty($_GET['rol'])): 
                $html .= '<li class="menu-add-above" style="display: flex; align-items: center; margin: 5px 0;">';
                $html .= '<img src="iconos/añadir.png" 
                        class="add-icon" 
                        style="cursor: pointer; margin-right: 10px;" 
                        data-id="' . htmlspecialchars($item['id_menu'] ?? '') . '" 
                        data-id-padre="' . htmlspecialchars($id_padre ?? '') . '" 
                        data-posicion="' . htmlspecialchars($item['posicion'] ?? 0) . '" 
                        data-position="before" 
                        data-nivel="' . $nivel . '" 
                        alt="Añadir encima">';
                $html .= '<div class="puntitos" style="flex: 1; border-bottom: 1px dashed #ccc;"></div>'; // Línea decorativa
                $html .= '</li>';
             endif; 
          

            // La propia opción del menú
            $html .= '<li class="menu-item" 
                        data-id="' . htmlspecialchars($item['id_menu'] ?? '') . '" 
                        data-id-padre="' . htmlspecialchars($id_padre ?? '') . '" 
                        data-nivel="' . $nivel . '" 
                        data-posicion="' . htmlspecialchars($item['posicion'] ?? 0) . '" 
                        style="background-color: white; margin: 5px 0; padding: 15px; border-radius: 8px; justify-content: space-between; align-items: center; box-shadow: 0px 2px 4px rgba(0,0,0,0.1);">';
                        if ($filtro_aplicado && is_array($permisos)) {
                            $html .= '<div class="menu-item-titulo" style="display: block; justify-content: space-between;align-items: center;">';
                        }else{
                            $html .= '<div class="menu-item-titulo" style="display: flex; justify-content: space-between;align-items: center;">';
                        }
                       
            $html .= '<a href="' . htmlspecialchars($item['url'] ?? '#') . '" style="color: #74b8eb; font-weight: bold; text-decoration: none;flex-grow: 1;">' . 
                        htmlspecialchars($item['nombre'] ?? '') . 
                        '</a>';
            
            
                        if ($filtro_aplicado && is_array($permisos)) {
                            $html .= '<ul class="permisos-list" style="list-style: none;id="permisos-' . htmlspecialchars($item['id_menu']) . '">';
                            
                            /*foreach ($permisos as $permiso) {
                                if ($permiso['id_menu'] == $item['id_menu']) {
                                    $asignado = $permiso['asignado'] ?? false;
                                    $rolesAsignados = !empty($permiso['roles_asignados']) ? explode(', ', $permiso['roles_asignados']) : [];
                                    $rolesUsuario = !empty($permiso['roles_usuario']) ? explode(', ', $permiso['roles_usuario']) : [];
                            
                                    // Verificar si estamos filtrando por usuario o por rol
                                    $filtroPorUsuario = !empty($_GET['usuario']) && empty($_GET['rol']); 
                                    $filtroPorRol = !empty($_GET['rol']); 
                            
                                    // Determinar si el permiso proviene de un rol
                                    $rolQueOtorga = null;
                                    if ($filtroPorUsuario) { // Solo calculamos esto si estamos en modo usuario
                                        foreach ($rolesUsuario as $rolUsuario) {
                                            if (in_array($rolUsuario, $rolesAsignados)) {
                                                $rolQueOtorga = $rolUsuario;
                                                break;
                                            }
                                        }
                                    }
                            
                                    $html .= '<li>';
                                    $html .= '<input type="checkbox" class="permiso-checkbox" data-id-permiso="' . htmlspecialchars($permiso['id_permiso']) . '" ' . ($asignado ? 'checked' : '') . '>';
                                    $html .= ' ' . htmlspecialchars($permiso['descripcion_permiso']) . ' (' . htmlspecialchars($permiso['cod_permiso']) . ')';
                            
                                    // 🔵 SOLO MOSTRAR LA "Ⓡ" SI SE FILTRA POR USUARIO, NO POR ROL
                                    if ($filtroPorUsuario && $rolQueOtorga) {
                                        $html .= ' <span class="permiso-rol" title="Otorgado por rol: ' . htmlspecialchars($rolQueOtorga) . '" style="color: blue; cursor: help;">Ⓡ</span>';
                                    }
                            
                                    $html .= '</li>';
                                }
                            }*/
                            
                            foreach ($permisos as $permiso) {
                                if ($permiso['id_menu'] == $item['id_menu']) {
                                    $asignado = $permiso['asignado'] ?? false;
                                    $rolesAsignados = !empty($permiso['roles_asignados']) ? explode(', ', $permiso['roles_asignados']) : [];
                                    $rolesUsuario = !empty($permiso['roles_usuario']) ? explode(', ', $permiso['roles_usuario']) : [];
                            
                                    // Verificar si estamos filtrando por usuario y/o rol
                                    $hayUsuario = !empty($_GET['usuario']);
                                    $hayRol = !empty($_GET['rol']);
                            
                                    // Si hay usuario y rol, nos comportamos como si solo hubiera usuario
                                    $filtroPorUsuario = $hayUsuario;
                                    $filtroPorRol = !$hayUsuario && $hayRol;
                            
                                    // Determinar si el permiso proviene de un rol del usuario
                                    $rolQueOtorga = null;
                                    if ($filtroPorUsuario) { // Solo si se está filtrando por usuario
                                        foreach ($rolesUsuario as $rolUsuario) {
                                            if (in_array($rolUsuario, $rolesAsignados)) {
                                                $rolQueOtorga = $rolUsuario;
                                                break;
                                            }
                                        }
                                    }
                            
                                    $html .= '<li>';
                                    $html .= '<input type="checkbox" class="permiso-checkbox" data-id-permiso="' . htmlspecialchars($permiso['id_permiso']) . '" ' . ($asignado ? 'checked' : '') . '>';
                                    $html .= ' ' . htmlspecialchars($permiso['descripcion_permiso']) . ' (' . htmlspecialchars($permiso['cod_permiso']) . ')';
                            
                                    // 🔵 SOLO MOSTRAR LA "Ⓡ" SI SE FILTRA POR USUARIO (O USUARIO+ROL)
                                    /*if ($filtroPorUsuario && $rolQueOtorga) {
                                        $html .= ' <span class="permiso-rol" title="Otorgado por rol: ' . htmlspecialchars($rolQueOtorga) . '" style="color: blue; cursor: help;">Ⓡ</span>';
                                    }*/
                                    /*if ($filtroPorUsuario && !empty($rolesUsuario) && !empty($rolesAsignados)) {
                                        // Filtrar solo los roles del usuario que otorgan este permiso
                                        $rolesOtorgantes = array_intersect($rolesUsuario, $rolesAsignados);
                                    
                                        if (!empty($rolesOtorgantes)) {
                                            // Concatenar los roles en el tooltip
                                            $rolesTooltip = implode(', ', array_map('htmlspecialchars', $rolesOtorgantes));
                                            $html .= ' <span class="permiso-rol" title="Otorgado por roles: ' . $rolesTooltip . '" style="color: blue; cursor: help;">🔹</span>';
                                        }
                                    }*/
                                    if ($filtroPorUsuario && !empty($rolesUsuario) && !empty($rolesAsignados)) {
                                        // Filtrar solo los roles del usuario que otorgan este permiso
                                        $rolesOtorgantes = array_intersect($rolesUsuario, $rolesAsignados);
                                    
                                        if (!empty($rolesOtorgantes)) {
                                            // Concatenar los roles en el tooltip
                                            $rolesTooltip = implode(', ', array_map('htmlspecialchars', $rolesOtorgantes));
                                            $html .= ' <span class="permiso-rol" title="Otorgado por roles: ' . $rolesTooltip . '" style="color: blue; cursor: help;">🔹</span>';
                                        }
                                    }
                                    
                            
                                    $html .= '</li>';
                                }
                            }
                            
                            
                            

                            $html .= '</ul>';
                        }
                        
            // Verificar valores de usuario y rol
            /*echo "<pre>";
            var_dump($_GET['usuario'] ?? 'No definido');
            var_dump($_GET['rol'] ?? 'No definido');
            echo "</pre>";*/
            // Detener la ejecución para ver los valores


                        
            if ((!isset($_GET['usuario']) && empty($_GET['usuario'])) || (!isset($_GET['rol']) && empty($_GET['rol']))):
                //echo "Ambos están vacíos o no definidos";
            //if (empty($_GET['usuario']) && empty($_GET['rol'])): 
                $html .= '<div class="menu-item-actions" style="display: flex; align-items: center;">';
                // Botón de edición
                $html .= '<img src="iconos/edit.png" 
                            class="edit-icon" 
                            style="cursor: pointer; margin-left: 10px;" 
                            data-id="' . htmlspecialchars($item['id_menu'] ?? '') . '" 
                            data-nivel="' . $nivel . '" 
                            alt="Editar"
                            onclick="abrirFormulario"()>';

                // Botón de eliminación
                $html .= '<img src="iconos/eliminar.png" 
                            class="delete-icon" 
                            style="cursor: pointer; margin-left: 10px;" 
                            data-id="' . htmlspecialchars($item['id_menu'] ?? '') . '" 
                            data-nivel="' . $nivel . '" 
                            alt="Eliminar">';

                $html .= '</div>'; // Cierre de acciones
            endif; 
            
            $html .= '</div>';
          
               // **Aseguramos que el ID del contenedor de permisos es correcto**
               $html .= '<div id="permisos-container-' . htmlspecialchars($item['id_menu']) . '" class="permisos-container" style="margin-top: 5px;">';
            
               if ((!isset($_GET['usuario']) && empty($_GET['usuario'])) || (!isset($_GET['rol']) && empty($_GET['rol']))):
                // **Lista de Permisos (Siempre existe, aunque esté vacía)**
               $html .= '<ul class="permisos-list" id="permisos-' . htmlspecialchars($item['id_menu']) . '" style="list-style: none; padding-left: 20px; margin-top: 5px;">';
               $html .= '</ul>'; 
               
               

               // **Botón para agregar permisos (solo el icono, sin línea)**
               $html .= '<img src="iconos/añadir.png" class="add-permiso-icon" style="cursor: pointer; width: 16px; height: 16px; display: block; margin-left: 6%;" data-id-menu="' . htmlspecialchars($item['id_menu']) . '" alt="Añadir Permiso">';
               endif;

               $html .= '</div>'; // **Cierre del contenedor de permisos**
               
               $html .= '</li>'; // **Cierre del `menu-item`**
   
            // Submenús recursivos
            $submenus = generarMenuVertical($menu, $permisos, $filtro_aplicado, $nivel + 1, $item['id_menu'], $visited);

             if (!empty($submenus)) {
                $html .= '<div class="submenu" 
                            data-nivel="' . ($nivel + 1) . '" 
                            data-id-padre="' . htmlspecialchars($item['id_menu'] ?? '') . '" 
                            style="margin-left: 40px;">';
                $html .= $submenus;
                $html .= '</div>';
            }
           
            if (empty($_GET['usuario']) && empty($_GET['rol'])): 
                if ($nivel == 1) {
                $html .= '<li class="menu-add-submenu" style="display: flex; align-items: center; margin: 5px 0;">';
                $html .= '<img src="iconos/añadir.png" 
                            class="add-submenu-icon" 
                            style="cursor: pointer; margin-right: 10px;" 
                            data-id="' . htmlspecialchars($item['id_menu'] ?? '') . '" 
                            data-nivel="' . ($nivel + 1) . '" 
                            data-id-padre="' . htmlspecialchars($item['id_menu'] ?? '') . '" 
                            alt="Añadir Submenú"
                            onclick="abrirFormulario()">';
                $html .= '<div class="puntitos" style="flex: 1; border-bottom: 1px dashed #ccc;"></div>'; // Línea decorativa
                $html .= '</li>';
            }
            endif; 
            // Botón para agregar submenú si es un elemento padre
            
        }
        
        if (empty($_GET['usuario']) && empty($_GET['rol'])): 
         if ($nivel == 1) {
            $html .= '<li class="menu-add-below" style="display: flex; align-items: center; margin: 5px 0; padding-bottom:2vw;">';
            $html .= '<img src="iconos/añadir.png" 
                        class="add-icon" 
                        style="cursor: pointer; margin-right: 10px;" 
                        data-id="" 
                        data-id-padre="" 
                        data-posicion="' . (count($items) + 1) . '" 
                        data-position="after" 
                        data-nivel="' . $nivel . '" 
                        alt="Añadir nuevo elemento"
                        onclick="abrirFormulario()">';
            $html .= '<div class="puntitos" style="flex: 1; border-bottom: 1px dashed #ccc;"></div>';
            $html .= '</li>';
        }       
        endif; 
        // Agregar ícono al final del nivel principal
        

        $html .= '</ul>';
    }

    return $html;
}

// Generar la estructura del menú
echo '<div id="contendorMenuVertical" style="display: flex; width: 100%; height: 100vh; box-sizing: border-box;">'; // Contenedor principal, ocupa toda la pantalla horizontal

// Contenedor del menú vertical
echo '<div class="menu-vertical-container" style="width: 50%; margin: 2vw; padding: 20px; background-color: #f8f9fa; border-radius: 12px; box-shadow: 0px 4px 8px rgba(0,0,0,0.1);">'; 

// Encabezado fijo
echo '<div class="menu-header">';
echo '<h2 style="text-align: center; color: rgb(207, 156, 255); margin-bottom: 20px;">Edición menú</h2>'; 
echo '</div>';

echo '<div class="menu-wrapper" style="background-color: #fff; border-radius: 12px; box-shadow: 0px 4px 8px rgba(0,0,0,0.1);">'; 
echo generarMenuVertical($menu,$permisos, $filtro_aplicado); 
echo '</div>'; // Cierre del contenedor del menú
echo '</div>'; // Cierre del contenedor del menú vertical

// Contenedor del formulario
echo '<div class="form-section" id="form-section" style="width: 50%; padding: 20px; display: none; margin-top:8vw">'; 
echo '<form id="menuForm" style="width: 100%;">';
echo '<input type="hidden" id="id_menu" name="id_menu">';
echo '<input type="hidden" id="id_padre" name="id_padre">';
echo '<input type="hidden" id="posicion" name="posicion">';
echo '<input type="hidden" id="nivel" name="nivel">';
echo '<div class="mb-3">';
echo '<label for="nombre" class="form-label" style="font-weight: bold;">Nombre</label>';
echo '<input type="text" class="form-control" id="nombre" name="nombre">';
echo '</div>';
echo '<div class="mb-3">';
echo '<label for="url" class="form-label" style="font-weight: bold;">URL</label>';
echo '<input type="text" class="form-control" id="url" name="url">';
echo '</div>';
echo '<button type="button" class="btn btn-primary" style="background-color: #74b8eb; border-color: #74b8eb;" onclick="guardarOpcion()">Guardar</button>';
echo '<button type="button" class="btn btn-secondary" onclick="cerrarFormulario()">Cancelar</button>';
echo '</form>';
echo '</div>'; // Cierre del contenedor del formulario
echo '</div>';
echo '</div>'; // Cierre del contenedor principal
?>
