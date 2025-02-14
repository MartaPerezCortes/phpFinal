console.log("Menu.js cargado correctamente");

// Verificar si el formulario existe y agregar el evento
document.addEventListener('DOMContentLoaded', function () {

    console.log("✅ Menu.js cargado correctamente");
    const formularioFiltros = document.getElementById('formularioFiltros');
    
    if (formularioFiltros) {
        console.log("Formulario de filtros encontrado. Configurando evento...");
        
        formularioFiltros.addEventListener('submit', function(event) {
            if (event) {
                event.preventDefault();  // 🔄 Solo lo ejecuta si `event` existe
            }
        
            filtrarPermisos(event);  // Llama a la función de filtrado
            ajustarVisibilidadBotonesMenu();  // Ajusta la visibilidad de botones
        });
    } else {
        console.error("Formulario de filtros NO encontrado.");
    }
    
});


function configurarEventosMenuVertical() {
    console.log("Configurando eventos del menú vertical...");

    // Eventos para botones de agregar opción
    document.querySelectorAll('.add-icon').forEach(icon => {
        icon.addEventListener('click', () => {
            const id = icon.dataset.id; // ID del elemento de referencia
            const position = icon.dataset.position; // 'before' o 'after'
            const posicion = parseInt(icon.dataset.posicion, 10); // La posición base
            const nivel = parseInt(icon.dataset.nivel, 10); // Nivel del elemento actual
            const idPadre = icon.dataset.idPadre || null; // ID del padre, si existe

            console.log(`Agregar opción: ID = ${id}, Posición = ${posicion}, Nivel = ${nivel}, ID Padre = ${idPadre}`);
            mostrarFormularioNuevo(id, position, nivel, posicion, idPadre);
        });
    });

    // Eventos para el botón de agregar submenú
    document.querySelectorAll('.add-submenu-icon').forEach(icon => {
        icon.addEventListener('click', () => {
            const idPadre = icon.dataset.id; // ID del padre
            const nivel = parseInt(icon.dataset.nivel, 10); // Nivel del submenú (nivel actual + 1)
            let posicion = 1; // Posición por defecto si no hay otros elementos
                
            // Encuentra todos los elementos `menu-item` del mismo nivel y padre
            const menuItems = [...document.querySelectorAll(`.menu-item[data-nivel="${nivel}"][data-id-padre="${idPadre}"]`)];
                
            if (menuItems.length > 0) {
                // Encuentra la última posición del nivel actual
                const lastMenuItem = menuItems[menuItems.length - 1];
                const posicionAnterior = parseInt(lastMenuItem.dataset.posicion, 10);
                if (!isNaN(posicionAnterior)) {
                    posicion = posicionAnterior + 1;
                }
            }
                
            console.log("Icono actual:", icon);
            console.log("Último elemento encontrado:", menuItems.length > 0 ? menuItems[menuItems.length - 1] : "Ninguno");
            console.log("Posición calculada:", posicion);
                
            console.log(`Agregar submenú: ID Padre = ${idPadre}, Nivel = ${nivel}, Posición = ${posicion}`);
            mostrarFormularioNuevo(null, 'after', nivel, posicion, idPadre);
        });
    });

    // Botones para editar
    document.querySelectorAll('.edit-icon').forEach(icon => {
        icon.addEventListener('click', () => {
            const id = icon.dataset.id;
            mostrarFormularioEdicion(id);
        });
    });

    // Botones para eliminar
    document.querySelectorAll('.delete-icon').forEach(icon => {
        icon.addEventListener('click', () => {
            const id = icon.dataset.id;
            eliminarOpcion(id);
        });
    });

       // 📌 **Cargar permisos para cada menú**
       document.querySelectorAll('.menu-item').forEach(item => {
        const idMenu = item.dataset.id;
        console.log(`Cargando permisos en menú: ${idMenu}`);
        cargarPermisos(idMenu);
    });

    // Configurar eventos para permisos
    configurarEventosPermisos();
}


// Ejecutar inmediatamente al cargar el script
console.log("Ejecutando configurarEventosMenuVertical al cargar Menu.js...");
configurarEventosMenuVertical();

// Mostrar formulario para agregar una nueva opción
function mostrarFormularioNuevo(id, position, nivel, posicion, idPadre) {
    console.log(`Mostrar formulario nuevo: ID = ${id}, posición = ${position}, nivel = ${nivel}, posicion = ${posicion}, idPadre = ${idPadre}`);
    const formSection = document.getElementById('form-section');
    formSection.style.display = 'block';
    document.getElementById('id_menu').value = ''; // Nueva opción
    document.getElementById('id_padre').value = idPadre || ''; // ID del padre (puede ser null para nivel 1)
    document.getElementById('posicion').value = posicion; // La posición base
    document.getElementById('nombre').value = ''; // Limpiar el formulario
    document.getElementById('nivel').value = nivel; // Añadir el nivel al formulario
}

// Mostrar formulario para editar una opción
function mostrarFormularioEdicion(id) {
    console.log("Mostrando formulario de edición para ID:", id);
    fetch(`C_Frontal.php?controlador=Menu&metodo=obtenerOpcion&id_menu=${id}`)
        .then(response => response.json())
        .then(data => {
            const formSection = document.getElementById('form-section');
            formSection.style.display = 'block';
            document.getElementById('id_menu').value = data.id_menu; // ID del menú a editar
            document.getElementById('id_padre').value = data.id_padre || ''; // No cambia
            document.getElementById('posicion').value = data.posicion || ''; // No cambia
            document.getElementById('nivel').value = data.nivel || ''; // No cambia
            document.getElementById('nombre').value = data.nombre; // Solo edita el nombre
            document.getElementById('url').value = data.url || ''; // Editar URL
        })
        .catch(error => console.error("Error al cargar la opción:", error));
}

// Ocultar el formulario
function cerrarFormulario() {
    const formSection = document.getElementById('form-section');
    formSection.style.display = 'none';
}

// Guardar la opción (editar o nueva)
function guardarOpcion() {
    const idMenuInput = document.getElementById('id_menu');
    const idPadreInput = document.getElementById('id_padre');
    const posicionInput = document.getElementById('posicion');
    const nombreInput = document.getElementById('nombre');
    const nivelInput = document.getElementById('nivel');
    const urlInput = document.getElementById('url'); // Campo para la URL

    if (!idMenuInput || !idPadreInput || !posicionInput || !nombreInput || !nivelInput || !urlInput) {
        console.error("Error: No se encontraron uno o más elementos del formulario.");
        return;
    }

    const formData = new FormData();
    formData.append('id_menu', idMenuInput.value);
    formData.append('id_padre', idPadreInput.value || null);
    formData.append('posicion', posicionInput.value);
    formData.append('nivel', nivelInput.value);
    formData.append('nombre', nombreInput.value);
    formData.append('url', urlInput.value); // Aquí se agrega el valor de la URL

    fetch("C_Frontal.php?controlador=Menu&metodo=guardarOpcion", {
        method: "POST",
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Opción guardada con éxito.");
                limpiarFormulario();
                actualizarMenu();
                actualizarNavbar(); // Actualiza el navbar
            } else {
                alert("Error al guardar: " + data.message);
            }
        })
        .catch(error => console.error("Error al guardar la opción:", error));
}

function actualizarNavbar() {
    fetch("C_Frontal.php?controlador=Menu&metodo=mostrarMenu")
    .then(response => response.text())
    .then(html => {
        const navbarContainer = document.querySelector('.container-fluid'); // Asegúrate de usar el selector correcto
        if (navbarContainer) {
            navbarContainer.innerHTML = html; // Reemplaza el contenido existente
        }
    })
    .catch(error => console.error("Error al actualizar el navbar:", error));
}


// Eliminar una opción
function eliminarOpcion(id) {
    if (confirm("¿Deseas eliminar esta opción y sus submenús?")) {
        fetch(`C_Frontal.php?controlador=Menu&metodo=eliminarOpcion&id_menu=${id}`, {
            method: "POST"
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Opción eliminada");
                    actualizarMenu();
                    actualizarNavbar(); // Actualiza el navbar
                } else {
                    alert("Error al eliminar: " + data.message);
                }
            })
            .catch(error => console.error("Error al eliminar la opción:", error));
    }
}

// Actualizar el menú vertical
function actualizarMenu() {
    fetch("C_Frontal.php?controlador=Menu&metodo=mostrarMenuVertical")
    .then(response => response.text())
    .then(html => {
        const menuContainer = document.querySelector('#contendorMenuVertical');
        if (menuContainer) {
            menuContainer.innerHTML = html;
            configurarEventosMenuVertical(); // Reconfigura eventos
             // 🔄 Volver a cargar permisos para cada elemento del menú
             document.querySelectorAll('.menu-item').forEach(item => {
                const idMenu = item.dataset.id;
                cargarPermisos(idMenu);
            });
        }
    })
    .catch(error => console.error("Error al actualizar el menú:", error));
}

function cerrarFormulario() {
    const formSection = document.getElementById('form-section');
    if (formSection) {
        formSection.style.display = 'none';
    }
}

function limpiarFormulario() {
    document.getElementById('nombre').value = ''; // Limpia el nombre
    document.getElementById('url').value = ''; // Limpia la URL
}
function obtenerVistaMenuPermisos() {
    fetch('C_Frontal.php?controlador=Menu&metodo=mostrarMenuFiltros')
        .then(response => response.text())
        .then(html => {
            document.getElementById('capaContenido').innerHTML = html;
            configurarEventosFiltros();  // Configura los eventos del formulario de filtros
            
        })
        .catch(error => console.error("Error al cargar el formulario de filtros:", error));
}

function configurarEventosFiltros() {
    const formularioFiltros = document.getElementById('formularioFiltros');
    if (formularioFiltros) {
        formularioFiltros.addEventListener('submit', function(event) {
            
            if (event) {
                event.preventDefault();  // 🔄 Solo lo ejecuta si `event` existe
            }
        
            filtrarPermisos();  // Llama a la función que cargará el menú filtrado
        });
    }
}



/********************PERMISOS********************/

function cargarPermisos(id_menu) {
    console.log(`Cargando permisos para ID_MENU: ${id_menu}`);

    const usuarioSeleccionado = document.getElementById('usuarioFiltro') ? document.getElementById('usuarioFiltro').value : '';
    const rolSeleccionado = document.getElementById('rolFiltro') ? document.getElementById('rolFiltro').value : '';

    fetch(`C_Frontal.php?controlador=Menu&metodo=obtenerPermisos&id_menu=${id_menu}&usuario=${usuarioSeleccionado}&rol=${rolSeleccionado}`)
        .then(res => res.json())
        .then(permisos => {
            const contenedor = document.querySelector(`#permisos-container-${id_menu}`);

            if (!contenedor) {
                console.error(`❌ No se encontró el contenedor de permisos para el menú ${id_menu}`);
                return;
            }

            const listaPermisos = contenedor.querySelector(".permisos-list");
            if (!listaPermisos) {
                console.error(`❌ No se encontró la lista de permisos dentro del contenedor ${id_menu}`);
                return;
            }

            listaPermisos.innerHTML = "";  // Limpiar antes de añadir permisos

            permisos.forEach(permiso => {
                const li = document.createElement("li");
                li.style.display = "flex";
                li.style.alignItems = "center";
                li.style.padding = "5px";

                if (usuarioSeleccionado || rolSeleccionado) {
                    li.innerHTML = `
                        <input type="checkbox" class="permiso-checkbox" data-id-permiso="${permiso.id_permiso}" ${permiso.asignado ? 'checked' : ''}>
                        <span style="font-size: 14px; margin-left: 10px;">
                            ${permiso.descripcion_permiso} (${permiso.cod_permiso})
                        </span>
                    `;
                } else {
                   /* li.innerHTML = `
                        <span style="font-size: 14px; margin-left: 15px;">
                            ${permiso.descripcion_permiso} (${permiso.cod_permiso})
                        </span>
                    `;*/
                    li.innerHTML = `
                    <li style="font-size: 14px; list-style-type: disc; margin-left: 15px;">
                        ${permiso.descripcion_permiso} (${permiso.cod_permiso})
                    </li>

                    <div style="display:flex">
                        <img src="iconos/edit.png" class="edit-permiso" data-id="${permiso.id_permiso}" data-id-menu="${id_menu}" style="cursor: pointer; margin-left: 10px;width: 18px; height: 18px; cursor: pointer;">
                        <img src="iconos/eliminar.png" class="delete-permiso" data-id="${permiso.id_permiso}" style="cursor: pointer; margin-left: 10px; width: 18px; height: 18px; cursor: pointer;">
                    </div>
                `;
                }

                listaPermisos.appendChild(li);
            });

            configurarEventosCheckboxPermisos(usuarioSeleccionado, rolSeleccionado);
        })
        .catch(err => console.error("Error al cargar permisos:", err));
}
function limpiarFiltros() {
    console.log("🔄 Limpiando filtros...");
    document.getElementById('usuarioFiltro').value = "";
    document.getElementById('rolFiltro').value = "";
    
    // Restablecer roles asignados para evitar que persistan
    const rolFiltro = document.getElementById('rolFiltro');
    for (let option of rolFiltro.options) {
        option.textContent = option.textContent.replace(/^✅ |^❌ /, '').replace(/\s*\(Asignado\)|\s*\(No Asignado\)/, '');
        option.dataset.asignado = "false"; 
    }

    // Restablecer el contenedor de permisos
    document.getElementById('capaMenuPermisos').innerHTML = "";

    console.log("✔️ Filtros restablecidos.");
}


// Configurar eventos de permisos
function configurarEventosPermisos() {
    document.querySelectorAll('.add-permiso-icon').forEach(icon => {
        icon.addEventListener('click', () => {
            const id_menu = icon.dataset.idMenu;
            mostrarFormularioPermiso(id_menu);
        });
    });

   
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('edit-permiso')) {
            const id_permiso = event.target.dataset.id;
            const id_menu = event.target.dataset.idMenu;
            console.log('Icono de editar permiso clicado:', id_permiso);
            mostrarFormularioPermiso(id_menu, id_permiso);
        }
    
        if (event.target.classList.contains('delete-permiso')) {
            const id_permiso = event.target.dataset.id;
            console.log('Icono de eliminar permiso clicado:', id_permiso);
            eliminarPermiso(id_permiso);
        }
    });
    
}



// Mostrar formulario de permisos
function mostrarFormularioPermiso(id_menu, id_permiso = null) {
    console.log(`Mostrando formulario de permisos para ID_MENU: ${id_menu}, ID_PERMISO: ${id_permiso}`);

    // Evitar que se duplique el formulario
    let formExistente = document.querySelector(`#permisos-container-${id_menu} .permiso-form`);
    if (formExistente) {
        formExistente.remove();
    }

    const contenedor = document.querySelector(`#permisos-container-${id_menu}`);
    
    if (!contenedor) {
        console.error(`No se encontró el contenedor de permisos para el menú ${id_menu}`);
        return;
    }

    const form = document.createElement("div");
    form.classList.add("permiso-form");
    form.style.padding = "10px";
    form.style.borderRadius = "8px";
    form.style.backgroundColor = "#f8f9fa";
    form.style.boxShadow = "0px 2px 4px rgba(0,0,0,0.1)";
    form.style.marginTop = "10px";
    form.style.display = "flex";
    form.style.flexDirection = "column";
    form.style.alignItems = "center";

    form.innerHTML = `
        <label class="form-label" style="font-weight: bold; font-size: 1vw; margin-bottom: 5px;">Descripción del Permiso</label>
        <input class="form-control" type="text" id="permiso-descripcion-${id_menu}" placeholder="" style="width: 80%; padding: 5px; border-radius: 5px; border: 1px solid #ccc; margin-bottom: 10px;">
        
        <label class="form-label" style="font-weight: bold; font-size: 1vw; margin-bottom: 5px;">Código del Permiso</label>
        <input class="form-control" type="number" id="permiso-codigo-${id_menu}" placeholder="1 - Leer, 2 - Crear, 3 - Editar, 4 - Eliminar" style="width: 80%; font-size: 0.8vw;  padding: 5px; border-radius: 5px; border: 1px solid #ccc; margin-bottom: 10px;">
        <div style="display:flex;">
        <button onclick="guardarPermiso(${id_menu}, ${id_permiso})" style="background-color: #74b8eb; color: white; border: none; cursor: pointer; font-size: 1vw; font-weight: bold; width: auto; border-radius: 5vw; margin: 5px; padding: 5px 10px;">
            Guardar
        </button>
        
        <button onclick="cerrarFormularioPermiso(${id_menu})" style="background-color: #ccc; color: white; border: none; cursor: pointer; font-size: 1vw; font-weight: bold; width: auto; border-radius: 5vw; margin: 5px; padding: 5px 10px;">
            Cancelar
        </button>
        </div>
    `;

    // Insertar el formulario en el contenedor de permisos
    contenedor.appendChild(form);
}

// **Función para cerrar el formulario de permisos**
function cerrarFormularioPermiso(id_menu) {
    const form = document.querySelector(`#permisos-container-${id_menu} .permiso-form`);
    if (form) {
        form.remove();
    }
}


// Guardar permisos
function guardarPermiso(id_menu, id_permiso = null) {
    const descripcion = document.getElementById(`permiso-descripcion-${id_menu}`).value;
    const codigo = document.getElementById(`permiso-codigo-${id_menu}`).value;

    const formData = new FormData();
    formData.append("id_menu", id_menu);
    formData.append("descripcion_permiso", descripcion);
    formData.append("cod_permiso", codigo);

    if (id_permiso) {
        formData.append("id_permiso", id_permiso);
    }

    fetch("C_Frontal.php?controlador=Menu&metodo=guardarPermiso", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            cargarPermisos(id_menu);
            console.log("✅ Permiso guardado correctamente.");

            //Recargar la lista de permisos para el menú
            cargarPermisos(id_menu);

            // Cerrar el formulario automáticamente
            cerrarFormularioPermiso(id_menu);
        } else {
            alert("Error al guardar permiso");
            
        }
    })
    .catch(err => console.error("Error al guardar permiso:", err));
}



function eliminarPermiso(id_permiso, id_menu) {
    console.log("🗑️ Eliminando permiso con ID:", id_permiso);

    fetch(`C_Frontal.php?controlador=Menu&metodo=eliminarPermiso&id_permiso=${id_permiso}`, { method: "POST" })
    .then(res => res.json()) // Obtener respuesta JSON
    .then(data => {
        const permisoElemento = document.querySelector(`.delete-permiso[data-id="${id_permiso}"]`);

        if (data.success) {
            console.log(`✅ Permiso ${id_permiso} eliminado correctamente.`);

            // 🔎 Buscar el permiso en el DOM y eliminarlo
            if (permisoElemento) {
                permisoElemento.closest("li").remove(); // 🔥 Eliminar solo ese `li`, sin afectar la vista completa
                console.log(`🚀 Permiso con ID ${id_permiso} eliminado de la interfaz.`);
            } else {
                console.warn("⚠️ No se encontró el elemento en la interfaz para eliminar.");
            }
        } else {
            // 🛑 Solo mostrar error si el permiso sigue en la interfaz
            if (permisoElemento) {
                console.error("❌ Error al eliminar permiso:", data.message);
                alert(`❌ Error: ${data.message}`);
            } else {
                console.log(`⚠️ Respuesta del servidor indica error, pero el permiso ya fue eliminado visualmente.`);
            }
        }
    })
    .catch(err => {
        console.error("❌ Error al eliminar permiso:", err);
        alert("❌ Error de conexión. Inténtalo de nuevo.");
    });
}



function abrirFormulario() {
    document.getElementById("form-section").style.display = "block";
    document.querySelector(".menu-vertical-container").classList.add("shift");
}

function cerrarFormulario() {
    document.getElementById("form-section").style.display = "none";
    document.querySelector(".menu-vertical-container").classList.remove("shift");
}
function cerrarFormularioPermiso(id_menu) {
    const form = document.querySelector(`#permisos-container-${id_menu} .permiso-form`);
    if (form) {
        form.style.opacity = 0;
        setTimeout(() => form.remove(), 300); // ⏳ Desaparece con animación y luego se elimina
    }
}

function filtrarPermisos(event) {
    if (event) {
        event.preventDefault();  // 🔄 Solo lo ejecuta si `event` existe
    }

    const usuario = document.getElementById('usuarioFiltro').value;
    const rol = document.getElementById('rolFiltro').value;

    console.log(`🔍 Filtrando permisos para Usuario: ${usuario}, Rol: ${rol}`);

    // Si no se selecciona ni usuario ni rol, limpiar todo
    if (!usuario && !rol) {
        limpiarFiltros();
        return;
    }

    fetch(`C_Frontal.php?controlador=Menu&metodo=mostrarMenuVertical&usuario=${usuario}&rol=${rol}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('capaMenuPermisos').innerHTML = html;
            configurarEventosCheckboxPermisos(usuario, rol);
            ajustarVisibilidadBotonesMenu(usuario, rol);
        })
        .catch(err => console.error("❌ Error al filtrar permisos:", err));
}


// **Función para restablecer los roles previos**
function resetearRolesUsuario() {
    const rolFiltro = document.getElementById('rolFiltro');

    // 🔄 Restaurar la lista de roles como si no hubiera usuario seleccionado
    for (let option of rolFiltro.options) {
        option.textContent = option.textContent.replace(/^✅ |^❌ /, '').replace(/\s*\(Asignado\)|\s*\(No Asignado\)/, '');
        option.dataset.asignado = "false";  // Marcar todos como NO asignados
    }
}


function configurarEventosCheckboxPermisos(usuario, rol) {
    document.querySelectorAll('.permiso-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const idPermiso = this.dataset.idPermiso;
            const asignado = this.checked;

            const formData = new FormData();
            formData.append('id_permiso', idPermiso);
            formData.append('asignado', asignado ? 1 : 0);

            if (usuario) formData.append('id_usuario', usuario);
            if (rol) formData.append('id_rol', rol);

            fetch('C_Frontal.php?controlador=Menu&metodo=actualizarPermiso', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    console.log("Permiso actualizado correctamente.");
                } else {
                    console.error("Error al actualizar el permiso:", data.message);
                }
            })
            .catch(err => console.error("Error al enviar permiso:", err));
        });
    });
}


function ajustarVisibilidadBotonesMenu(usuario, rol) {
    const botonesAccion = document.querySelectorAll('.menu-item-actions'); // Botones de edición/eliminación

    if (usuario || rol) {
        // Si hay un filtro aplicado, ocultar botones de edición/eliminación
        botonesAccion.forEach(boton => boton.style.display = 'none');
        const menuVertical = document.querySelectorAll('#contendorMenuVertical'); 
        menuVertical.forEach(boton => boton.style.display = 'block');
        const menuVerticalContainer = document.querySelectorAll('.menu-vertical-container'); 
        menuVerticalContainer.forEach(boton => boton.style.width = '70%');
        menuVerticalContainer.forEach(boton => boton.style.margin = 'auto');
        menuVerticalContainer.forEach(boton => boton.style.height = 'auto');

    } else {
        // Si no hay filtro, mostrar botones de edición/eliminación
        botonesAccion.forEach(boton => boton.style.display = 'flex');
    }
}


// Llama a esta función cada vez que se aplique un filtro
document.getElementById('formularioFiltros').addEventListener('submit', function(event) {
    if (event) {
        event.preventDefault();  // 🔄 Solo lo ejecuta si `event` existe
    }

    filtrarPermisos(event); // Llama a la función que carga los permisos filtrados
    ajustarVisibilidadBotonesMenu(); // Ajusta la visibilidad de los botones según los filtros
});

/***********************ROLES******************* */
console.log("🚀 Intentando cargar Menu.js...");


// ✅ Función para mostrar el formulario de roles

function mostrarFormularioRol(idRol = '', descripcion = '') {
    let contenedorFormularioRol = document.getElementById('formularioRolContainer');
    
    if (!contenedorFormularioRol) {
        contenedorFormularioRol = document.createElement('div');
        contenedorFormularioRol.id = 'formularioRolContainer';
        document.getElementById('rol-container').insertAdjacentElement('afterend', contenedorFormularioRol);
    }

    contenedorFormularioRol.innerHTML = `
        <label>Descripción del Rol:</label>
        <input type="text" id="rolDescripcion" value="${descripcion}">
        <div style="display: flex; justify-content: space-around;">
            <button id="guardarRol" type="button">Guardar</button> 
            <button id="cancelarRol" type="button">Cancelar</button>
        </div>
    `;

    // Mostrar con animación
    contenedorFormularioRol.style.display = 'block';
    contenedorFormularioRol.style.opacity = 0;
    setTimeout(() => contenedorFormularioRol.style.opacity = 1, 100);

    // Evento de guardar
    document.getElementById('guardarRol').addEventListener('click', function () {
        guardarRol(idRol);
    });

    // Evento de cancelar
    document.getElementById('cancelarRol').addEventListener('click', function (event) {
        if (event) {
            event.preventDefault();  // 🔄 Solo lo ejecuta si `event` existe
        }
    
        event.stopPropagation();
        cerrarFormularioRol(); // Cierra el formulario sin afectar la vista
    });
}

// ✅ Función para cerrar el formulario correctamente
function cerrarFormularioRol() {
    let contenedorFormularioRol = document.getElementById('formularioRolContainer');
    if (contenedorFormularioRol) {
        contenedorFormularioRol.style.opacity = 0;
        setTimeout(() => contenedorFormularioRol.style.display = 'none', 300);
    }
}


// ✅ Función para guardar un rol y actualizar la lista sin recargar la vista
function guardarRol(idRol) {
    const descripcion = document.getElementById('rolDescripcion').value;
    if (!descripcion) {
        alert('⚠️ La descripción no puede estar vacía');
        return;
    }

    const formData = new FormData();
    formData.append('descripcion', descripcion);
    if (idRol) formData.append('id_rol', idRol);

    fetch('C_Frontal.php?controlador=Menu&metodo=guardarRol', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Rol guardado correctamente');
            cerrarFormularioRol();  // Cierra el formulario después de guardar
            actualizarListaRoles(); // Recarga la lista del select sin recargar la página
        } else {
            alert('❌ Error al guardar el rol');
        }
    })
    .catch(error => console.error('Error al guardar rol:', error));
}



// ✅ Función para actualizar dinámicamente el select de roles
function actualizarListaRoles() {
    console.log("📥 Actualizando lista de roles...");
    fetch('C_Frontal.php?controlador=Menu&metodo=obtenerTodosLosRoles')
    .then(response => response.json())
    .then(roles => {
        const rolFiltro = document.getElementById('rolFiltro');
        rolFiltro.innerHTML = '<option value="">-- Selecciona Rol --</option>'; // Reiniciar opciones
        
        roles.forEach(rol => {
            const option = document.createElement('option');
            option.value = rol.id_rol;
            option.textContent = rol.rol_descripcion;
            rolFiltro.appendChild(option);
        });

        console.log("🔄 Lista de roles actualizada.");
        actualizarIconosRol(); // Actualiza los iconos de rol si es necesario
    })
    .catch(error => console.error('Error al actualizar lista de roles:', error));
}

// ✅ Función para cerrar el formulario correctamente
function cerrarFormularioRol() {
    let contenedorFormularioRol = document.getElementById('formularioRolContainer');
    if (contenedorFormularioRol) {
        contenedorFormularioRol.style.opacity = 0;
        setTimeout(() => contenedorFormularioRol.style.display = 'none', 300);
    }
}



// ✅ Función principal que inicializa los eventos
/*function intentarInicializarMenu() {
    const formularioFiltros = document.getElementById('formularioFiltros');
    const rolFiltro = document.getElementById('rolFiltro');

    if (!rolFiltro) {
        return;
    }

    console.log("✅ `rolFiltro` encontrado. Inicializando menú...");

    actualizarIconosRol();

    rolFiltro.addEventListener('change', actualizarIconosRol);

    if (formularioFiltros) {
        console.log("✅ Formulario de filtros encontrado. Configurando evento...");
        formularioFiltros.addEventListener('submit', function (event) {
            if (event) {
                event.preventDefault();  // 🔄 Solo lo ejecuta si `event` existe
            }
        
            filtrarPermisos(event);
            ajustarVisibilidadBotonesMenu();
        });
    } else {
        console.warn("❌ Formulario de filtros NO encontrado.");
    }

   // 🛑 Detenemos la comprobación una vez que se ha cargado correctamente
   if (typeof intervaloCarga !== "undefined" && intervaloCarga) {
    clearInterval(intervaloCarga);
    
    }  

}*/

// Variable global para evitar múltiples ejecuciones
let menuInicializado = false;

// ✅ Intentar inicializar solo si no se ha hecho antes
function intentarInicializarMenu() {
    const formularioFiltros = document.getElementById('formularioFiltros');
    const rolFiltro = document.getElementById('rolFiltro');

    if (!rolFiltro) {
        console.warn("⚠️ `rolFiltro` no encontrado. Reintentando...");
        return;
    }

    if (menuInicializado) {
        console.log("🔄 El menú ya está inicializado. Saliendo...");
        return;
    }

    console.log("✅ `rolFiltro` encontrado. Inicializando menú...");
    actualizarIconosRol(); // Ejecutar una sola vez

    // Evita múltiples eventos en cada filtro
    if (!rolFiltro.dataset.eventoAsignado) {
        rolFiltro.addEventListener('change', actualizarIconosRol);
        rolFiltro.dataset.eventoAsignado = "true"; // Marcar como configurado
    }

    if (formularioFiltros && !formularioFiltros.dataset.eventoAsignado) {
        console.log("✅ Formulario de filtros encontrado. Configurando evento...");
        formularioFiltros.addEventListener('submit', function (event) {
            if (event) event.preventDefault();
            filtrarPermisos(event);
            ajustarVisibilidadBotonesMenu();
        });
        formularioFiltros.dataset.eventoAsignado = "true";
    }

    menuInicializado = true; // Marcar que ya se configuró el menú
    clearInterval(intervaloCarga); // 🛑 Detener el intervalo una vez configurado
}

// ⏳ **Ejecutar `intentarInicializarMenu` hasta que el DOM tenga los elementos correctos**
const intervaloCarga = setInterval(intentarInicializarMenu, 300);


// 🔄 Ejecutar `intentarInicializarMenu` cada 300ms hasta que el DOM tenga los elementos correctos
if (typeof intervaloCarga === "undefined") {
    console.log("🚀 Configurando intervalo de carga...");
    const intervaloCarga = setInterval(intentarInicializarMenu, 300);
} else {
    console.log("⚠️ intervaloCarga ya está definido, no se volverá a crear.");
}


// ✅ Definir eliminarRol() antes de asignar eventos
function eliminarRol(idRol) {
    if (!idRol) {
        console.error("❌ No se puede eliminar: ID de rol no encontrado.");
        return;
    }

    if (!confirm('⚠️ ¿Seguro que quieres eliminar este rol? Se eliminarán también sus permisos asociados.')) return;

    console.log(`📤 Enviando solicitud para eliminar el rol con ID: ${idRol}`);

    fetch('C_Frontal.php?controlador=Menu&metodo=eliminarRol', {
        method: 'POST',
        body: new URLSearchParams({ id_rol: idRol }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    })
    .then(response => response.json())
    .then(data => {
        console.log("📥 Respuesta recibida:", data);
        if (data.success) {
            alert('✅ Rol y sus permisos eliminados correctamente.');
            actualizarListaRoles(); // Recargar la lista en el select
        } else {
            alert('❌ Error al eliminar el rol: ' + data.message);
        }
    })
    .catch(error => console.error('Error al eliminar rol:', error));
}



// ✅ Función para configurar eventos en los iconos de rol
function configurarEventosRol() {
    console.log("⚙️ Configurando eventos para los iconos de rol...");

    const addRolIcon = document.getElementById('add-rol-icon');
    const editRolIcon = document.getElementById('edit-rol-icon');
    const deleteRolIcon = document.getElementById('delete-rol-icon');

    if (addRolIcon) {
        console.log("✅ Icono de añadir detectado.");
        addRolIcon.addEventListener('click', () => mostrarFormularioRol());
    } else {
        console.warn("⚠️ No se encontró el icono de añadir.");
    }

    if (editRolIcon) {
        console.log("✅ Icono de editar detectado.");
        editRolIcon.addEventListener('click', () => 
            mostrarFormularioRol(rolFiltro.value, rolFiltro.options[rolFiltro.selectedIndex].text)
        );
    } else {
        console.warn("⚠️ No se encontró el icono de editar.");
    }

    if (deleteRolIcon) {
        console.log("✅ Icono de eliminar detectado.");
        deleteRolIcon.addEventListener('click', () => eliminarRol(rolFiltro.value));
    } else {
        console.warn("⚠️ No se encontró el icono de eliminar.");
    }

}

// ✅ Función para actualizar los iconos según el rol seleccionado
function actualizarIconosRol() {
    console.log("🔄 Ejecutando actualizarIconosRol...");
    const rolFiltro = document.getElementById('rolFiltro');
    if (!rolFiltro) return;

    const selectedRol = rolFiltro.value;
    console.log("🎯 Rol seleccionado:", selectedRol);
    let iconHtml = '';

    if (selectedRol) {
        console.log("✏️ Mostrando iconos de edición y eliminación...");
        iconHtml = `
            <img src="iconos/edit.png" id="edit-rol-icon" title="Editar rol">
            <img src="iconos/eliminar.png" id="delete-rol-icon" title="Eliminar rol">
        `;
    } else {
        console.log("➕ Mostrando icono de añadir...");
        iconHtml = `
            <img src="iconos/añadir.png" id="add-rol-icon" title="Añadir nuevo rol">
        `;
    }

    let container = document.getElementById('rol-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'rol-container';
        rolFiltro.parentNode.insertBefore(container, rolFiltro);
        container.appendChild(rolFiltro);
    }

    let iconContainer = document.getElementById('rol-icon-container');
    if (!iconContainer) {
        iconContainer = document.createElement('span');
        iconContainer.id = 'rol-icon-container';
        container.appendChild(iconContainer);
    }

    iconContainer.innerHTML = iconHtml;
    configurarEventosRol();
}

/*************ROLES ASOCIADOS*************** */
function actualizarListaRolesUsuario() {
    const usuarioId = document.getElementById('usuarioFiltro').value;
    const rolFiltro = document.getElementById('rolFiltro');
    const botonBuscar = document.querySelector('#botonesBuscar button');

    if (!usuarioId) {
        botonBuscar.textContent = "Buscar";
        botonBuscar.style.backgroundColor = ""; // 🔄 Restaurar color normal
        botonBuscar.style.display = 'inline-block';
        return;
    }

    fetch(`C_Frontal.php?controlador=Menu&metodo=obtenerRolesUsuario&id_usuario=${usuarioId}`)
        .then(response => response.json())
        .then(rolesUsuario => {
            console.log("📌 Roles asignados al usuario:", rolesUsuario);

            if (!Array.isArray(rolesUsuario)) {
                console.error("❌ Formato inesperado en rolesUsuario:", rolesUsuario);
                return;
            }

            const rolesAsignados = rolesUsuario.map(rol => String(rol.id_rol || rol));

            for (let option of rolFiltro.options) {
                if (option.value) {
                    const rolId = String(option.value);

                    option.textContent = option.textContent.replace(/^✅ |^❌ /, '').replace(/\s*\(Asignado\)|\s*\(No Asignado\)/, '');

                    if (rolesAsignados.includes(rolId)) {
                        option.textContent = `✅ ${option.textContent}`;
                        option.dataset.asignado = "true";
                    } else {
                        option.textContent = `❌ ${option.textContent}`;
                        option.dataset.asignado = "false";
                    }
                }
            }
        })
        .catch(error => console.error("❌ Error al obtener roles del usuario:", error));
}



// ✅ Función para actualizar el botón según el rol seleccionado
function actualizarBotonRol() {
    const usuarioId = document.getElementById('usuarioFiltro').value;
    const rolId = document.getElementById('rolFiltro').value;
    const rolSeleccionado = document.getElementById('rolFiltro').selectedOptions[0];
    const botonBuscar = document.querySelector('#botonesBuscar button');

    if (!usuarioId || !rolId) {
        botonBuscar.textContent = "Buscar";
        botonBuscar.onclick = () => filtrarPermisos(event);
        botonBuscar.style.backgroundColor = ""; // 🔄 Restaurar color normal
        botonBuscar.style.display = 'inline-block';
        /*document.querySelectorAll('.menu-vertical-container').forEach(element => {
            element.style.display = 'block';
        });*/
        return;
    }

    if (rolSeleccionado.dataset.asignado === "true") {
        botonBuscar.textContent = "Quitar Rol";
        botonBuscar.onclick = () => quitarRolUsuario(usuarioId, rolId);
        /*document.querySelectorAll('.menu-vertical-container').forEach(element => {
            element.style.display = 'none';
        });*/
    } else {
        botonBuscar.textContent = "Asignar Rol";
        botonBuscar.onclick = () => asignarRolUsuario(usuarioId, rolId);
        /*document.querySelectorAll('.menu-vertical-container').forEach(element => {
            element.style.display = 'none';
        });*/
    }

    botonBuscar.style.display = 'inline-block';
}

// ✅ Asignar un rol sin recargar el menú
function asignarRolUsuario(idUsuario, idRol) {
    fetch("C_Frontal.php?controlador=Menu&metodo=asignarRolUsuario", {
        method: "POST",
        body: new URLSearchParams({ id_usuario: idUsuario, id_rol: idRol }),
        headers: { "Content-Type": "application/x-www-form-urlencoded" }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("✅ Rol asignado correctamente.");
            actualizarListaRolesUsuario(); // ⚠️ Solo actualiza roles, NO el menú
            setTimeout(actualizarBotonRol, 300); 
           
        } else {
            alert("❌ Error al asignar rol.");
        }
    })
    .catch(error => console.error("Error al asignar rol:", error));
}



// ✅ Quitar un rol sin recargar el menú
function quitarRolUsuario(idUsuario, idRol) {
    fetch("C_Frontal.php?controlador=Menu&metodo=quitarRolUsuario", {
        method: "POST",
        body: new URLSearchParams({ id_usuario: idUsuario, id_rol: idRol }),
        headers: { "Content-Type": "application/x-www-form-urlencoded" }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("✅ Rol eliminado correctamente.");
            actualizarListaRolesUsuario(); // ⚠️ Solo actualiza roles, NO el menú
            setTimeout(actualizarBotonRol, 300);
            
        } else {
            alert("❌ Error al eliminar rol.");
        }
    })
    .catch(error => console.error("Error al eliminar rol:", error));
}


// Agregar eventos a los selects para actualizar automáticamente
document.getElementById('usuarioFiltro').addEventListener('change', () => {
    actualizarListaRolesUsuario();
    actualizarBotonRol();
});

document.getElementById('rolFiltro').addEventListener('change', actualizarBotonRol);

