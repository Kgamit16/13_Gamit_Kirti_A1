
<?php
require_once "db.php";

// Create cart session
if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = array();
}

// Add product to cart
if (isset($_GET["add"])) {
    $id = (int) $_GET["add"];

    $stmt = $conn->prepare(
        "SELECT id FROM products WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {

        if (isset($_SESSION["cart"][$id])) {
            $_SESSION["cart"][$id] =
                $_SESSION["cart"][$id] + 1;
        } else {
            $_SESSION["cart"][$id] = 1;
        }
    }

    header("Location: cart.php");
    exit;
}

// Remove product from cart
if (isset($_GET["remove"])) {
    $removeId = (int) $_GET["remove"];

    unset($_SESSION["cart"][$removeId]);

    header("Location: cart.php");
    exit;
}

$total = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shopping Cart</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 750px;
            margin: 40px auto;
            padding: 25px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        .total {
            text-align: right;
            color: #4CAF50;
            margin-top: 20px;
        }

        .remove {
            color: white;
            background-color: #e53935;
            padding: 7px 10px;
            text-decoration: none;
            border-radius: 4px;
        }

        .remove:hover {
            background-color: #c62828;
        }

        .btn {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .btn:hover {
            background-color: #45a049;
        }

        .empty {
            text-align: center;
            color: #777;
            padding: 20px;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Shopping Cart</h2>

    <?php if (!empty($_SESSION["cart"])) { ?>

        <table>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>

            <?php
            foreach ($_SESSION["cart"] as $id => $qty) {

                $stmt = $conn->prepare(
                    "SELECT pname, price FROM products WHERE id = ?"
                );

                $stmt->bind_param("i", $id);
                $stmt->execute();

                $p = $stmt->get_result()->fetch_assoc();

                if (!$p) {
                    continue;
                }

                $qty = (int) $qty;
                $subtotal = $p["price"] * $qty;
                $total += $subtotal;
            ?>

                <tr>
                    <td>
                        <?php
                        echo htmlspecialchars($p["pname"]);
                        ?>
                    </td>

                    <td>
                        Rs. <?php
                        echo number_format($p["price"], 2);
                        ?>
                    </td>

                    <td>
                        <?php echo $qty; ?>
                    </td>

                    <td>
                        Rs. <?php
                        echo number_format($subtotal, 2);
                        ?>
                    </td>

                    <td>
                        <a class="remove"
                           href="cart.php?remove=<?php echo (int) $id; ?>">
                            Remove
                        </a>
                    </td>
                </tr>

            <?php } ?>

        </table>

        <h3 class="total">
            Total: Rs. <?php echo number_format($total, 2); ?>
        </h3>

    <?php } else { ?>

        <p class="empty">Your shopping cart is empty.</p>

    <?php } ?>

    <a class="btn" href="index.php">Continue Shopping</a>

</div>

</body>
</html>
