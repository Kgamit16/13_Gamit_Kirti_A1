<?php

session_start();
require_once "config/db.php";

$categoryId = filter_input(
    INPUT_GET,
    "category_id",
    FILTER_VALIDATE_INT
);

if (!$categoryId) {
    header("Location: index.php");
    exit;
}

$stmt = $conn->prepare(
    "SELECT id, name FROM categories WHERE id = ?"
);

$stmt->execute([$categoryId]);

$category = $stmt->fetch();

if (!$category) {
    http_response_code(404);
    exit("Category not found.");
}

?>

<!DOCTYPE html>

<html>
<head>
    <title>Products</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="container">

<h1>
    <?= htmlspecialchars($category["name"]) ?>
</h1>

<p><a href="index.php">Back to Categories</a></p>

<div id="product-results">
    <p>Loading products...</p>
</div>


</div>

<script>
const categoryId = <?= (int)$categoryId ?>;
</script>

<script src="assets/script.js"></script>

</body>
</html>
