<h2>Mtto. de Menú</h2>

<div class="container-fluid" id="capaFiltrosBusqueda">
    <form id="formularioFiltros" name="formularioFiltros" style="width:90%; margin:auto;" onsubmit="filtrarPermisos(event)">

        <div class="row" >
            <!-- Filtro de Usuario -->
            <div class="form-group col-md-6 col-sm-12">
                <label for="usuarioFiltro">Selecciona Usuario:</label>
                <select id="usuarioFiltro" name="usuario" class="form-control">
                    <option value="">-- Selecciona Usuario --</option>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?= htmlspecialchars($usuario['id_usuario']) ?>">
                            <?= htmlspecialchars($usuario['nombre']) ?>
                            <?= !empty($usuario['apellido_1']) ? ' ' . htmlspecialchars($usuario['apellido_1']) : '' ?>
                            <?= !empty($usuario['apellido_2']) ? ' ' . htmlspecialchars($usuario['apellido_2']) : '' ?>
                        </option>
                    <?php endforeach; ?>

                </select>
            </div>

            <!-- Filtro de Rol -->
            <div class="form-group col-md-6 col-sm-12">
                <label for="rolFiltro">Selecciona Rol:</label>
                <select id="rolFiltro" name="rol" class="form-control">
                    <option value="">-- Selecciona Rol --</option>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?= htmlspecialchars($rol['id_rol']) ?>">
                            <?= htmlspecialchars($rol['rol_descripcion']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="row">
            <div id="botonesBuscar" class="col-lg-12">
                <button type="submit" class="btn btn-primary">Buscar</button>
                <button type="button" class="btn btn-secondary" onclick="limpiarFiltros()">Limpiar</button>
            </div>
        </div>

    </form>
</div>

<!-- Contenedores para resultados y edición/creación -->
<div class="container-fluid" id="capaMenuPermisos"></div>
<script src="js/Menu.js"></script>
