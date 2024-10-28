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

// Obtener productos de la categoría seleccionada
$productos = [];
if (isset($_GET['categoria'])) {
    $categoriaSeleccionada = $_GET['categoria'];
    $result = $conn->query("SELECT * FROM productos WHERE categoria = '$categoriaSeleccionada'");
    $productos = $result->fetch_all(MYSQLI_ASSOC);
}

// Manejo de búsqueda de productos
$searchTerm = '';
if (isset($_POST['search_term'])) {
    $searchTerm = $_POST['search_term'];
    $result = $conn->query("SELECT * FROM productos WHERE nombre LIKE '%$searchTerm%'");
    $productos = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda de Componentes Gamer</title>
    <link rel="stylesheet" href="pc.css">
</head>
<body>
    <div class="container">
        <!-- Botón para volver a index.php -->
        <div class="back-button">
            <a href="index.php" class="button">Volver a Inicio</a>
        </div>

        <div class="search-bar">
            <form method="POST">
                <input type="text" name="search_term" placeholder="Buscar producto..." value="<?= htmlspecialchars($searchTerm) ?>">
                <button type="submit" name="search">Buscar</button>
            </form>
        </div>

        <div class="category-container">
            <div class="category-box">
                <h2>Categorías</h2>
                <?php
                $categorias = [
                    "Procesadores" => "img/procesadores.png", 
                    "Tarjetas Gráficas" => "img/tarjetas_graficas.png", 
                    "Memorias" => "img/memorias.png", 
                    "Almacenamiento" => "img/almacenamiento.png", 
                    "Fuentes de Poder" => "img/fuentes.png", 
                    "Gabinetes" => "img/gabinetes.png", 
                    "Coolers" => "img/coolers.png", 
                    "Periféricos" => "img/perifericos.png"
                ];
                
                foreach ($categorias as $categoria => $imagen) {
                    echo "<div class='category-item' onclick='toggleCategory(\"$categoria\", this)'>
                            <img src='$imagen' alt='$categoria'>
                            <span>$categoria</span>
                          </div>";
                }
                ?>
            </div>

            <div class="product-display">
                <h2>Productos</h2>
                <div id="priceCounter">
                    <p>Total: $<span id="totalPrice">0</span></p>
                </div>
                <button type="submit" class="add-to-cart" id="addToCartButton">Añadir al carrito</button>
                <form action="ver_carrito.php" method="POST" id="productForm">
                    <div id="productList">
                        <?php if (!empty($productos)): ?>
                            <?php foreach ($productos as $producto): ?>
                                <div class="product-item" style="display:none;">
                                    <img src="img/<?= $producto['nombre'] ?>.png" alt="<?= $producto['nombre'] ?>">
                                    <p><?= $producto['nombre'] ?> - $<?= $producto['precio'] ?></p>
                                    <label>
                                        <input type="checkbox" name="selected_products[]" value="<?= $producto['nombre'] ?>-<?= $producto['precio'] ?>" onchange="updateTotalPrice(this)">
                                        Seleccionar
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No hay productos disponibles para esta categoría.</p>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let activeCategory = null;

        function toggleCategory(categoria, element) {
            const productList = document.getElementById('productList');
            const currentProducts = productList.children;
            let found = false;

            for (let i = 0; i < currentProducts.length; i++) {
                if (currentProducts[i].querySelector('p').textContent.includes(categoria)) {
                    currentProducts[i].style.display = currentProducts[i].style.display === 'none' ? 'flex' : 'none';
                    found = true;
                }
            }

            if (!found) {
                // Redirigir a la misma página con la categoría seleccionada
                window.location.href = "?categoria=" + categoria;
                activeCategory = categoria;
            }
        }

        function updateTotalPrice(checkbox) {
            const totalPriceElement = document.getElementById('totalPrice');
            let totalPrice = parseFloat(totalPriceElement.innerText);

            const price = parseFloat(checkbox.value.split('-')[1]);
            if (checkbox.checked) {
                totalPrice += price;
            } else {
                totalPrice -= price;
            }
            totalPriceElement.innerText = totalPrice.toFixed(2);
        }
    </script>
</body>
</html>


