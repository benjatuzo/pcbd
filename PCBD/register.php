<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root"; // Cambia este valor si es necesario
$password = ""; // Cambia este valor si tienes una contraseña
$dbname = "jybcomputerparts"; // Cambiado a la base de datos correcta

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Manejar el registro
session_start();
$admin_password = "1234"; // Cambia esta contraseña según lo desees
$show_admin_form = false; // Variable para controlar la visualización del formulario de admin

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['register'])) {
        $user = $conn->real_escape_string($_POST['username']);
        $pass = $_POST['password'];
        $pass_hashed = password_hash($pass, PASSWORD_BCRYPT);

        // Verificar si el usuario ya existe en la tabla usuarios
        $sql_check = "SELECT * FROM usuarios WHERE nombre_usuario='$user'";
        $result = $conn->query($sql_check);

        if ($result->num_rows > 0) {
            $register_error = "El nombre de usuario ya existe. Por favor, elige otro.";
        } else {
            // Insertar nuevo usuario en la tabla usuarios
            $sql_insert = "INSERT INTO usuarios (nombre_usuario, contrasena) VALUES ('$user', '$pass_hashed')";
            if ($conn->query($sql_insert) === TRUE) {
                $_SESSION['user_id'] = $conn->insert_id;
                $_SESSION['username'] = $user;
                header("Location: index.php"); // Redirige a la página principal
                exit();
            } else {
                $register_error = "Error al registrar: " . $conn->error;
            }
        }
    } elseif (isset($_POST['admin_password_submit'])) {
        // Verificar la contraseña del admin
        if ($_POST['admin_password'] === $admin_password) {
            $show_admin_form = true; // Muestra el formulario de registro de admin
        } else {
            $register_error = "Contraseña de administrador incorrecta.";
        }
    } elseif (isset($_POST['admin_register'])) {
        // Manejar registro como admin
        $admin_user = $conn->real_escape_string($_POST['admin_username']);
        $admin_pass = $_POST['admin_password'];
        $admin_pass_hashed = password_hash($admin_pass, PASSWORD_BCRYPT);

        // Insertar nuevo administrador en la tabla admin
        $sql_insert = "INSERT INTO admin (nombre_usuario, contrasena) VALUES ('$admin_user', '$admin_pass_hashed')";
        if ($conn->query($sql_insert) === TRUE) {
            $_SESSION['user_id'] = $conn->insert_id;
            $_SESSION['username'] = $admin_user;
            $_SESSION['is_admin'] = true; // Marca al usuario como admin
            header("Location: index.php"); // Redirige a la página principal
            exit();
        } else {
            $register_error = "Error al registrar admin: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - JyB Computer Parts</title>
    <link rel="stylesheet" href="register.css"> <!-- Enlaza con el archivo CSS de registro -->
    <script>
        function toggleAdminForm() {
            document.getElementById("admin-form").style.display = "block";
            document.getElementById("admin-password-form").style.display = "none";
        }
    </script>
</head>
<body>
    <div class="register-container">
        <div class="register-box">
            <h1>Registrar - JyB Computer Parts</h1>
            <form action="" method="POST">
                <label for="username">Usuario:</label>
                <input type="text" id="username" name="username" required>
                <br>
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
                <br>
                <button type="submit" name="register">Registrarse</button>
                <button type="button" onclick="document.getElementById('admin-password-form').style.display='block'">Registrarse como Admin</button>
            </form>
            <?php if (isset($register_error)) { echo "<p class='error'>$register_error</p>"; } ?>

            <!-- Formulario para la contraseña del administrador -->
            <div id="admin-password-form" style="display:none;">
                <h2>Ingrese la contraseña de administrador</h2>
                <form action="" method="POST">
                    <label for="admin_password">Contraseña:</label>
                    <input type="password" id="admin_password" name="admin_password" required>
                    <button type="submit" name="admin_password_submit">Aceptar</button>
                </form>
            </div>

            <!-- Formulario para registrar como administrador -->
            <?php if ($show_admin_form) { ?>
                <div id="admin-form">
                    <h2>Registrar como Administrador</h2>
                    <form action="" method="POST">
                        <label for="admin_username">Usuario Admin:</label>
                        <input type="text" id="admin_username" name="admin_username" required>
                        <br>
                        <label for="admin_password">Contraseña:</label>
                        <input type="password" id="admin_password" name="admin_password" required>
                        <br>
                        <button type="submit" name="admin_register">Registrarse como Admin</button>
                    </form>
                </div>
            <?php } ?>

            <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
        </div>
    </div>
</body>
</html>

<?php
// Cerrar la conexión a la base de datos
$conn->close();
?>
