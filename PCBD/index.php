<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <title>JyB Computer Parts</title>
</head>
<body>
    <?php
    session_start();

    // Verificar si se está cerrando sesión
    if (isset($_POST['logout'])) {
        session_destroy();
        header("Location: index.php");
        exit();
    }

    // Simulación de registro
    if (isset($_POST['register_admin'])) {
        $adminPassword = 'tu_contraseña_aqui';
        if ($_POST['password'] === $adminPassword) {
            $_SESSION['username'] = $_POST['username'];
            $_SESSION['is_admin'] = true;
            header("Location: index.php");
            exit();
        } else {
            $error_message = "Contraseña incorrecta.";
        }
    }

    if (isset($_SESSION['username'])) {
        $isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
        $username = $_SESSION['username'];
    } else {
        $isAdmin = false;
        $username = null;
    }

    $productos_url = $isAdmin ? '1productos.php' : 'productos.php';
    ?>

    <header>
        <div class="session-buttons">
            <?php if ($username): ?>
                <span class="username" id="username">
                    Bienvenido, <?php echo htmlspecialchars($username); ?><?php if ($isAdmin) echo ' (Admin)'; ?>
                </span>
                <form action="" method="POST" style="display: inline;">
                    <button type="submit" name="logout" class="btn" id="logout-button">Cerrar Sesión</button>
                </form>
            <?php else: ?>
                <a href="login.php" class="btn" id="login-button">Iniciar Sesión</a>
            <?php endif; ?>
        </div>
        <div class="header-title">
            <img src="ruta_al_logo.png" alt="Logo JyB" class="logo">
            <h1>JyB Computer Parts</h1>
        </div>
        <a href="ver_carrito.php" class="btn cart-button">
            <img src="guardar/carrito.png" alt="Carrito" class="cart-icon"> Carrito
        </a>
    </header>

    <main>
        <div class="buttons-container">
            <a href="<?php echo $productos_url; ?>" class="large-btn">Productos</a>
            <a href="pc.php" class="large-btn">Arma tu PC</a>
            <a href="soporte.php" class="large-btn">Soporte</a>
        </div>

        <div class="carousel-container">
            <div class="carousel">
                <div class="carousel-images">
                    <!-- Imágenes del carrusel -->
                    <img src="inteli9.png" alt="Imagen 1">
                    <img src="imagen2.jpg" alt="Imagen 2">
                    <img src="imagen3.jpg" alt="Imagen 3">
                    <img src="imagen4.jpg" alt="Imagen 4">
                    <img src="imagen5.jpg" alt="Imagen 5">
                    <img src="imagen6.jpg" alt="Imagen 6">
                    <img src="imagen7.jpg" alt="Imagen 7">
                    <img src="imagen8.jpg" alt="Imagen 8">
                    <img src="imagen9.jpg" alt="Imagen 9">
                    <img src="imagen10.jpg" alt="Imagen 10">
                </div>
                <button class="carousel-button left" onclick="moveSlide(-1)">&#10094;</button>
                <button class="carousel-button right" onclick="moveSlide(1)">&#10095;</button>
            </div>
        </div>

        <!-- Nueva barra con imagen de la empresa y redes sociales -->
        <div class="partner-bar">
            <div class="partner-box">
                <img src="ruta_a_la_imagen_empresa.png" alt="Empresa" class="partner-logo">
                <p>¡Trabajamos con!</p>
            </div>
            <div class="social-box">
                <div class="social-media">
                    <div class="social-icon">
                        <img src="ruta_a_icono_facebook.png" alt="Facebook">
                        <span>Facebook</span>
                    </div>
                    <div class="social-icon">
                        <img src="ruta_a_icono_twitter.png" alt="Twitter">
                        <span>Twitter</span>
                    </div>
                    <div class="social-icon">
                        <img src="ruta_a_icono_instagram.png" alt="Instagram">
                        <span>Instagram</span>
                    </div>
                    <div class="social-icon">
                        <img src="ruta_a_icono_linkedin.png" alt="LinkedIn">
                        <span>LinkedIn</span>
                    </div>
                </div>
                <p>¡Síguenos!</p>
            </div>
        </div>
    </main>

    <script>
        let currentSlide = 0;
        const slides = document.querySelectorAll('.carousel-images img');
        const totalSlides = slides.length;

        function updateCarousel() {
            const offset = -currentSlide * 100;
            document.querySelector('.carousel-images').style.transform = `translateX(${offset}%)`;
        }

        function moveSlide(direction) {
            currentSlide += direction;
            if (currentSlide < 0) {
                currentSlide = totalSlides - 1;
            } else if (currentSlide >= totalSlides) {
                currentSlide = 0;
            }
            updateCarousel();
        }

        setInterval(() => moveSlide(1), 5000);
    </script>

    <style>
        .header-title {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header-title h1 {
            font-size: 2.5em;
            margin: 0;
        }

        .logo {
            width: 100px;
            height: auto;
        }

        /* Estilo para el carrusel */
        .carousel {
            overflow: hidden;
            width: 80%;
            margin: 0 auto;
            position: relative;
        }

        .carousel-images {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }

        .carousel-images img {
            min-width: 100%;
            height: auto;
            max-height: 400px; /* Aumentar altura del carrusel */
        }

        .carousel-button {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            cursor: pointer;
            padding: 10px;
        }

        .carousel-button.left {
            left: -10px;
        }

        .carousel-button.right {
            right: -10px;
        }

        /* Estilo para la barra de socios */
        .partner-bar {
            display: flex;
            justify-content: space-between; /* Espaciado entre los cuadros */
            padding: 20px;
            background-color: #f2f2f2;
            position: relative;
        }

        .partner-box,
        .social-box {
            width: 45%; /* Ancho de los cuadros */
            padding: 20px;
            border: 1px solid #ccc; /* Borde para los cuadros */
            border-radius: 10px; /* Bordes redondeados */
            background-color: white; /* Color de fondo blanco */
            text-align: center; /* Centrar contenido */
            margin: 0 auto; /* Centrar los cuadros */
        }

        .partner-logo {
            width: 150px; /* Ajustar tamaño de la imagen de la empresa */
            height: auto;
        }

        .social-media {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 10px 0;
        }

        .social-icon {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .social-icon img {
            width: 40px; /* Ajustar tamaño de iconos de redes sociales */
            height: auto;
        }
    </style>
</body>
</html>
