<?php
session_start();

// Redirigir a index.php si el usuario ya ha iniciado sesión
if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Conexión a la base de datos
$servername = "localhost";
$username = "root"; // Cambia este valor si es necesario
$password = ""; // Cambia este valor si tienes una contraseña
$dbname = "jybcomputerparts";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Manejar el inicio de sesión
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['login'])) {
        $user = $conn->real_escape_string($_POST['username']);
        $pass = $_POST['password'];

        // Primero, verificar si el usuario es un administrador
        $sqlAdmin = "SELECT * FROM admin WHERE nombre_usuario='$user'";
        $resultAdmin = $conn->query($sqlAdmin);
        
        if ($resultAdmin->num_rows > 0) {
            $row = $resultAdmin->fetch_assoc();
            // Verificar la contraseña
            if (password_verify($pass, $row['contrasena'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['nombre_usuario'];
                $_SESSION['is_admin'] = true; // Usuario es admin
                header("Location: index.php");
                exit();
            } else {
                $login_error = "Contraseña incorrecta";
            }
        } else {
            // Si no es admin, verificar en la tabla usuarios
            $sqlUser = "SELECT * FROM usuarios WHERE nombre_usuario='$user'";
            $resultUser = $conn->query($sqlUser);
            
            if ($resultUser->num_rows > 0) {
                $row = $resultUser->fetch_assoc();
                // Verificar la contraseña
                if (password_verify($pass, $row['contrasena'])) {
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['username'] = $row['nombre_usuario'];
                    $_SESSION['is_admin'] = false; // Usuario no es admin
                    header("Location: index.php");
                    exit();
                } else {
                    $login_error = "Contraseña incorrecta";
                }
            } else {
                $login_error = "Usuario no encontrado";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión - JyB Computer Parts</title>
    <link rel="stylesheet" href="login.css"> <!-- Enlaza con el archivo CSS de inicio de sesión -->
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <a href="index.php" class="back-button">&#10094; Volver</a>
            <h1>JyB Computer Parts</h1>
            <form action="" method="POST">
                <label for="username">Usuario:</label>
                <input type="text" id="username" name="username" required>
                <br>
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
                <br>
                <button type="submit" name="login">Iniciar sesión</button>
            </form>
            <?php if (isset($login_error)) { echo "<p class='error'>$login_error</p>"; } ?>
            <p>¿No tienes cuenta? <a href="register.php">Regístrate aquí</a></p>
        </div>
    </div>
</body>
</html>

<?php
// Cerrar la conexión a la base de datos
$conn->close();
?>
