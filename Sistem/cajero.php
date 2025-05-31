<?php
session_start();
if (!isset($_SESSION['tipo'])) {
    // Si no hay sesi�n iniciada, redirigir a la p�gina de inicio de sesi�n
    echo "<script>
            alert('Acceso denegado, inicie sesi�n primero.');
            window.location.href = 'index.php';
          </script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina Principal - SistemInv</title>
    <link rel="stylesheet" href="src/css/cajero.css">
    <script src="src/js/cajero.js"></script>
</head>

<body>
    <div class="sombra"></div>
    <div class="spancliente">
        <h1>Búsqueda de Clientes</h1>
        <form id="formBuscarCliente">
            <div class="form-busqueda-cliente">
                <select id="tipoCedula">
                    <option value="V">V</option>
                    <option value="E">E</option>
                </select>
                <input type="text" id="cedulaCliente" placeholder="Ingrese cédula">
                <button type="submit">Buscar</button>
            </div>
        </form>
        <div id="datosCliente">
            <h3>Datos del Cliente</h3>
            <p><strong>Nombre: </strong> <span id="clienteNombre"></span></p>
            <p><strong>Apellido: </strong> <span id="clienteApellido"></span></p>
            <p><strong>Cédula: </strong> <span id="clienteCedula"></span></p>
            <p><strong>Teléfono: </strong> <span id="clienteTelefono"></span></p>
            <p><strong>Dirección: </strong> <span id="clienteDireccion"></span></p>
        </div>
        <div class="acciones-cliente">
            <button id="btnAgregarCliente" type="button">Agregar Cliente</button>
            <button id="btnEditarCliente" type="button">Editar Cliente</button>
        </div>
        <div class="acciones-aceptar">
            <button id="btnAceptarCliente" type="button">Aceptar</button>
        </div>
    </div>
    <div class="cabecera">
        <div class="caja">
            <p>Caja #2</p>
        </div>
        <div class="cajero">
            <img src="src/images/person.svg">
            <p><?php echo htmlspecialchars($_SESSION['usuario']); ?></p>
        </div>
        <div class="hora">
            <p id="hora"></p>
        </div>
    </div>
    <div class="opc">
        <div class="opcion">Opciones</div>
        <div class="opcion">Cerrar Sesión</div>
    </div>
    <div class="busqueda">
        <div class="buscador">
            <input type="text" id="buscador" placeholder="Buscar producto...">
            <button id="buscarBtn">Buscar</button>
        </div>
    </div>
    <div class="resumen">
        <div class="resumendeventa">
            <h1>Resumen de Venta</h1>
        </div>
        <div class="cliente">
            <h2><strong>Cliente</strong></h2>
            <div class="datoscliente">
                <p>Nombre: <span id="nombreCliente"></span></p>
                <p><span id="telefonoCliente"></span></p>
            </div>
            <button>Editar</button>
        </div>
        <div class="resumen-venta-totales">
            <div><strong>Subtotal: $ <span id="subtotalVenta">0.00</span></strong></div>
            <div><strong>IVA (16%): $ <span id="ivaVenta">0.00</span></strong></div>
            <div><strong>Total a Pagar: $ <span id="montoTotalGeneral">0.00</span></strong></div>
        </div>
    </div>
    <div class="productos-seleccionados" id="productosSeleccionados">
        <h2>Productos Seleccionados</h2>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="tablaProductosSeleccionados">
                <!-- Aquí se agregan los productos seleccionados dinámicamente -->
            </tbody>
        </table>
    </div>
    <div class="modal-agregar-cliente" id="modalAgregarCliente">
        <div class="modal-contenido">
            <h2>Agregar Cliente</h2>
            <form id="formAgregarCliente">
                <div class="form-group">
                    <label for="modalTipoCedula">Tipo:</label>
                    <select id="modalTipoCedula" required>
                        <option value="V">V</option>
                        <option value="E">E</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="modalCedulaCliente">Cédula:</label>
                    <input type="text" id="modalCedulaCliente" required>
                </div>
                <div class="form-group">
                    <label for="modalNombreCliente">Nombre:</label>
                    <input type="text" id="modalNombreCliente" required>
                </div>
                <div class="form-group">
                    <label for="modalApellidoCliente">Apellido:</label>
                    <input type="text" id="modalApellidoCliente" required>
                </div>
                <div class="form-group">
                    <label for="modalTelefonoCliente">Teléfono:</label>
                    <input type="text" id="modalTelefonoCliente">
                </div>
                <div class="form-group">
                    <label for="modalDireccionCliente">Dirección:</label>
                    <input type="text" id="modalDireccionCliente">
                </div>
                <div class="modal-acciones">
                    <button type="submit">Agregar</button>
                    <button type="button" id="btnCancelarAgregarCliente">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>