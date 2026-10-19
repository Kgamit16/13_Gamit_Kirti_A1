<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/db.php";

$categories = $conn->query(
    "SELECT COUNT(*) FROM categories"
)->fetchColumn();

$products = $conn->query(
    "SELECT COUNT(*) FROM products"
)->fetchColumn();

?>

<!DOCTYPE html>

<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="container">

```
<h1>Shopping Cart Admin Dashboard</h1>

<p>
    Welcome,
    <?= htmlspecialchars($_SESSION["admin_username"]) ?>!
</p>

<div class="stats">

    <div class="card">
        <h3>Total Categories</h3>
        <p><?= $categories ?></p>
    </div>

    <div class="card">
        <h3>Total Products</h3>
        <p><?= $products ?></p>
    </div>

</div>

<h2>Manage Shopping Site</h2>

<p>
    <a class="button" href="categories.php">
        Manage Categories
    </a>
</p>

<p>
    <a class="button" href="products.php">
        Manage Products
    </a>
</p>

<p>
    <a class="button" href="../index.php">
        View Shopping Site
    </a>
</p>

<p>
    <a class="button danger" href="logout.php">
        Admin Logout
    </a>
</p>
```

</div>

</body>
</html>
