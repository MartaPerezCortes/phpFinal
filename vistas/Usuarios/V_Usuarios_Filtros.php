<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$permisos_id = array_column($_SESSION['permisos'] ?? [], 'id_permiso');


?>
<h2>Mtto. de Usuarios</h2>
<div class="container-fluid" id="capaFiltrosBusqueda"> <! --se ajusta al ancho de la pantalla-- >
<form id="formularioBuscar" name="formularioBuscar">

    <div class="row">
            <div class="form-group col-md-6 col-sm-12"> <! -- si la pantalla es mediana ocupa la mitad (6) y si es pequeña ocupa todo (12)-- >
                <label for="ftexto">Nombre/texto:</label> <! -- el for el mismo que el name del input-- >
                <input type="text" id="ftexto" name="ftexto" class="form-control" placeholder="Texto a buscar" value=""/>
            </div>
            <div class="form-group col-md-6 col-sm-12"> <! --si la pantalla es mediana ocupa la mitad (6) y si es pequeña ocupa todo (12)-- >
                <label for="factivo">Estado:</label> <! -- el for el mismo que el name del input-- >
                <select  id="factivo" name="factivo" class="form-control" > <! -- es un select porque es SI o NO, una opcion, no para introducir datos-- >
                    <option value="" selected>Todos</option>
                    <option value="S" >Activos</option>
                    <option value="N" >No Activos</option>
                </select>

            </div>
        </div>
        <div class="row">
            <div id="botonesBuscar" class="col-lg-12">
            

                <?php if (in_array(2, $permisos_id)  ||  in_array(4, $permisos_id)): ?> 
                    <button type="button" class="btn btn-primary" 
                            onclick="buscar('Usuarios','getVistaListadoUsuarios','formularioBuscar','capaResultadosBusqueda');">
                        Buscar
                    </button>
                <?php endif; ?>

                <?php if (in_array(3, $permisos_id)): ?> 
                    <button type="button" class="btn btn-secondary" id="btnCrearNuevo"
                            onclick="obtenerVista('Usuarios','getVistaNuevoEditar','capaEditarCrear','');mostrarBotonNuevo()">
                        Nuevo usuario
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</form> 
  
<div class="container-fluid" id="capaResultadosBusqueda"></div>
<div class="container-fluid" id="capaEditarCrear"></div>