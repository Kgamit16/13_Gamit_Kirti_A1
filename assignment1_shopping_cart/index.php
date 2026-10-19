<?php

session_start();
require_once "config/db.php";

$categories = $conn->query(
    "SELECT * FROM categories ORDER BY name"
)->fetchAll();

?>

<!DOCTYPE html>

<html>
<head>
    <title>Online Shopping Cart</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="container">

```
<header class="topbar">

    <h1>My Shopping Store</h1>

    <nav>
        <a href="index.php">Home</a>
        <a href="cart.php">Cart</a>

        <?php if (isset($_SESSION["user_id"])): ?>

            <span>
                Hello,
                <?= htmlspecialchars($_SESSION["user_name"]) ?>
            </span>

            <a href="logout.php">Logout</a>

        <?php else: ?>

            <a href="register.php">Register</a>
            <a href="login.php">Login</a>

        <?php endif; ?>

        <a href="admin/login.php">Admin</a>

    </nav>

</header>

<h2>Shop by Category</h2>

<div class="category-list">

    <?php foreach ($categories as $category): ?>

        <a
            class="category-card"
            href="products.php?category_id=<?= $category["id"] ?>"
        >
            <?= htmlspecialchars($category["name"]) ?>
        </a>

    <?php endforeach; ?>

</div>

<h2>Products</h2>

<div id="product-results">
    <p>Select a category to view products.</p>
</div>
```

</div>

<script src="assets/script.js"></script>

</body>
</html>
