function buscar(controlador, metodo, formulario, destino) {
    let opciones = { method: "GET" };
    let parametros = "controlador=" + controlador + "&metodo=" + metodo;
    parametros += '&' + new URLSearchParams(new FormData(document.getElementById(formulario))).toString();

    fetch("C_Frontal.php?" + parametros, opciones)
        .then(res => res.ok ? res.text() : Promise.reject(res.status))
        .then(vista => {
            const contenedor = document.getElementById(destino);
            contenedor.innerHTML = ""; // Limpia el contenedor antes de agregar resultados
            contenedor.innerHTML = vista; // Agrega los nuevos resultados
            configurarEventosDeslizantes(); // Configura eventos después de cargar la vista
        })
        .catch(err => console.error("Error al pedir vista de búsqueda:", err));
}
function guardarUsuario() {
    console.log('Guardando usuario...');

    let opciones = {
        method: "POST",
        body: new URLSearchParams(new FormData(document.getElementById('formularioEdicion')))
    };

    fetch("C_Frontal.php?controlador=Usuarios&metodo=guardarUsuario", opciones)
        .then(res => res.text()) // Cambia a text() para ver la respuesta completa
        .then(data => {
            console.log("Respuesta del servidor:", data); // Muestra la respuesta completa en la consola
            try {
                const resultado = JSON.parse(data); // Intenta convertir la respuesta a JSON
                const mensajeDiv = document.getElementById('mensajeResultado'); // Elemento para mostrar mensajes
                mensajeDiv.style.display = 'block'; // Asegura que el mensaje sea visible
                if (resultado.correcto === 'S') {
                    document.getElementById('capaEditarCrear').innerHTML = resultado.msj;
                    console.log("Usuario guardado correctamente.");
                    mensajeDiv.innerText = resultado.msj; // Mensaje de éxito
                    mensajeDiv.className = 'alert alert-success'; // Estilo de éxito
                    setTimeout(() => {
                        document.getElementById('capaEditarCrear').innerHTML = '';
                        mensajeDiv.style.display = 'none';
                    }, 2000);
                } else {
                    document.getElementById('msjError').innerText = resultado.msj;
                    console.error("Error al guardar usuario:", resultado.msj);
                    mensajeDiv.innerText = resultado.msj; // Mensaje de error
                    mensajeDiv.className = 'alert alert-danger'; // Estilo de error
                }
            } catch (error) {
                console.error("Error al procesar JSON:", error, "Respuesta recibida:", data);
            }
        })
        .catch(err => console.error("Error al guardar usuario:", err));
}
function validar() {
    let isValid = true;

    const fields = [
        { id: "usuario", errorId: "nombreError" },
        { id: "pass", errorId: "contrasenaError" }
    ];

    fields.forEach(field => {
        const input = document.getElementById(field.id);
        const errorMessage = document.getElementById(field.errorId);

        if (input.value.trim() === "") {
            errorMessage.style.display = "inline";
            errorMessage.innerText = "Este campo es obligatorio";
            isValid = false;
        } else {
            errorMessage.style.display = "none";
        }
    });

    if (isValid) {
        document.getElementById("formularioLogin").submit();
    }
}
function validarFormulario() {
    let isValid = true;
    const fields = [
        { id: "nombre", errorId: "nombreError", errorMessage: "Por favor ingrese su nombre." },
        { id: "apellido_1", errorId: "apellido1Error", errorMessage: "Ingrese su primer apellido." },
        { id: "login", errorId: "loginError", errorMessage: "Ingrese su login." },
        { id: "mail", errorId: "mailError", errorMessage: "Por favor ingrese un correo válido." },
        { id: "contrasena", errorId: "contrasenaError", errorMessage: "Por favor ingrese una contraseña." },
        { id: "movil", errorId: "movilError", errorMessage: "Por favor ingrese su móvil." },
        { id: "fecha_Alta", errorId: "fechaAltaError", errorMessage: "Por favor seleccione la fecha de alta." },
        { id: "estado", errorId: "estadoError", errorMessage: "Por favor seleccione un estado." }
    ];

    fields.forEach(field => {
        const input = document.getElementById(field.id);
        const errorMessage = document.getElementById(field.errorId);
        console.log(`Verificando campo: ${field.id}`, input ? `Valor: "${input.value}"` : "No encontrado");

        if (input && input.value.trim() === "") {
            errorMessage.style.display = "block";
            errorMessage.innerText = field.errorMessage;
            input.classList.add("is-invalid");
            isValid = false;
        } else if (input) {
            errorMessage.style.display = "none";
            input.classList.remove("is-invalid");
        }
    });

    // Validar opcionalmente segundo apellido si tiene un valor
    const apellido2 = document.getElementById("apellido_2");
    const segundoApellidoError = document.getElementById("segundoApellidoError");
    console.log(`Verificando campo:`+apellido2.value);

    if (apellido2 && apellido2.value.trim() !== "") {
        segundoApellidoError.style.display = "none";
        apellido2.classList.remove("is-invalid");
    } else if (apellido2 && apellido2.value.trim() === "") {
        segundoApellidoError.style.display = "none";
    }

    validarGenero();
    if (isValid) {
        guardarUsuario();
    } else {
        document.getElementById("msjError").innerText = "Por favor, complete todos los campos obligatorios.";
    }
    return isValid;
}

function validarFormularioEditar() {
    let isValid = true;
    const fields = [
        { id: "nombre", errorId: "nombreError", errorMessage: "Por favor ingrese su nombre." },
        { id: "apellido_1", errorId: "apellido1Error", errorMessage: "Ingrese su primer apellido." },
        { id: "login", errorId: "loginError", errorMessage: "Ingrese su login." },
        { id: "mail", errorId: "mailError", errorMessage: "Por favor ingrese un correo válido." },
        { id: "movil", errorId: "movilError", errorMessage: "Por favor ingrese su móvil." },
        { id: "fecha_Alta", errorId: "fechaAltaError", errorMessage: "Por favor seleccione la fecha de alta." },
        { id: "estado", errorId: "estadoError", errorMessage: "Por favor seleccione un estado." }
    ];

    fields.forEach(field => {
        const input = document.getElementById(field.id);
        const errorMessage = document.getElementById(field.errorId);

        if (input && input.value.trim() === "") {
            errorMessage.style.display = "block";
            errorMessage.innerText = field.errorMessage;
            input.classList.add("is-invalid");
            isValid = false;
        } else if (input) {
            errorMessage.style.display = "none";
            input.classList.remove("is-invalid");
        }
    });

    validarGenero();
    if (isValid) {
        guardarUsuario();
    } else {
        document.getElementById("msjError").innerText = "Por favor, complete todos los campos obligatorios.";
    }
    return isValid;
}

function validarGenero() {
    const sexoH = document.getElementById("sexoH");
    const sexoM = document.getElementById("sexoM");
    const sexoO = document.getElementById("sexoO");
    const generoError = document.getElementById("generoError");

    if (!sexoH.checked && !sexoM.checked && !sexoO.checked) {
        generoError.style.display = "block";
        generoError.innerText = "Por favor seleccione su género.";
    } else {
        generoError.style.display = "none";
    }
}

function configurarEventosDeslizantes() {
    const toggles = document.querySelectorAll('.toggle-status');

    console.log('Configurando eventos deslizantes para', toggles.length, 'elementos.');

    toggles.forEach(function (toggle) {
        toggle.addEventListener('change', function () {
            const userId = this.getAttribute('data-id');
            const isActive = this.checked ? 'S' : 'N';

            console.log('Cambio de estado detectado para usuario', userId, 'nuevo estado:', isActive);

            this.nextElementSibling.classList.toggle('toggle-active', isActive === 'S');
            this.nextElementSibling.classList.toggle('toggle-inactive', isActive === 'N');

            actualizarEstadoUsuario(userId, isActive);
        });
    });
}

function actualizarEstadoUsuario(userId, estado) {
    const formData = new URLSearchParams();
    formData.append("id_Usuario", userId);
    formData.append("activo", estado);

    fetch('C_Frontal.php?controlador=Usuarios&metodo=actualizarEstado', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: formData.toString()
    })
    .then(response => response.text()) // Cambia a text() para ver la respuesta completa, incluidos los echos
    .then(data => {
        console.log("Respuesta del servidor:", data); // Ver la respuesta en la consola
        try {
            const parsedData = JSON.parse(data); // Intenta convertir a JSON
            if (parsedData.correcto === 'S') {
                console.log('Estado actualizado correctamente en la BD.');
            } else {
                console.error('Error al actualizar el estado en la BD:', parsedData.msj);
            }
        } catch (error) {
            console.error("Error al procesar JSON:", error, "Respuesta recibida:", data);
        }
    })
    .catch(error => console.error('Error en la solicitud AJAX:', error));
}
function mostrarAdvertencia(event) {
    alert('No tienes permiso para modificar este estado.');
}