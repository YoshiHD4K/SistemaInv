<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="src/css/styles-login.css">
</head>

<body>
    <div class="parteizquierda">
        <h1>Bienvenidos</h1>
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
        </form>
    </div>
</body>

</html>