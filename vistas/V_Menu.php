<?php
$menu = $datos['menu']; // Opciones del menú obtenidas del controlador
$permisosUsuario = array_column($_SESSION['permisos'] ?? [], 'id_permiso'); // Extraemos solo los IDs de permisos del usuario

// Filtrar el menú para incluir solo los elementos con permisos asignados
$menuFiltrado = [];
$menuVisto = []; // Array para evitar duplicados

foreach ($menu as $item) {
    if (isset($item['id_permiso']) && in_array($item['id_permiso'], $permisosUsuario)) {
        if (!isset($menuVisto[$item['id_menu']])) { // Si no hemos agregado este ID aún
            $menuFiltrado[] = $item;
            $menuVisto[$item['id_menu']] = true; // Marcamos este ID como visto
        }
    }
}

// Función para generar el menú sin duplicados
function generarMenu($menu, $nivel = 1, $id_padre = null) {
    $html = '';
    $items = array_filter($menu, function ($item) use ($nivel, $id_padre) {
        return $item['nivel'] == $nivel && $item['id_padre'] == $id_padre;
    });

    if (!empty($items)) {
        $html .= $nivel == 1 ? '<ul class="navbar-nav me-auto mb-2 mb-lg-0">' : '<ul class="dropdown-menu">';

        foreach ($items as $item) {
            $submenus = generarMenu($menu, $nivel + 1, $item['id_menu']);
            $html .= '<li class="nav-item ' . ($submenus ? 'dropdown' : '') . '">';

            if ($submenus) {
                // Menú con submenús
                $html .= '<a class="nav-link dropdown-toggle" href="' . htmlspecialchars($item['url'] ?? '#') . '" role="button" data-bs-toggle="dropdown">' .
                            htmlspecialchars($item['nombre']) .
                            '</a>';
                $html .= $submenus;
            } else {
                // Menú sin submenús
                $html .= '<a class="nav-link" href="' . htmlspecialchars($item['url'] ?? '#') . '">' .
                            htmlspecialchars($item['nombre']) .
                            '</a>';
            }

            $html .= '</li>';
        }
        $html .= '</ul>';
    }

    return $html;
}

// Renderizar el menú dinámico
echo '<nav class="navbar navbar-expand-lg navbar-light bg-light">';
echo '<div class="container-fluid">';
echo '<a class="navbar-brand" href="#">Navbar</a>';
echo '<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">';
echo '<span class="navbar-toggler-icon"></span>';
echo '</button>';
echo '<div class="collapse navbar-collapse" id="navbarSupportedContent">';
echo generarMenu($menuFiltrado); // Pasamos el menú filtrado sin duplicados

// Barra de búsqueda con botón de buscar
echo '<form class="d-flex" role="search" style="margin-left:auto;">';
echo '<input class="form-control me-2" type="search" placeholder="Buscar" aria-label="Buscar">';
echo '<button class="btn btn-outline-success" type="submit">Buscar</button>';
echo '</form>';

echo '</div>'; // Cierre de collapse
echo '</div>'; // Cierre de container-fluid
echo '</nav>';
?>
