<?php
session_start();

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "jybcomputerparts";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Inicializa el carrito si no existe
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Manejar productos seleccionados para el carrito
if (isset($_POST['productos'])) {
    $productos_ids = $_POST['productos'];
    foreach ($productos_ids as $id) {
        if (!in_array($id, $_SESSION['cart'])) {
            $_SESSION['cart'][] = $id; // Agregar producto al carrito
        }
    }
}

// Eliminar productos del carrito
if (isset($_POST['remove_product'])) {
    $remove_id = $_POST['remove_product'];
    if (($key = array_search($remove_id, $_SESSION['cart'])) !== false) {
        unset($_SESSION['cart'][$key]); // Eliminar producto del carrito
    }
}

// Obtener productos desde el carrito
$cartProducts = $_SESSION['cart'];
$productos_seleccionados = [];

if (!empty($cartProducts)) {
    $ids_string = implode(',', $cartProducts);
    $sql = "SELECT * FROM productos WHERE id IN ($ids_string)";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($producto = $result->fetch_assoc()) {
            $productos_seleccionados[] = $producto;
        }
    }
}

// Obtener productos disponibles
$sql_disponibles = "SELECT * FROM productos";
$result_disponibles = $conn->query($sql_disponibles);
$productos_disponibles = [];
if ($result_disponibles->num_rows > 0) {
    while ($producto_disponible = $result_disponibles->fetch_assoc()) {
        $productos_disponibles[] = $producto_disponible;
    }
}

// Inicializa las listas si no existen
if (!isset($_SESSION['product_lists'])) {
    $_SESSION['product_lists'] = [];
}

// Guardar lista de productos seleccionados
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['selected_products'])) {
    $selectedProducts = $_POST['selected_products'];
    $_SESSION['product_lists'][] = $selectedProducts; // Guardar la lista seleccionada
}

// Variables para pago
$mensaje_pago = "";
$total = 0;

// Calcular total del carrito
if (!empty($productos_seleccionados)) {
    foreach ($productos_seleccionados as $producto) {
        $total += $producto['precio'];
    }
}

// Procesar el pago
if (isset($_POST['payment'])) {
    $card_number = $_POST['card_number'];
    $expiration_date = $_POST['expiration_date'];
    $cvv = $_POST['cvv'];

    // Simulación de validación de tarjeta (puedes agregar lógica real aquí)
    if (preg_match('/^\d{16}$/', $card_number) && !empty($expiration_date) && preg_match('/^\d{3}$/', $cvv)) {
        $mensaje_pago = "Pago completado";
        $_SESSION['cart'] = []; // Resetear el carrito después del pago
        $total = 0; // Reiniciar el total
    } else {
        $mensaje_pago = "Error en la información de la tarjeta.";
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="ver_carrito.css">
    <style>
        body {
            background-color: #f4f4f4;
            color: #333;
            font-family: 'Arial', sans-serif;
        }

        .carrito-contenedor {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 2px solid #4CAF50;
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        h2 {
            color: #4CAF50;
            text-align: center;
        }

        .listas-productos {
            margin-bottom: 20px;
        }

        .lista-producto {
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #4CAF50;
        }

        .product-img {
            width: 50px;
            height: 50px;
            margin-right: 10px;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .back-btn, #createListBtn, .remove-btn, button[type="submit"] {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 10px 20px;
            margin: 10px 0;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .back-btn:hover, #createListBtn:hover, .remove-btn:hover, button[type="submit"]:hover {
            background-color: #45a049;
        }

        .created-lists {
            margin-top: 20px;
        }

        .payment-section {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #4CAF50;
            border-radius: 10px;
            background-color: #f9f9f9;
        }

        .message {
            color: #4CAF50;
            text-align: center;
            margin: 10px 0;
        }

        .disponibles {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #4CAF50;
            border-radius: 10px;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>

<div class="carrito-contenedor">
    <h2>Carrito de Compras</h2>

    <?php if ($mensaje_pago): ?>
        <div class="message"><?= $mensaje_pago; ?></div>
    <?php endif; ?>

    <?php if (!empty($productos_seleccionados)) : ?>
        <div class="listas-productos">
            <h3>Productos en el Carrito</h3>
            <div class="lista-producto">
                <ul>
                    <?php foreach ($productos_seleccionados as $producto) : ?>
                        <li>
                            <img src="<?= htmlspecialchars($producto['imagen']); ?>" alt="<?= htmlspecialchars($producto['nombre']); ?>" class="product-img">
                            <p><?= htmlspecialchars($producto['nombre']); ?> - $<?= htmlspecialchars($producto['precio']); ?></p>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="remove_product" value="<?= $producto['id']; ?>">
                                <button type="submit" class="remove-btn">Eliminar</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php else : ?>
        <p>No hay productos en el carrito.</p>
    <?php endif; ?>

    <h3>Total: $<?= number_format($total, 2); ?></h3>

    <div class="payment-section">
        <h2>Información de Pago</h2>
        <form method="POST">
            <label for="card_number">Número de Tarjeta:</label>
            <input type="text" name="card_number" required>

            <label for="expiration_date">Fecha de Expiración:</label>
            <input type="text" name="expiration_date" placeholder="MM/AA" required>

            <label for="cvv">CVV:</label>
            <input type="text" name="cvv" required>

            <button type="submit" name="payment">Pagar</button>
        </form>
    </div>

    <button class="back-btn" onclick="window.location.href='index.php'">Regresar a la Tienda</button>

    <h2>Productos Disponibles</h2>
    <div class="disponibles">
        <ul>
            <?php foreach ($productos_disponibles as $producto): ?>
                <li>
                    <img src="<?= htmlspecialchars($producto['imagen']); ?>" alt="<?= htmlspecialchars($producto['nombre']); ?>" class="product-img">
                    <p><?= htmlspecialchars($producto['nombre']); ?> - $<?= htmlspecialchars($producto['precio']); ?></p>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="productos[]" value="<?= $producto['id']; ?>">
                        <button type="submit" class="remove-btn">Agregar</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <h2>Listas de Productos Seleccionados</h2>
    <div class="created-lists">
        <ul>
            <?php foreach ($_SESSION['product_lists'] as $lista): ?>
                <li>
                    <?= implode(', ', $lista); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

</div>

</body>
</html>

<?php $conn->close(); ?>

