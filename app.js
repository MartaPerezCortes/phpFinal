console.log("App.js cargado correctamente");

function cargarUnScript(url) {
    if (document.querySelector(`script[src="${url}"]`)) {
        console.log(`⚠️ Script ${url} ya está cargado.`);
        return;  // 🔄 Si ya está cargado, no lo recargamos
    }

    let script = document.createElement('script');
    script.src = url;
    script.async = true;
    document.head.appendChild(script);

    console.log(`✅ Script cargado: ${url}`);
}



function obtenerVista(controlador, metodo, destino) {
    let opciones = { method: "GET" };
    let parametros = `controlador=${controlador}&metodo=${metodo}`;

    console.log(`🔄 Cargando vista: ${controlador} -> ${metodo} en ${destino}`);

    // 🛑 **Eliminar contenido anterior para evitar problemas de eventos duplicados**
    document.getElementById(destino).innerHTML = `<p style="text-align:center; color:#888;">Cargando...</p>`;

    fetch(`C_Frontal.php?${parametros}`, opciones)
        .then(res => res.ok ? res.text() : Promise.reject(res.status))
        .then(vista => {
            document.getElementById(destino).innerHTML = vista;  // Insertar nueva vista

            // 🔄 **Forzar recarga del script del controlador**
            eliminarScript(`js/${controlador}.js`);  // 💥 Elimina el script anterior
            setTimeout(() => {
                cargarUnScript(`js/${controlador}.js`);  // 🔄 Vuelve a cargar el script
            }, 100); // Pequeña espera para asegurar que el DOM ya está listo

            // 🛑 **Reconfigurar eventos del menú y permisos**
            setTimeout(() => {
                console.log("⚙️ Reconfigurando eventos del menú...");
                if (typeof configurarEventosMenuVertical === "function") {
                    configurarEventosMenuVertical();
                }
                if (typeof configurarEventosFiltros === "function") {
                    configurarEventosFiltros();
                }
                if (typeof configurarEventosPermisos === "function") {
                    configurarEventosPermisos();
                }
            }, 200); // Espera extra para asegurar que el contenido está cargado
            
           // ✅ **Forzar actualización de iconos después de la carga**
           console.log("🔄 Forzando actualización de iconos de roles...");
           if (typeof actualizarIconosRol === "function") {
               actualizarIconosRol();
           }

           // ✅ **Asegurar que los iconos de rol se actualicen dinámicamente**
           const rolFiltro = document.getElementById('rolFiltro');
           if (rolFiltro) {
               rolFiltro.addEventListener('change', () => {
                   console.log("🎯 Cambio detectado en rolFiltro, actualizando iconos...");
                   actualizarIconosRol();
                   actualizarBotonRol();
               });
           }

           // ✅ **Actualizar roles según el usuario seleccionado**
           const usuarioFiltro = document.getElementById('usuarioFiltro');
           if (usuarioFiltro) {
               usuarioFiltro.addEventListener('change', () => {
                   console.log("👤 Cambio detectado en usuarioFiltro, obteniendo roles...");
                   actualizarListaRolesUsuario();
                   actualizarBotonRol();
               });

               // 🔄 **Cargar roles iniciales del usuario si ya hay uno seleccionado**
               if (usuarioFiltro.value) {
                   actualizarListaRolesUsuario();
                   actualizarBotonRol();
               }
           }
            

        })
        .catch(err => console.error("❌ Error al pedir vista:", err));
}

// ✅ **Nueva función para eliminar scripts antes de recargarlos**
function eliminarScript(url) {
    let scripts = document.querySelectorAll(`script[src="${url}"]`);
    scripts.forEach(script => script.remove());
}


function obtenerVista_EditarCrear(controlador, metodo, destino, id) {
    let opciones = { method: "GET" };
    let parametros = `controlador=${controlador}&metodo=${metodo}&id=${id}`;

    fetch(`C_Frontal.php?${parametros}`, opciones)
        .then(res => res.ok ? res.text() : Promise.reject(res.status))
        .then(vista => {
            document.getElementById(destino).innerHTML = vista;
            // Cargar el archivo JavaScript específico del controlador
            cargarUnScript(`js/${controlador}.js`);
        })
        .catch(err => console.error("Error al pedir vista de edición:", err));
}

// Manejo de errores visuales
function mostrarError(mensaje) {
    const errorDiv = document.getElementById("formError");
    errorDiv.textContent = mensaje;
    errorDiv.style.display = "block";
}

function ocultarErrores() {
    const errorDiv = document.getElementById("formError");
    errorDiv.style.display = "none";
    errorDiv.textContent = "";
}

// Eventos globales al cargar la página
document.addEventListener("DOMContentLoaded", function () {
    console.log("App.js: DOMContentLoaded activado");
});
