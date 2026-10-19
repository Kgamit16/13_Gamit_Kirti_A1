<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/db.php";

$categories = $conn->query(
    "SELECT * FROM categories ORDER BY name"
)->fetchAll();

$products = $conn->query(
    "SELECT p.*, c.name AS category_name
     FROM products p
     INNER JOIN categories c ON p.category_id = c.id
     ORDER BY p.id DESC"
)->fetchAll();

?>

<!DOCTYPE html>

<html>
<head>
    <title>Manage Products</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="container">

```
<h1>Manage Products</h1>

<p><a href="dashboard.php">Back to Dashboard</a></p>

<?php if (isset($_GET["message"])): ?>
    <p class="success">
        <?= htmlspecialchars($_GET["message"]) ?>
    </p>
<?php endif; ?>

<h2>Add Product</h2>

<form
    action="product_action.php"
    method="POST"
    enctype="multipart/form-data"
>

    <input type="hidden" name="action" value="add">

    <label>Product Name</label>
    <input type="text" name="pname" maxlength="150" required>

    <label>Category</label>

    <select name="category_id" required>

        <option value="">Select Category</option>

        <?php foreach ($categories as $category): ?>

            <option value="<?= $category["id"] ?>">
                <?= htmlspecialchars($category["name"]) ?>
            </option>

        <?php endforeach; ?>

    </select>

    <label>Price</label>

    <input
        type="number"
        name="price"
        min="0.01"
        step="0.01"
        required
    >

    <label>Product Picture</label>

    <input
        type="file"
        name="picture"
        accept="image/jpeg,image/png,image/webp"
        required
    >

    <button type="submit">Add Product</button>

</form>

<h2>Product List</h2>

<table>

    <tr>
        <th>ID</th>
        <th>Picture</th>
        <th>Name</th>
        <th>Category</th>
        <th>Price</th>
        <th>Update</th>
        <th>Delete</th>
    </tr>

    <?php foreach ($products as $product): ?>

    <tr>

        <td><?= $product["id"] ?></td>

        <td>
            <?php if ($product["picture"]): ?>
                <img
                    src="../uploads/<?= rawurlencode($product["picture"]) ?>"
                    width="70"
                    alt="Product"
                >
            <?php endif; ?>
        </td>

        <td>
            <?= htmlspecialchars($product["pname"]) ?>
        </td>

        <td>
            <?= htmlspecialchars($product["category_name"]) ?>
        </td>

        <td>
            ₹<?= number_format((float)$product["price"], 2) ?>
        </td>

        <td>

            <form
                action="product_action.php"
                method="POST"
                enctype="multipart/form-data"
            >

                <input type="hidden" name="action" value="update">

                <input
                    type="hidden"
                    name="id"
                    value="<?= $product["id"] ?>"
                >

                <input
                    type="text"
                    name="pname"
                    value="<?= htmlspecialchars($product["pname"]) ?>"
                    maxlength="150"
                    required
                >

                <select name="category_id" required>

                    <?php foreach ($categories as $category): ?>

                        <option
                            value="<?= $category["id"] ?>"
                            <?= (int)$category["id"] === (int)$product["category_id"] ? "selected" : "" ?>
                        >
                            <?= htmlspecialchars($category["name"]) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

                <input
                    type="number"
                    name="price"
                    value="<?= htmlspecialchars((string)$product["price"]) ?>"
                    min="0.01"
                    step="0.01"
                    required
                >

                <input
                    type="file"
                    name="picture"
                    accept="image/jpeg,image/png,image/webp"
                >

                <button type="submit">Update</button>

            </form>

        </td>

        <td>

            <form
                action="product_action.php"
                method="POST"
                onsubmit="return confirm('Delete this product?')"
            >

                <input type="hidden" name="action" value="delete">

                <input
                    type="hidden"
                    name="id"
                    value="<?= $product["id"] ?>"
                >

                <button class="danger" type="submit">
                    Delete
                </button>

            </form>

        </td>

    </tr>

    <?php endforeach; ?>

</table>
```

</div>

</body>
</html>
