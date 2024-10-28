<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soporte</title>
    <link rel="stylesheet" href="soporte.css">
</head>
<body>
<header>
    <div class="button-container">
        <button class="rounded-button" onclick="location.href='index.php'">Inicio</button>
        <button class="rounded-button" onclick="location.href='productos.php'">Productos</button>
        <button class="rounded-button" onclick="location.href='pc.php'">Armar PC</button>
        <button class="rounded-button" onclick="location.href='soporte.php'">Soporte</button>
    </div>
</header>

<main>
    <h1>Soporte</h1>
    <p>Si tienes algún problema, contáctate con nosotros:</p>

    <form action="" method="POST">
        <label for="email">Correo electrónico:</label>
        <input type="email" id="email" name="email" value="correo@ejemplo.com" readonly>

        <label for="phone">Número de teléfono:</label>
        <input type="text" id="phone" name="phone" value="123456789" readonly>
    </form>
</main>

</body>
</html>
