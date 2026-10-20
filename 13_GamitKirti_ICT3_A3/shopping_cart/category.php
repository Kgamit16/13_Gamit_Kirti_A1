
<?php
require_once "db.php";

// Get all categories
$categories = $conn->query("SELECT * FROM categories");

// Get selected category ID
$categoryId = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Shopping Cart - Categories</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .container {
            width: 100%;
            max-width: 1050px;
            margin: 20px auto;
            padding: 25px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            color: #333;
            font-size: 32px;
            margin: 5px 0 30px;
        }

        h2 {
            color: #4CAF50;
            font-size: 26px;
            margin: 25px 0 20px;
        }

        .categories {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin-bottom: 25px;
        }

        .categories a {
            display: inline-block;
            padding: 10px 16px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.2s;
        }

        .categories a:hover {
            background-color: #388e3c;
        }

        hr {
            border: none;
            border-top: 1px solid #ddd;
            margin: 25px 0;
        }

        /* Products area */
        #products {
            width: 100%;
            display: block;
            margin: 20px auto;
            text-align: center;
        }

        /* Products heading returned by products.php */
        #products h1,
        #products h2 {
            display: block;
            width: 100%;
            text-align: center;
            color: #333;
            font-size: 30px;
            margin: 20px 0 28px;
        }

        /* Container for product cards */
        #products .products-container,
        #products .product-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: stretch;
            gap: 20px;
            width: 100%;
            margin: 0 auto;
        }

        /* Individual product card */
        #products .product {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            width: 220px;
            min-height: 300px;
            margin: 0;
            padding: 18px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 9px;
            text-align: center;
            vertical-align: top;
            transition: box-shadow 0.2s, transform 0.2s;
        }

        #products .product:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.10);
            transform: translateY(-2px);
        }

        #products .product img {
            display: block;
            width: 100%;
            max-width: 180px;
            height: 125px;
            margin: 0 auto 12px;
            object-fit: contain;
        }

        #products .product h3 {
            color: #333;
            font-size: 20px;
            margin: 12px 0;
            overflow-wrap: anywhere;
        }

        #products .product p {
            color: #4CAF50;
            font-size: 18px;
            font-weight: bold;
            margin: 8px 0 18px;
        }

        #products .product a {
            display: inline-block;
            margin-top: auto;
            padding: 10px 14px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
            border: none;
        }

        #products .product a:hover {
            background-color: #388e3c;
        }

        #products .product button {
            padding: 10px 14px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        #products .product button:hover {
            background-color: #388e3c;
        }

        /* Pagination */
        #products .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            width: 100%;
            margin: 30px auto;
            text-align: center;
        }

        #products .pagination button,
        #products button[data-page],
        #products .pagination a {
            display: inline-block;
            padding: 10px 14px;
            margin: 0;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
            font-size: 16px;
        }

        #products .pagination button:hover,
        #products button[data-page]:hover,
        #products .pagination a:hover {
            background-color: #388e3c;
        }

        /* Back to categories link, if present */
        #products .back-categories {
            display: block;
            width: 100%;
            margin: 25px auto;
            color: #4CAF50;
            text-align: center;
            text-decoration: none;
        }

        /* Bottom navigation */
        .navigation {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin: 35px 0 5px;
        }

        .navigation a {
            display: inline-block;
            padding: 12px 17px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.2s;
        }

        .navigation a:hover {
            background-color: #388e3c;
        }

        /* Loading and error messages */
        .loading-message {
            padding: 25px;
            text-align: center;
            color: #666;
        }

        /* Mobile layout */
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }

            .container {
                margin: 10px auto;
                padding: 15px;
            }

            h1 {
                font-size: 26px;
            }

            h2 {
                font-size: 23px;
            }

            #products h1,
            #products h2 {
                font-size: 25px;
            }

            #products .product {
                width: 100%;
                max-width: 280px;
            }

            .navigation {
                gap: 8px;
            }

            .navigation a {
                padding: 10px 12px;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <h1>Online Shopping</h1>

    <h2>Categories</h2>

    <div class="categories">
        <?php
        if ($categories) {
            while ($c = $categories->fetch_assoc()) {
                $id = (int)$c["id"];
                $name = htmlspecialchars(
                    $c["name"],
                    ENT_QUOTES,
                    "UTF-8"
                );

                echo '<a href="category.php?id=' . $id . '">'
                    . $name . '</a>';
            }
        }
        ?>
    </div>

    <hr>

    <h2>Products</h2>

    <div id="products">
        <p class="loading-message">Loading products...</p>
    </div>

    <div class="navigation">
        <a href="index.php">Home</a>
        <a href="cart.php">View Cart</a>
        <a href="login.php">User Login</a>
        <a href="register.php">Register</a>
    </div>

</div>

<script>
var categoryId = <?php echo $categoryId; ?>;

function loadProducts(page) {
    if (!page) {
        page = 1;
    }

    var products = document.getElementById("products");

    if (categoryId <= 0) {
        products.innerHTML =
            '<p class="loading-message">Please select a category.</p>';
        return;
    }

    var xhr = new XMLHttpRequest();

    xhr.open(
        "GET",
        "products.php?category_id=" + categoryId +
        "&page=" + page,
        true
    );

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                products.innerHTML = xhr.responseText;
            } else {
                products.innerHTML =
                    '<p class="loading-message">' +
                    'Unable to load products. Please try again.' +
                    '</p>';
            }
        }
    };

    xhr.onerror = function () {
        products.innerHTML =
            '<p class="loading-message">Connection error. Please try again.</p>';
    };

    xhr.send();
}

/* Handle pagination */
document.addEventListener("click", function (e) {
    var target = e.target;

    while (target && target !== document) {
        if (target.getAttribute &&
            target.getAttribute("data-page") !== null) {

            e.preventDefault();

            var page = parseInt(
                target.getAttribute("data-page"),
                10
            );

            if (!isNaN(page)) {
                loadProducts(page);
            }

            return;
        }

        target = target.parentNode;
    }
});

/* Load products when the page opens */
loadProducts(1);
</script>

</body>
</html>
