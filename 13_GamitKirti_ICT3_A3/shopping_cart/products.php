
<?php
require_once "db.php";

// Get category ID
$categoryId = 0;

if (isset($_GET["category_id"])) {
    $categoryId = (int) $_GET["category_id"];
}

// Get page number (compatible with older PHP)
$page = 1;

if (isset($_GET["page"])) {
    $page = (int) $_GET["page"];
}

if ($page < 1) {
    $page = 1;
}

$limit = 5;
$offset = ($page - 1) * $limit;

// Count total products
$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM products
     WHERE category_id = ?"
);

$stmt->bind_param("i", $categoryId);
$stmt->execute();

$countResult = $stmt->get_result();
$countRow = $countResult->fetch_assoc();

$total = (int) $countRow["total"];

// Fetch products
$stmt = $conn->prepare(
    "SELECT id, pname, price, picture
     FROM products
     WHERE category_id = ?
     ORDER BY id DESC
     LIMIT ? OFFSET ?"
);

$stmt->bind_param("iii", $categoryId, $limit, $offset);
$stmt->execute();

$result = $stmt->get_result();

$pages = (int) ceil($total / $limit);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Products</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .products {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 25px;
        }

        .product {
            width: 200px;
            padding: 15px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
        }

        .product img {
            width: 150px;
            height: 130px;
            object-fit: contain;
        }

        .product h3 {
            color: #333;
        }

        .price {
            color: #4CAF50;
            font-weight: bold;
            font-size: 18px;
        }

        .cart-btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .cart-btn:hover {
            background-color: #45a049;
        }

        .pagination {
            text-align: center;
            margin-top: 30px;
        }

        .pagination a {
            display: inline-block;
            padding: 8px 12px;
            margin: 3px;
            background-color: white;
            color: #333;
            border: 1px solid #ccc;
            text-decoration: none;
            border-radius: 4px;
        }

        .pagination a.active,
        .pagination a:hover {
            background-color: #4CAF50;
            color: white;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: #4CAF50;
            text-decoration: none;
        }
    </style>
</head>

<body>

    <h1>Our Products</h1>

    <div class="products">

        <?php if ($result->num_rows > 0) { ?>

            <?php while ($p = $result->fetch_assoc()) { ?>

                <div class="product">

                    <?php if (!empty($p["picture"])) { ?>
                        <img
                            src="uploads/<?php
                                echo rawurlencode($p["picture"]);
                            ?>"
                            alt="Product">
                    <?php } ?>

                    <h3>
                        <?php
                        echo htmlspecialchars($p["pname"]);
                        ?>
                    </h3>

                    <p class="price">
                        Price: Rs.
                        <?php echo number_format($p["price"], 2); ?>
                    </p>

                    <a class="cart-btn"
                       href="cart.php?add=<?php echo (int) $p["id"]; ?>">
                        Add to Cart
                    </a>

                </div>

            <?php } ?>

        <?php } else { ?>

            <p>No products found in this category.</p>

        <?php } ?>

    </div>

    <div class="pagination">

        <?php
        for ($i = 1; $i <= $pages; $i++) {
            $active = "";

            if ($i == $page) {
                $active = "active";
            }

            echo '<a class="' . $active . '" href="products.php?category_id='
                . $categoryId . '&page=' . $i . '">' . $i . '</a>';
        }
        ?>

    </div>

    <a class="back" href="index.php">Back to Categories</a>

</body>
</html>
