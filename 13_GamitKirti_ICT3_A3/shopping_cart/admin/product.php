
<?php
require_once "../db.php";

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}

// Resize uploaded image
function resizeImage($source, $destination, $mime) {
    if ($mime === "image/jpeg") {
        $img = @imagecreatefromjpeg($source);
    } elseif ($mime === "image/png") {
        $img = @imagecreatefrompng($source);
    } elseif ($mime === "image/webp" &&
              function_exists("imagecreatefromwebp")) {
        $img = @imagecreatefromwebp($source);
    } else {
        return false;
    }

    if (!$img) {
        return false;
    }

    $w = imagesx($img);
    $h = imagesy($img);

    if ($w <= 0 || $h <= 0) {
        imagedestroy($img);
        return false;
    }

    $scale = min(300 / $w, 300 / $h, 1);
    $nw = max(1, (int) ($w * $scale));
    $nh = max(1, (int) ($h * $scale));

    $new = imagecreatetruecolor($nw, $nh);

    if ($mime === "image/png" || $mime === "image/webp") {
        imagealphablending($new, false);
        imagesavealpha($new, true);

        $transparent = imagecolorallocatealpha(
            $new, 0, 0, 0, 127
        );

        imagefilledrectangle(
            $new, 0, 0, $nw, $nh, $transparent
        );
    }

    imagecopyresampled(
        $new, $img, 0, 0, 0, 0,
        $nw, $nh, $w, $h
    );

    $ok = false;

    if ($mime === "image/jpeg") {
        $ok = imagejpeg($new, $destination, 85);
    } elseif ($mime === "image/png") {
        $ok = imagepng($new, $destination);
    } elseif ($mime === "image/webp" &&
              function_exists("imagewebp")) {
        $ok = imagewebp($new, $destination, 85);
    }

    imagedestroy($img);
    imagedestroy($new);

    return $ok;
}

$message = "";

// Add product
if (isset($_POST["add"])) {
    $category = (int) $_POST["category_id"];
    $name = trim($_POST["pname"]);
    $price = (float) $_POST["price"];
    $picture = "";

    if ($category <= 0 || $name === "" || $price < 0) {
        die("Invalid product details.");
    }

    // Check category
    $check = $conn->prepare(
        "SELECT id FROM categories WHERE id = ?"
    );

    $check->bind_param("i", $category);
    $check->execute();

    if ($check->get_result()->num_rows == 0) {
        die("Please select a valid category.");
    }

    // Upload product image
    if (isset($_FILES["picture"]) &&
        $_FILES["picture"]["error"] != UPLOAD_ERR_NO_FILE) {

        if ($_FILES["picture"]["error"] != UPLOAD_ERR_OK) {
            die("Image upload failed. Please try again.");
        }

        $tmp = $_FILES["picture"]["tmp_name"];
        $info = @getimagesize($tmp);

        $mime = ($info && isset($info["mime"]))
              ? $info["mime"] : "";

        if (!$info ||
            !in_array($mime, array(
                "image/jpeg",
                "image/png",
                "image/webp"
            ), true) ||
            $_FILES["picture"]["size"] > 2 * 1024 * 1024) {

            die("Upload a JPG, PNG or WebP image under 2 MB.");
        }

        // Create uploads folder if it does not exist
        if (!is_dir("../uploads")) {
            mkdir("../uploads", 0755, true);
        }

        $extension = ($mime == "image/jpeg") ? "jpg" :
                    (($mime == "image/png") ? "png" : "webp");

        // Compatible with older PHP versions
        $filename = uniqid() . mt_rand(100, 999) . "." . $extension;

        $destination = "../uploads/" . $filename;

        if (!resizeImage($tmp, $destination, $mime)) {
            die("Image resizing failed. Check that PHP GD is enabled.");
        }

        $picture = $filename;
    }

    // Insert product into database
    $stmt = $conn->prepare(
        "INSERT INTO products
         (category_id, pname, price, picture)
         VALUES (?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "isds",
        $category,
        $name,
        $price,
        $picture
    );

    if ($stmt->execute()) {
        header("Location: product.php");
        exit;
    } else {
        $message = "Unable to add product.";
    }
}

// Delete product
if (isset($_GET["delete"])) {
    $id = (int) $_GET["delete"];

    // Get image filename before deleting
    $stmt = $conn->prepare(
        "SELECT picture FROM products WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $old = $stmt->get_result()->fetch_assoc();

    // Delete product
    $stmt = $conn->prepare(
        "DELETE FROM products WHERE id = ?"
    );

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {

        if ($old && $old["picture"] != "") {
            $path = "../uploads/" . basename($old["picture"]);

            if (is_file($path)) {
                unlink($path);
            }
        }

        header("Location: product.php");
        exit;

    } else {
        $message = "Unable to delete product.";
    }
}

// Fetch categories and products
$categories = $conn->query(
    "SELECT * FROM categories ORDER BY name"
);

$products = $conn->query(
    "SELECT p.*, c.name AS category_name
     FROM products p
     JOIN categories c ON p.category_id = c.id
     ORDER BY p.id DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Manage Products</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 25px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        h2, h3 {
            color: #333;
            text-align: center;
        }

        .form-box {
            max-width: 450px;
            margin: 25px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 7px;
        }

        input, select {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 11px;
            margin-top: 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .table-wrapper {
            overflow-x: auto;
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

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .product-img {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }

        .delete {
            display: inline-block;
            padding: 8px 10px;
            background-color: #e53935;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .delete:hover {
            background-color: #c62828;
        }

        .back {
            display: inline-block;
            margin-top: 25px;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .back:hover {
            background-color: #45a049;
        }

        .message {
            text-align: center;
            color: red;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Manage Products</h2>

    <?php if ($message != "") { ?>
        <p class="message">
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php } ?>

    <div class="form-box">

        <h3>Add New Product</h3>

        <form method="post" enctype="multipart/form-data">

            <label>Category:</label>

            <select name="category_id" required>
                <?php while ($c = $categories->fetch_assoc()) { ?>
                    <option value="<?php echo (int) $c["id"]; ?>">
                        <?php
                        echo htmlspecialchars(
                            $c["name"],
                            ENT_QUOTES,
                            "UTF-8"
                        );
                        ?>
                    </option>
                <?php } ?>
            </select>

            <label>Product Name:</label>

            <input type="text" name="pname" required>

            <label>Price:</label>

            <input type="number" name="price"
                   min="0" step="0.01" required>

            <label>Product Picture:</label>

            <input type="file" name="picture"
                   accept="image/jpeg,image/png,image/webp">

            <button type="submit" name="add">
                Add Product
            </button>

        </form>

    </div>

    <h3>Product List</h3>

    <div class="table-wrapper">

        <table>
            <tr>
                <th>ID</th>
                <th>Category</th>
                <th>Name</th>
                <th>Price</th>
                <th>Picture</th>
                <th>Action</th>
            </tr>

            <?php while ($p = $products->fetch_assoc()) { ?>

                <tr>
                    <td>
                        <?php echo (int) $p["id"]; ?>
                    </td>

                    <td>
                        <?php
                        echo htmlspecialchars(
                            $p["category_name"],
                            ENT_QUOTES,
                            "UTF-8"
                        );
                        ?>
                    </td>

                    <td>
                        <?php
                        echo htmlspecialchars(
                            $p["pname"],
                            ENT_QUOTES,
                            "UTF-8"
                        );
                        ?>
                    </td>

                    <td>
                        Rs. <?php
                        echo number_format($p["price"], 2);
                        ?>
                    </td>

                    <td>
                        <?php if (!empty($p["picture"])) { ?>

                            <img
                                class="product-img"
                                src="../uploads/<?php
                                    echo rawurlencode($p["picture"]);
                                ?>"
                                alt="Product">

                        <?php } else { ?>

                            No Image

                        <?php } ?>
                    </td>

                    <td>
                        <a
                            class="delete"
                            href="product.php?delete=<?php
                                echo (int) $p["id"];
                            ?>"
                            onclick="return confirm('Delete product?')">
                            Delete
                        </a>
                    </td>
                </tr>

            <?php } ?>

        </table>

    </div>

    <a class="back" href="dashboard.php">
        Back to Dashboard
    </a>

</div>

</body>
</html>
