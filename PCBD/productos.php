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
$noProductsFound = false;
if (isset($_POST['search'])) {
    $searchTerm = $_POST['search_term'];
    $productos = array_filter($productos, function($producto) use ($searchTerm) {
        return stripos($producto['nombre'], $searchTerm) !== false;
    });

    if (empty($productos)) {
        $noProductsFound = true;
    }
}

// Manejo del carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $producto_id = $_POST['product_id'];
    $cantidad = 1;

    $stmt = $conn->prepare("INSERT INTO carrito (producto_id, cantidad) VALUES (?, ?)");
    $stmt->bind_param("ii", $producto_id, $cantidad);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Producto añadido al carrito');</script>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda de Componentes</title>
    <link rel="stylesheet" href="productos.css">
    <style>
        .product-item {
            position: relative;
            margin-bottom: 20px; /* Añade espacio entre productos */
        }
        .add-to-cart-btn {
            padding: 4px 8px;
            font-size: 12px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 10px; /* Espaciado entre botones */
        }
        .category-filter button {
            padding: 10px 15px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            background-color: #f0f0f0;
            color: #333;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .category-filter button:hover {
            background-color: #007bff;
            color: #fff;
        }
        .coolers-btn {
            background-color: #f2dede;
            color: #a94442;
        }
        .perifericos-btn {
            background-color: #d9edf7;
            color: #31708f;
        }
        .coolers-btn:hover, .perifericos-btn:hover {
            background-color: #007bff;
            color: #fff;
        }
    </style>
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
            <button type="submit" name="category_filter" value="Coolers" class="coolers-btn">Coolers</button>
            <button type="submit" name="category_filter" value="Periféricos" class="perifericos-btn">Periféricos</button>
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
                    
                    <!-- Botón "Añadir al carrito" separado -->
                    <form method="POST" style="display:inline-block;">
                        <input type="hidden" name="product_id" value="<?= $producto['id']; ?>">
                        <button type="submit" name="add_to_cart" class="add-to-cart-btn">Añadir</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

<script>
    document.querySelectorAll('.show-details').forEach(button => {
        button.onclick = function() {
            const details = this.querySelector('.product-details');
            details.style.display = details.style.display === "none" ? "block" : "none";
        };
    });
</script>

</body>
</html>
