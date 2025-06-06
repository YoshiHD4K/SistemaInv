<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="src/css/styles-login.css">
    <style>
        .btn {
            color: #000 !important;
            /* Letras negras */
            font-weight: bold;
            transition: background 0.2s, color 0.2s, transform 0.18s cubic-bezier(.4, 2, .6, 1);
            /* Sombra y profundidad opcional */
            box-shadow: 0 4px 16px rgba(66, 107, 150, 0.10);
        }

        .btn:hover,
        .btn:focus {
            color: #000 !important;
            background: #f9d923;
            /* Movimiento frontal y ligeramente ascendente, sin rotación */
            transform: translateY(-8px) scale(1.08);
            box-shadow: 0 16px 32px rgba(66, 107, 150, 0.22);
        }

        .btn-small {
            font-size: 110%;
            padding: 7px 18px;
        }
    </style>
</head>

<body>
    <div class="parteizquierda">
        <h1>Bienvenido</h1>
    </div>
    <div class="partederecha">
        <form action="login.php" method="post">
            <img src="src\images\StockWise no bg.png" alt="StockWise no bg"
                style="display:block; margin:0 auto 0px auto; max-width:180px;">
            <h1>Iniciar Sesión</h1>
            <div class="textInputWrapper">
                <input placeholder="Usuario" type="text" class="textInput" name="Usuario" required>
            </div>
            <div class="textInputWrapper">
                <input placeholder="Contraseña" type="password" class="textInput" name="Contraseña" required>
            </div>
            <a href="forgotpassword.php" class="span">Olvide mi contraseña</a>
            <button class="btn" type="submit"> Iniciar Sesión </button>
            <button class="btn btn-small" type="button" onclick="window.location.href='http://localhost/SistemaInv'">Regresar al inicio</button>
        </form>
    </div>
</body>

</html>