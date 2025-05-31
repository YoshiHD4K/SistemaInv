document.addEventListener("DOMContentLoaded", function () {
    const cajero = document.querySelector(".cajero");
    const menuOpc = document.querySelector(".opc");
    const horaElemento = document.getElementById("hora");

    cajero.addEventListener("click", function (e) {
        e.stopPropagation();
        if (menuOpc.style.opacity === "1") {
            menuOpc.style.opacity = "0";
            menuOpc.style.pointerEvents = "none";
        } else {
            menuOpc.style.opacity = "1";
            menuOpc.style.pointerEvents = "auto";
        }
    });

    // Ocultar el men� si se hace clic fuera de .opc
    document.addEventListener("click", function (e) {
        if (!menuOpc.contains(e.target) && !cajero.contains(e.target)) {
            menuOpc.style.opacity = "0";
            menuOpc.style.pointerEvents = "none";
        }
    });

    // Mostrar la hora actual en el elemento con id "hora"
    function actualizarHora() {
        const ahora = new Date();
        const opcionesFecha = { year: 'numeric', month: '2-digit', day: '2-digit' };
        const fecha = ahora.toLocaleDateString('es-ES', opcionesFecha);
        const opcionesHora = { hour: '2-digit', minute: '2-digit' }; // sin segundos
        const hora = ahora.toLocaleTimeString('es-ES', opcionesHora);
        horaElemento.textContent = `${fecha} ${hora}`;
    }

    actualizarHora(); // Mostrar al cargar
    setInterval(actualizarHora, 1000); // Actualizar cada segundo

    // --- B�squeda de cliente por c�dula ---
    const formBuscarCliente = document.getElementById('formBuscarCliente');
    const cedulaClienteInput = document.getElementById('cedulaCliente');
    const tipoCedulaInput = document.getElementById('tipoCedula');
    const datosClienteDiv = document.getElementById('datosCliente');
    const clienteNombre = document.getElementById('clienteNombre');
    const clienteApellido = document.getElementById('clienteApellido');
    const clienteCedula = document.getElementById('clienteCedula');
    const clienteTelefono = document.getElementById('clienteTelefono');
    const clienteDireccion = document.getElementById('clienteDireccion');

    function limpiarDatosCliente() {
        clienteNombre.textContent = '';
        clienteApellido.textContent = '';
        clienteCedula.textContent = '';
        clienteTelefono.textContent = '';
        clienteDireccion.textContent = '';
        datosClienteDiv.querySelector('h3').textContent = 'Datos del Cliente';
    }

    // Búsqueda de cliente por cédula usando AJAX a PHP
    function buscarClientePorCedula(tipo, cedula, callback) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'buscar_cliente.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                try {
                    var cliente = JSON.parse(xhr.responseText);
                    callback(cliente);
                } catch (e) {
                    callback(null);
                }
            }
        };
        xhr.send('tipo=' + encodeURIComponent(tipo) + '&cedula=' + encodeURIComponent(cedula));
    }

    function esCedulaValida(tipo, cedula) {
        // Solo números, longitud razonable (5-9 dígitos), tipo V o E
        return (['V','E'].includes(tipo)) && /^\d{5,9}$/.test(cedula);
    }

    formBuscarCliente.addEventListener('submit', function(e) {
        e.preventDefault();
        limpiarDatosCliente();
        const tipo = tipoCedulaInput.value;
        const cedula = cedulaClienteInput.value.trim();
        if (!esCedulaValida(tipo, cedula)) {
            datosClienteDiv.style.display = 'block';
            datosClienteDiv.querySelector('h3').textContent = 'Ingrese una cédula válida';
            // Oculta los campos de datos
            Array.from(datosClienteDiv.querySelectorAll('p')).forEach(p => p.style.display = 'none');
            // Limpiar resumen de venta
            const nombreClienteResumen = document.getElementById('nombreCliente');
            const telefonoClienteResumen = document.getElementById('telefonoCliente');
            if (nombreClienteResumen) nombreClienteResumen.textContent = '';
            if (telefonoClienteResumen) telefonoClienteResumen.textContent = '';
            return;
        }
        buscarClientePorCedula(tipo, cedula, function(cliente) {
            datosClienteDiv.style.display = 'block';
            if (cliente && cliente.nombre) {
                clienteNombre.textContent = cliente.nombre;
                clienteApellido.textContent = cliente.apellido;
                clienteCedula.textContent = cliente.tipo + '-' + cliente.cedula;
                clienteTelefono.textContent = cliente.telefono;
                clienteDireccion.textContent = cliente.direccion;
                datosClienteDiv.querySelector('h3').textContent = 'Datos del Cliente';
                // Muestra los campos de datos
                Array.from(datosClienteDiv.querySelectorAll('p')).forEach(p => p.style.display = 'block');
                // Actualizar resumen de venta
                const nombreClienteResumen = document.getElementById('nombreCliente');
                const telefonoClienteResumen = document.getElementById('telefonoCliente');
                if (nombreClienteResumen) nombreClienteResumen.textContent = cliente.nombre + ' ' + cliente.apellido;
                if (telefonoClienteResumen) telefonoClienteResumen.textContent = cliente.tipo + '-' + cliente.cedula;
            } else {
                datosClienteDiv.querySelector('h3').textContent = 'Cliente no encontrado';
                // Oculta los campos de datos
                Array.from(datosClienteDiv.querySelectorAll('p')).forEach(p => p.style.display = 'none');
                // Limpiar resumen de venta
                const nombreClienteResumen = document.getElementById('nombreCliente');
                const telefonoClienteResumen = document.getElementById('telefonoCliente');
                if (nombreClienteResumen) nombreClienteResumen.textContent = '';
                if (telefonoClienteResumen) telefonoClienteResumen.textContent = '';
            }
        });
    });

    // Mostrar spancliente y sombra al hacer clic en el botón Editar de la sección .cliente
    const btnEditarClienteResumen = document.querySelector('.cliente button');
    const spanClienteDiv = document.querySelector('.spancliente');
    const sombraDiv = document.querySelector('.sombra');

    if (btnEditarClienteResumen) {
        btnEditarClienteResumen.addEventListener('click', function(e) {
            e.stopPropagation();
            spanClienteDiv.classList.add('visible');
            sombraDiv.classList.add('visible');
        });
    }

    // Ocultar spancliente y sombra al hacer clic fuera de spancliente o sobre la sombra
    function ocultarSpanCliente(e) {
        // No cerrar si el modal de agregar cliente está abierto o si se hace clic dentro del modal
        const modalAgregarCliente = document.getElementById('modalAgregarCliente');
        if (
            spanClienteDiv.classList.contains('visible') &&
            !spanClienteDiv.contains(e.target) &&
            e.target !== btnEditarClienteResumen &&
            e.target !== spanClienteDiv &&
            e.target !== sombraDiv &&
            (!modalAgregarCliente || (!modalAgregarCliente.classList.contains('visible') || !modalAgregarCliente.contains(e.target)))
        ) {
            spanClienteDiv.classList.remove('visible');
            sombraDiv.classList.remove('visible');
        }
        // Si se hace clic directamente en la sombra y el modal no está abierto
        if (e.target === sombraDiv && (!modalAgregarCliente || !modalAgregarCliente.classList.contains('visible'))) {
            spanClienteDiv.classList.remove('visible');
            sombraDiv.classList.remove('visible');
        }
    }
    document.addEventListener('mousedown', ocultarSpanCliente);

    // Botón aceptar para cerrar el spancliente
    const btnAceptarCliente = document.getElementById('btnAceptarCliente');
    if (btnAceptarCliente) {
        btnAceptarCliente.addEventListener('click', function () {
            spanClienteDiv.classList.remove('visible');
            sombraDiv.classList.remove('visible');
        });
    }

    // Mostrar modal de agregar cliente
    const btnAgregarCliente = document.getElementById('btnAgregarCliente');
    const modalAgregarCliente = document.getElementById('modalAgregarCliente');
    const btnCancelarAgregarCliente = document.getElementById('btnCancelarAgregarCliente');
    const formAgregarCliente = document.getElementById('formAgregarCliente');

    if (btnAgregarCliente && modalAgregarCliente) {
        btnAgregarCliente.addEventListener('click', function() {
            // Reinicia animación si ya estaba visible
            if (modalAgregarCliente.classList.contains('visible')) {
                modalAgregarCliente.classList.remove('visible');
                void modalAgregarCliente.offsetWidth; // Forzar reflow para reiniciar animación
            }
            modalAgregarCliente.classList.add('visible');
        });
    }
    if (btnCancelarAgregarCliente && modalAgregarCliente) {
        btnCancelarAgregarCliente.addEventListener('click', function() {
            modalAgregarCliente.classList.remove('visible');
        });
    }
    // Cerrar modal al hacer clic fuera del contenido
    if (modalAgregarCliente) {
        modalAgregarCliente.addEventListener('mousedown', function(e) {
            if (e.target === modalAgregarCliente) {
                modalAgregarCliente.classList.remove('visible');
                // Evita que se cierre el spancliente al cerrar solo el modal
                e.stopPropagation();
            }
        });
    }

    // Mostrar modal de agregar cliente en modo edición
    const btnEditarCliente = document.getElementById('btnEditarCliente');
    if (btnEditarCliente && modalAgregarCliente) {
        btnEditarCliente.addEventListener('click', function() {
            // Rellenar el formulario con los datos actuales del cliente
            document.getElementById('modalTipoCedula').value = tipoCedulaInput.value;
            document.getElementById('modalCedulaCliente').value = cedulaClienteInput.value.trim();
            document.getElementById('modalNombreCliente').value = clienteNombre.textContent.trim();
            document.getElementById('modalApellidoCliente').value = clienteApellido.textContent.trim();
            document.getElementById('modalTelefonoCliente').value = clienteTelefono.textContent.trim();
            document.getElementById('modalDireccionCliente').value = clienteDireccion.textContent.trim();
            // Bloquear tipo y cédula para edición
            document.getElementById('modalTipoCedula').disabled = true;
            document.getElementById('modalCedulaCliente').disabled = true;
            // Cambiar título y botón
            modalAgregarCliente.querySelector('h2').textContent = 'Editar Cliente';
            formAgregarCliente.setAttribute('data-modo', 'editar');
            modalAgregarCliente.classList.add('visible');
        });
    }
    // Al abrir para agregar, limpiar y desbloquear campos
    if (btnAgregarCliente && modalAgregarCliente) {
        btnAgregarCliente.addEventListener('click', function() {
            formAgregarCliente.reset();
            document.getElementById('modalTipoCedula').disabled = false;
            document.getElementById('modalCedulaCliente').disabled = false;
            // Si hay una cédula buscada, la coloca automáticamente en el modal
            document.getElementById('modalTipoCedula').value = tipoCedulaInput.value;
            document.getElementById('modalCedulaCliente').value = cedulaClienteInput.value.trim();
            modalAgregarCliente.querySelector('h2').textContent = 'Agregar Cliente';
            formAgregarCliente.setAttribute('data-modo', 'agregar');
        });
    }
    // Enviar formulario de agregar/editar cliente
    if (formAgregarCliente) {
        formAgregarCliente.addEventListener('submit', function(e) {
            e.preventDefault();
            const tipo = document.getElementById('modalTipoCedula').value;
            const cedula = document.getElementById('modalCedulaCliente').value.trim();
            const nombre = document.getElementById('modalNombreCliente').value.trim();
            const apellido = document.getElementById('modalApellidoCliente').value.trim();
            const telefono = document.getElementById('modalTelefonoCliente').value.trim();
            const direccion = document.getElementById('modalDireccionCliente').value.trim();
            if (!esCedulaValida(tipo, cedula) || !nombre || !apellido) {
                alert('Debe ingresar cédula válida, nombre y apellido para guardar.');
                return;
            }
            var modo = formAgregarCliente.getAttribute('data-modo');
            var url = modo === 'editar' ? 'editar_cliente.php' : 'agregar_cliente.php';
            var xhr = new XMLHttpRequest();
            xhr.open('POST', url, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    try {
                        var resp = JSON.parse(xhr.responseText);
                        alert(resp.msg);
                        if (resp.success) {
                            modalAgregarCliente.classList.remove('visible');
                            formAgregarCliente.reset();
                            // Si es edición, actualiza los datos en pantalla
                            if (modo === 'editar') {
                                clienteNombre.textContent = nombre;
                                clienteApellido.textContent = apellido;
                                clienteTelefono.textContent = telefono;
                                clienteDireccion.textContent = direccion;
                                clienteCedula.textContent = tipo + '-' + cedula;
                                // Actualizar resumen de venta
                                const nombreClienteResumen = document.getElementById('nombreCliente');
                                const telefonoClienteResumen = document.getElementById('telefonoCliente');
                                if (nombreClienteResumen) nombreClienteResumen.textContent = nombre + ' ' + apellido;
                                if (telefonoClienteResumen) telefonoClienteResumen.textContent = tipo + '-' + cedula;
                            }
                        }
                    } catch (e) {
                        alert('Error al procesar la respuesta del servidor.');
                    }
                }
            };
            var params =
                'tipo=' + encodeURIComponent(tipo) +
                '&cedula=' + encodeURIComponent(cedula) +
                '&nombre=' + encodeURIComponent(nombre) +
                '&apellido=' + encodeURIComponent(apellido) +
                '&telefono=' + encodeURIComponent(telefono) +
                '&direccion=' + encodeURIComponent(direccion);
            if (modo === 'editar') {
                // Para editar, también enviar el id
                var idCliente = clienteCedula.textContent.split('-')[1];
                if (idCliente) params += '&id=' + encodeURIComponent(idCliente);
            }
            xhr.send(params);
        });
    }

    // --- Autocompletado de productos ---
    const buscadorInput = document.getElementById('buscador');
    let listaDesplegable = null;

    function posicionarListaDesplegable() {
        if (!listaDesplegable || !buscadorInput) return;
        const inputRect = buscadorInput.getBoundingClientRect();
        listaDesplegable.style.top = (window.scrollY + inputRect.bottom) + 'px';
        listaDesplegable.style.left = (window.scrollX + inputRect.left) + 'px';
        listaDesplegable.style.width = inputRect.width + 'px';
    }

    if (buscadorInput) {
        buscadorInput.addEventListener('input', function () {
            const valor = buscadorInput.value.trim();
            if (valor.length === 0) {
                if (listaDesplegable) listaDesplegable.remove();
                return;
            }
            fetch(`buscar_producto.php?term=${encodeURIComponent(valor)}`)
                .then(res => res.json())
                .then(productos => {
                    if (listaDesplegable) listaDesplegable.remove();
                    if (!productos.length) return;
                    listaDesplegable = document.createElement('ul');
                    listaDesplegable.className = 'lista-productos-autocompletar';
                    productos.forEach(prod => {
                        const li = document.createElement('li');
                        li.textContent = `${prod.producto} (Disp: ${prod.cantidadDisp}) - $ ${prod.precio}`;
                        li.dataset.id = prod.id;
                        li.dataset.nombre = prod.producto;
                        li.dataset.precio = prod.precio;
                        li.dataset.cantidad = prod.cantidadDisp;
                        li.addEventListener('mousedown', function(e) {
                            buscadorInput.value = prod.producto;
                            buscadorInput.dataset.id = prod.id;
                            if (listaDesplegable) listaDesplegable.remove();
                            // Aquí puedes disparar evento para agregar a la venta, etc.
                        });
                        listaDesplegable.appendChild(li);
                    });
                    document.body.appendChild(listaDesplegable);
                    posicionarListaDesplegable();
                });
        });
        // Reposicionar al hacer scroll o resize
        window.addEventListener('scroll', posicionarListaDesplegable, true);
        window.addEventListener('resize', posicionarListaDesplegable);
        // Cerrar lista si se hace clic fuera
        document.addEventListener('mousedown', function(e) {
            if (listaDesplegable && !buscadorInput.contains(e.target) && !listaDesplegable.contains(e.target)) {
                listaDesplegable.remove();
            }
        });
    }

    // --- Lógica de productos seleccionados ---
    const tablaProductosSeleccionados = document.getElementById('tablaProductosSeleccionados');
    const montoTotalGeneral = document.getElementById('montoTotalGeneral');
    let productosSeleccionados = [];

    function renderProductosSeleccionados() {
        tablaProductosSeleccionados.innerHTML = '';
        let subtotal = 0;
        productosSeleccionados.forEach((prod, idx) => {
            const tr = document.createElement('tr');
            const total = prod.precio * prod.cantidad;
            subtotal += total;
            tr.innerHTML = `
                <td>${prod.nombre}</td>
                <td>Bs ${parseFloat(prod.precio).toFixed(2)}</td>
                <td><input type="number" min="1" max="${prod.cantidadDisp}" value="${prod.cantidad}" data-idx="${idx}" class="input-cantidad-prod"></td>
                <td>$ <span class="total-prod">${total.toFixed(2)}</span></td>
                <td><button type="button" class="btn-eliminar-prod" data-idx="${idx}">Eliminar</button></td>
            `;
            tablaProductosSeleccionados.appendChild(tr);
        });
        // Calcular IVA y total
        let iva = subtotal * 0.16;
        let totalFinal = subtotal + iva;
        // Mostrar en resumen de venta
        document.getElementById('subtotalVenta').textContent = subtotal.toFixed(2);
        document.getElementById('ivaVenta').textContent = iva.toFixed(2);
        document.getElementById('montoTotalGeneral').textContent = totalFinal.toFixed(2);
    }

    // Agregar producto desde autocompletado o Enter
    if (buscadorInput) {
        // Al seleccionar de la lista
        buscadorInput.addEventListener('input', function () {
            buscadorInput.removeAttribute('data-id');
        });
        document.addEventListener('mousedown', function(e) {
            if (listaDesplegable && listaDesplegable.contains(e.target)) {
                const li = e.target.closest('li');
                if (li && li.dataset.id) {
                    agregarProductoSeleccionado(li.dataset);
                }
            }
        });
        // Al presionar Enter
        buscadorInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && buscadorInput.dataset.id) {
                e.preventDefault();
                agregarProductoSeleccionado(buscadorInput.dataset);
            }
        });
    }

    function agregarProductoSeleccionado(data) {
        const id = data.id;
        if (!id || productosSeleccionados.some(p => p.id === id)) return;
        // Buscar info completa del producto
        fetch(`buscar_producto.php?term=${encodeURIComponent(data.nombre)}`)
            .then(res => res.json())
            .then(productos => {
                const prod = productos.find(p => p.id == id);
                if (prod) {
                    productosSeleccionados.push({
                        id: prod.id,
                        nombre: prod.producto,
                        precio: parseFloat(prod.precio),
                        cantidad: 1,
                        cantidadDisp: prod.cantidadDisp
                    });
                    renderProductosSeleccionados();
                    buscadorInput.value = '';
                    buscadorInput.removeAttribute('data-id');
                }
            });
    }

    // Cambiar cantidad o eliminar producto
    if (tablaProductosSeleccionados) {
        tablaProductosSeleccionados.addEventListener('input', function(e) {
            if (e.target.classList.contains('input-cantidad-prod')) {
                const idx = e.target.dataset.idx;
                let nuevaCantidad = parseInt(e.target.value);
                if (isNaN(nuevaCantidad) || nuevaCantidad < 1) nuevaCantidad = 1;
                if (nuevaCantidad > parseInt(productosSeleccionados[idx].cantidadDisp)) {
                    nuevaCantidad = parseInt(productosSeleccionados[idx].cantidadDisp);
                }
                productosSeleccionados[idx].cantidad = nuevaCantidad;
                renderProductosSeleccionados();
            }
        });
        tablaProductosSeleccionados.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-eliminar-prod')) {
                const idx = e.target.dataset.idx;
                productosSeleccionados.splice(idx, 1);
                renderProductosSeleccionados();
            }
        });
    }
});
