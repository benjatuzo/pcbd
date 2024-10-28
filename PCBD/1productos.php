<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "jybcomputerparts";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Manejo de la subida de imágenes
function subirImagen($imagen) {
    $target_dir = "imagenes/";
    $target_file = $target_dir . basename($imagen["name"]);
    if (move_uploaded_file($imagen["tmp_name"], $target_file)) {
        return $target_file;
    } else {
        return "default-product.jpg"; // Imagen por defecto si la subida falla
    }
}

// Agregar producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $categoria = $_POST['categoria'];
    $descripcion = $_POST['descripcion'];
    $imagen = subirImagen($_FILES['imagen']);

    $sql = "INSERT INTO productos (nombre, precio, categoria, descripcion, imagen) VALUES ('$nombre', '$precio', '$categoria', '$descripcion', '$imagen')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Nuevo producto añadido con éxito');</script>";
    } else {
        echo "<script>alert('Error: " . $sql . "<br>" . $conn->error . "');</script>";
    }
}

// Eliminar producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    $id = $_POST['product_id'];
    $sql = "DELETE FROM productos WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Producto eliminado con éxito');</script>";
    } else {
        echo "<script>alert('Error: " . $sql . "<br>" . $conn->error . "');</script>";
    }
}

// Obtener productos
$filterCategory = '';
if (isset($_POST['category_filter'])) {
    $filterCategory = $_POST['category_filter'];
    $result = $conn->query("SELECT * FROM productos WHERE categoria = '$filterCategory'");
} else {
    $result = $conn->query("SELECT * FROM productos");
}

$productos = $result->fetch_all(MYSQLI_ASSOC);

// Manejo de búsqueda
$searchTerm = '';
$noProductsFound = false; // Variable para manejar si no se encontraron productos
if (isset($_POST['search'])) {
    $searchTerm = $_POST['search_term'];
    $productos = array_filter($productos, function($producto) use ($searchTerm) {
        return stripos($producto['nombre'], $searchTerm) !== false;
    });

    // Verificar si hay productos después de la búsqueda
    if (empty($productos)) {
        $noProductsFound = true; // Cambiar el estado si no se encontraron productos
    }
}

// Manejo del carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (isset($_POST['selected_products']) && !empty($_POST['selected_products'])) {
        // Aquí puedes manejar la lógica para guardar en la sesión o en la base de datos
        // Por simplicidad, redireccionamos a la página del carrito
        header("Location: ver_carrito.php?selected_products=" . implode(',', $_POST['selected_products']));
        exit();
    } else {
        echo "<script>alert('No se seleccionó ningún producto');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda de Componentes</title>
    <link rel="stylesheet" href="1productos.css">
    <link rel="stylesheet" href="carrito.css"> <!-- CSS para la ventana del carrito -->
</head>
<body>

<div class="container">

    <!-- Botón para volver a la página de inicio -->
    <button class="back-btn" onclick="window.location.href='index.php'">Volver al Inicio</button>

    <!-- Buscador en el centro -->
    <div class="search-bar">
        <form method="POST">
            <input type="text" name="search_term" placeholder="Buscar producto..." value="<?= htmlspecialchars($searchTerm) ?>">
            <button type="submit" name="search">Buscar</button>
        </form>
    </div>
    
    <!-- Botones de Añadir y Eliminar productos en la parte superior derecha -->
    <div class="header-buttons">
        <button class="add-product-btn" id="addProductBtn">Añadir Producto</button>
        <button class="delete-product-btn" id="deleteProductBtn">Eliminar Producto</button>
        
        <!-- Botón de Carrito -->
        <button class="cart-btn" id="cartBtn">Carrito</button>
    </div>

    <!-- Filtro por categorías -->
    <div class="category-filter">
        <form method="POST">
            <h3>Filtrar por categoría</h3>
            <button type="submit" name="category_filter" value="Procesadores">Procesadores</button>
            <button type="submit" name="category_filter" value="Tarjetas Gráficas">Tarjetas Gráficas</button>
            <button type="submit" name="category_filter" value="Memorias">Memorias</button>
            <button type="submit" name="category_filter" value="Almacenamiento">Almacenamiento</button>
            <button type="submit" name="category_filter" value="Fuentes de Poder">Fuentes de Poder</button>
            <button type="submit" name="category_filter" value="Gabinetes">Gabinetes</button>
            <button type="submit" name="category_filter" value="Coolers">Coolers</button> <!-- Nuevo filtro de Coolers -->
            <button type="submit" name="category_filter" value="Periféricos">Periféricos</button> <!-- Nuevo filtro de Periféricos -->
        </form>
    </div>

    <!-- Lista de productos -->
    <div class="product-list">
        <?php if ($noProductsFound): ?>
            <p>No se encontraron productos.</p>
        <?php else: ?>
            <?php foreach ($productos as $producto) : ?>
                <div class="product-item">
                    <img src="<?= $producto['imagen']; ?>" alt="<?= $producto['nombre']; ?>" class="product-img">
                    <h3><?= $producto['nombre']; ?></h3>
                    <p class="price">$<?= $producto['precio']; ?></p>
                    <button class="show-details">❗
                        <div class="product-details" style="display: none;">
                            <p><?= $producto['descripcion']; ?></p>
                        </div>
                    </button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Modal para añadir productos -->
    <div id="addProductModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('addProductModal')">X</span>
            <form method="POST" enctype="multipart/form-data">
                <h3>Añadir Producto</h3>
                <input type="text" name="nombre" placeholder="Nombre del producto" required><br>
                <input type="number" step="0.01" name="precio" placeholder="Precio" required><br>
                <select name="categoria" required>
                    <option value="Procesadores">Procesadores</option>
                    <option value="Tarjetas Gráficas">Tarjetas Gráficas</option>
                    <option value="Memorias">Memorias</option>
                    <option value="Almacenamiento">Almacenamiento</option>
                    <option value="Fuentes de Poder">Fuentes de Poder</option>
                    <option value="Gabinetes">Gabinetes</option>
                    <option value="Coolers">Coolers</option> <!-- Opción de Coolers -->
                    <option value="Periféricos">Periféricos</option> <!-- Opción de Periféricos -->
                </select><br>
                <textarea name="descripcion" placeholder="Descripción del producto"></textarea><br>
                <input type="file" name="imagen"><br>
                <button type="submit" name="add_product">Añadir</button>
            </form>
        </div>
    </div>

    <!-- Modal para eliminar productos -->
    <div id="deleteProductModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('deleteProductModal')">X</span>
            <form method="POST">
                <h3>Eliminar Producto</h3>
                <select name="product_id" required>
                    <?php foreach ($productos as $producto) : ?>
                        <option value="<?= $producto['id']; ?>"><?= $producto['nombre']; ?></option>
                    <?php endforeach; ?>
                </select><br>
                <button type="submit" name="delete_product">Eliminar</button>
            </form>
        </div>
    </div>

    <!-- Modal del Carrito -->
    <div id="cartModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('cartModal')">X</span>
            <h3>Carrito</h3>
            <form method="POST">
                <h4>Añadir productos al carrito</h4>
                <div class="cart-items">
                    <?php foreach ($productos as $producto) : ?>
                        <div class="cart-item">
                            <input type="checkbox" name="selected_products[]" value="<?= $producto['id']; ?>">
                            <span><?= $producto['nombre']; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="submit" name="add_to_cart">Añadir al Carrito</button>
            </form>
        </div>
    </div>

</div>

<script>
    // Funciones para abrir y cerrar modales
    document.getElementById('addProductBtn').onclick = function() {
        document.getElementById('addProductModal').style.display = "block";
    };
    document.getElementById('deleteProductBtn').onclick = function() {
        document.getElementById('deleteProductModal').style.display = "block";
    };
    document.getElementById('cartBtn').onclick = function() {
        document.getElementById('cartModal').style.display = "block";
    };

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = "none";
    }
    
    // Mostrar detalles del producto
    const detailsButtons = document.querySelectorAll('.show-details');
    detailsButtons.forEach(button => {
        button.onclick = function() {
            const detailsDiv = this.querySelector('.product-details');
            detailsDiv.style.display = detailsDiv.style.display === "block" ? "none" : "block";
        };
    });
</script>

</body>
</html>
