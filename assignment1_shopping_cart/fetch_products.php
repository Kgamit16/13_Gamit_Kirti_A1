<?php

session_start();
require_once "config/db.php";

header("Content-Type: text/html; charset=UTF-8");

$categoryId = filter_input(
    INPUT_GET,
    "category_id",
    FILTER_VALIDATE_INT
);

$page = filter_input(
    INPUT_GET,
    "page",
    FILTER_VALIDATE_INT
);

if (!$categoryId || !$page || $page < 1) {
    http_response_code(400);
    exit("Invalid category or page.");
}

$check = $conn->prepare(
    "SELECT id FROM categories WHERE id = ?"
);

$check->execute([$categoryId]);

if (!$check->fetch()) {
    http_response_code(404);
    exit("Category not found.");
}

$perPage = 6;

$countStmt = $conn->prepare(
    "SELECT COUNT(*) FROM products WHERE category_id = ?"
);

$countStmt->execute([$categoryId]);

$totalProducts = (int)$countStmt->fetchColumn();

$totalPages = max(1, (int)ceil($totalProducts / $perPage));

if ($page > $totalPages) {
    $page = $totalPages;
}

$offset = ($page - 1) * $perPage;

$stmt = $conn->prepare(
    "SELECT id, pname, price, picture
     FROM products
     WHERE category_id = ?
     ORDER BY id DESC
     LIMIT $perPage OFFSET $offset"
);

$stmt->execute([$categoryId]);

$products = $stmt->fetchAll();

if (!$products) {
    echo "<p>No products available in this category.</p>";
} else {

    echo '<div class="product-grid">';

    foreach ($products as $product) {

        echo '<div class="product-card">';

        if (!empty($product["picture"])) {

            echo '<img src="uploads/' .
                rawurlencode($product["picture"]) .
                '" alt="' .
                htmlspecialchars($product["pname"], ENT_QUOTES, "UTF-8") .
                '">';

        }

        echo '<h3>' .
            htmlspecialchars($product["pname"], ENT_QUOTES, "UTF-8") .
            '</h3>';

        echo '<p>₹' .
            number_format((float)$product["price"], 2) .
            '</p>';

        echo '<form method="POST" action="add_to_cart.php">';

        echo '<input type="hidden" name="product_id" value="' .
            (int)$product["id"] . '">';

        echo '<button type="submit">Add to Cart</button>';

        echo '</form>';

        echo '</div>';
    }

    echo '</div>';
}

echo '<div class="pagination">';

for ($i = 1; $i <= $totalPages; $i++) {

    echo '<button type="button" class="page-button" data-page="' .
        $i . '">' . $i . '</button>';
}

echo '</div>';

?>
