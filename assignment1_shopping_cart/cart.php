<?php

session_start();
require_once "config/db.php";

$cart = $_SESSION["cart"] ?? [];

$products = [];
$total = 0;

if (!empty($cart)) {

    $ids = [];

    foreach ($cart as $id => $quantity) {

        if (filter_var($id, FILTER_VALIDATE_INT) && $quantity > 0) {
            $ids[] = (int)$id;
        }
    }

    if ($ids) {

        $placeholders = implode(",", array_fill(0, count($ids), "?"));

        $stmt = $conn->prepare(
            "SELECT id, pname, price, picture
             FROM products
             WHERE id IN ($placeholders)"
        );

        $stmt->execute($ids);

        $products = $stmt->fetchAll();
    }
}

?>

<!DOCTYPE html>

<html>
<head>
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="container">

```
<h1>My Shopping Cart</h1>

<p><a href="index.php">Continue Shopping</a></p>

<?php if (!isset($_SESSION["user_id"])): ?>

    <p>Please <a href="login.php">log in</a> to add products to your cart.</p>

<?php elseif (!$products): ?>

    <p>Your cart is empty.</p>

<?php else: ?>

    <table>

        <tr>
            <th>Picture</th>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
        </tr>

        <?php foreach ($products as $product): ?>

            <?php

            $quantity = max(
                1,
                (int)($cart[$product["id"]] ?? 1)
            );

            $subtotal = (float)$product["price"] * $quantity;
            $total += $subtotal;

            ?>

            <tr>

                <td>
                    <?php if ($product["picture"]): ?>

                        <img
                            src="uploads/<?= rawurlencode($product["picture"]) ?>"
                            width="60"
                            alt="Product"
                        >

                    <?php endif; ?>
                </td>

                <td>
                    <?= htmlspecialchars($product["pname"]) ?>
                </td>

                <td>
                    ₹<?= number_format((float)$product["price"], 2) ?>
                </td>

                <td><?= $quantity ?></td>

                <td>
                    ₹<?= number_format($subtotal, 2) ?>
                </td>

            </tr>

        <?php endforeach; ?>

        <tr>
            <th colspan="4">Grand Total</th>

            <th>
                ₹<?= number_format($total, 2) ?>
            </th>
        </tr>

    </table>

    <p>
        This practical demonstrates cart calculations.
        Payment and order placement are not implemented.
    </p>

<?php endif; ?>
```

</div>

</body>
</html>
