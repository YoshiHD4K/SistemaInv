function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('collapsed');
    document.querySelector('.main-content').classList.toggle('collapsed');
}

function showScreen(screenId, link) {
    document.querySelectorAll('.pantalla').forEach(function(sec) {
        sec.classList.remove('active');
    });
    document.getElementById(screenId).classList.add('active');
    document.querySelectorAll('.sidebar ul li a').forEach(function(a) {
        a.classList.remove('active');
    });
    link.classList.add('active');
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('pantalla-dashboard').classList.add('active'); // Cambia aquí
    document.querySelectorAll('.sidebar ul li a[data-screen]').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            showScreen(this.getAttribute('data-screen'), this);
        });
    });

    // MOVER EL BLOQUE DE AUTOCOMPLETADO AQU� PARA QUE SE EJECUTE SOLO CUANDO EL DOM EST� LISTO
    const inputProducto = document.getElementById('producto_entrada');
    const divBusqueda = document.getElementById('busqueda-productos');
    let listaDesplegable = null;
    if (inputProducto && divBusqueda) {
        inputProducto.addEventListener('input', function () {
            const valor = inputProducto.value.trim();
            if (listaDesplegable) listaDesplegable.remove();
            divBusqueda.innerHTML = '';
            if (valor.length === 0) {
                return;
            }
            fetch('buscar_producto.php?term=' + encodeURIComponent(valor))
                .then(res => res.json())
                .then(productos => {
                    if (productos.error) {
                        divBusqueda.innerHTML = '<div class="error-autocompletar">' + productos.error + '</div>';
                        return;
                    }
                    if (!productos.length) {
                        return;
                    }
                    listaDesplegable = document.createElement('ul');
                    listaDesplegable.className = 'lista-productos-autocompletar';
                    productos.forEach(prod => {
                        const li = document.createElement('li');
                        li.textContent = prod.nombre;
                        li.addEventListener('mousedown', function () {
                            inputProducto.value = prod.nombre;
                            divBusqueda.innerHTML = '';
                        });
                        listaDesplegable.appendChild(li);
                    });
                    divBusqueda.appendChild(listaDesplegable);
                })
                .catch(err => {
                    divBusqueda.innerHTML = '<div class="error-autocompletar">Error de conexi�n o respuesta inv�lida</div>';
                });
        });
        document.addEventListener('mousedown', function (e) {
            if (listaDesplegable && !inputProducto.contains(e.target) && !divBusqueda.contains(e.target)) {
                divBusqueda.innerHTML = '';
            }
        });
    }

    // Autocompletado para productos en registrar salida
    const inputProductoSalida = document.getElementById('producto_salida');
    const divBusquedaSalida = document.getElementById('busqueda-productos-salida');
    let listaDesplegableSalida = null;
    if (inputProductoSalida && divBusquedaSalida) {
        inputProductoSalida.addEventListener('input', function () {
            const valor = inputProductoSalida.value.trim();
            if (listaDesplegableSalida) listaDesplegableSalida.remove();
            divBusquedaSalida.innerHTML = '';
            if (valor.length === 0) {
                return;
            }
            fetch('buscar_producto.php?term=' + encodeURIComponent(valor))
                .then(res => res.json())
                .then(productos => {
                    if (productos.error) {
                        divBusquedaSalida.innerHTML = '<div class="error-autocompletar">' + productos.error + '</div>';
                        return;
                    }
                    if (!productos.length) {
                        return;
                    }
                    listaDesplegableSalida = document.createElement('ul');
                    listaDesplegableSalida.className = 'lista-productos-autocompletar';
                    productos.forEach(prod => {
                        const li = document.createElement('li');
                        li.textContent = prod.nombre;
                        li.addEventListener('mousedown', function () {
                            inputProductoSalida.value = prod.nombre;
                            divBusquedaSalida.innerHTML = '';
                        });
                        listaDesplegableSalida.appendChild(li);
                    });
                    divBusquedaSalida.appendChild(listaDesplegableSalida);
                })
                .catch(err => {
                    divBusquedaSalida.innerHTML = '<div class="error-autocompletar">Error de conexi�n o respuesta inv�lida</div>';
                });
        });
        document.addEventListener('mousedown', function (e) {
            if (listaDesplegableSalida && !inputProductoSalida.contains(e.target) && !divBusquedaSalida.contains(e.target)) {
                divBusquedaSalida.innerHTML = '';
            }
        });
    }
});