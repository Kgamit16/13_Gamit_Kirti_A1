```php
<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: products.php");
    exit;
}

$action = $_POST["action"] ?? "";

function uploadPicture($fieldName, $required = false)
{
    if (
        !isset($_FILES[$fieldName]) ||
        $_FILES[$fieldName]["error"] === UPLOAD_ERR_NO_FILE
    ) {
        if ($required) {
            throw new Exception("Please select a product picture.");
        }

        return null;
    }

    $file = $_FILES[$fieldName];

    if ($file["error"] !== UPLOAD_ERR_OK) {
        throw new Exception("Picture upload failed. Please try again.");
    }

    if ($file["size"] > 5 * 1024 * 1024) {
        throw new Exception("Picture must be smaller than 5 MB.");
    }

    if (!is_uploaded_file($file["tmp_name"])) {
        throw new Exception("Invalid uploaded file.");
    }

    $imageInfo = @getimagesize($file["tmp_name"]);

    if ($imageInfo === false) {
        throw new Exception("Please upload a valid image.");
    }

    $allowedTypes = [
        "image/jpeg" => "jpg",
        "image/png"  => "png",
        "image/webp" => "webp"
    ];

    $mime = $imageInfo["mime"];

    if (!isset($allowedTypes[$mime])) {
        throw new Exception("Only JPG, PNG, and WEBP images are allowed.");
    }

    $uploadDirectory = dirname(__DIR__) . "/uploads/";

    if (!is_dir($uploadDirectory)) {
        if (!mkdir($uploadDirectory, 0755, true)) {
            throw new Exception("Could not create the uploads directory.");
        }
    }

    if (!is_writable($uploadDirectory)) {
        throw new Exception("The uploads directory is not writable.");
    }

    $filename = bin2hex(random_bytes(16)) . "." . $allowedTypes[$mime];
    $destination = $uploadDirectory . $filename;

    if (!move_uploaded_file($file["tmp_name"], $destination)) {
        throw new Exception("Could not save the uploaded picture.");
    }

    return $filename;
}

function removePicture($filename)
{
    if (empty($filename)) {
        return;
    }

    $path = dirname(__DIR__) . "/uploads/" . basename($filename);

    if (is_file($path)) {
        unlink($path);
    }
}

try {

    // ADD PRODUCT
    if ($action === "add") {

        $pname = trim($_POST["pname"] ?? "");
        $categoryId = filter_input(
            INPUT_POST,
            "category_id",
            FILTER_VALIDATE_INT
        );
        $price = filter_input(
            INPUT_POST,
            "price",
            FILTER_VALIDATE_FLOAT
        );

        if (
            $pname === "" ||
            strlen($pname) > 150 ||
            !$categoryId ||
            $price === false ||
            $price === null ||
            $price <= 0
        ) {
            throw new Exception("Enter valid product details.");
        }

        $check = $conn->prepare(
            "SELECT id FROM categories WHERE id = ?"
        );

        $check->execute([$categoryId]);

        if (!$check->fetch()) {
            throw new Exception("Please select a valid category.");
        }

        $picture = uploadPicture("picture", true);

        try {

            $stmt = $conn->prepare(
                "INSERT INTO products (category_id, pname, price, picture)
                 VALUES (?, ?, ?, ?)"
            );

            $stmt->execute([
                $categoryId,
                $pname,
                $price,
                $picture
            ]);

        } catch (Throwable $e) {
            removePicture($picture);
            throw $e;
        }

        $message = "Product added successfully.";

    }

    // UPDATE PRODUCT
    elseif ($action === "update") {

        $id = filter_input(
            INPUT_POST,
            "id",
            FILTER_VALIDATE_INT
        );

        $pname = trim($_POST["pname"] ?? "");

        $categoryId = filter_input(
            INPUT_POST,
            "category_id",
            FILTER_VALIDATE_INT
        );

        $price = filter_input(
            INPUT_POST,
            "price",
            FILTER_VALIDATE_FLOAT
        );

        if (
            !$id ||
            $pname === "" ||
            strlen($pname) > 150 ||
            !$categoryId ||
            $price === false ||
            $price === null ||
            $price <= 0
        ) {
            throw new Exception("Enter valid product details.");
        }

        $check = $conn->prepare(
            "SELECT * FROM products WHERE id = ?"
        );

        $check->execute([$id]);
        $oldProduct = $check->fetch();

        if (!$oldProduct) {
            throw new Exception("Product not found.");
        }

        $check = $conn->prepare(
            "SELECT id FROM categories WHERE id = ?"
        );

        $check->execute([$categoryId]);

        if (!$check->fetch()) {
            throw new Exception("Please select a valid category.");
        }

        $newPicture = uploadPicture("picture", false);

        try {

            if ($newPicture !== null) {

                $stmt = $conn->prepare(
                    "UPDATE products
                     SET category_id = ?, pname = ?, price = ?, picture = ?
                     WHERE id = ?"
                );

                $stmt->execute([
                    $categoryId,
                    $pname,
                    $price,
                    $newPicture,
                    $id
                ]);

            } else {

                $stmt = $conn->prepare(
                    "UPDATE products
                     SET category_id = ?, pname = ?, price = ?
                     WHERE id = ?"
                );

                $stmt->execute([
                    $categoryId,
                    $pname,
                    $price,
                    $id
                ]);

            }

        } catch (Throwable $e) {

            if ($newPicture !== null) {
                removePicture($newPicture);
            }

            throw $e;
        }

        if ($newPicture !== null) {
            removePicture($oldProduct["picture"] ?? "");
        }

        $message = "Product updated successfully.";

    }

    // DELETE PRODUCT
    elseif ($action === "delete") {

        $id = filter_input(
            INPUT_POST,
            "id",
            FILTER_VALIDATE_INT
        );

        if (!$id) {
            throw new Exception("Invalid product ID.");
        }

        $stmt = $conn->prepare(
            "SELECT picture FROM products WHERE id = ?"
        );

        $stmt->execute([$id]);
        $product = $stmt->fetch();

        if (!$product) {
            throw new Exception("Product not found.");
        }

        $stmt = $conn->prepare(
            "DELETE FROM products WHERE id = ?"
        );

        $stmt->execute([$id]);

        removePicture($product["picture"] ?? "");

        $message = "Product deleted successfully.";

    }

    else {
        throw new Exception("Invalid product action.");
    }

} catch (PDOException $e) {

    $message = "Database error. Please check your product details and database.";

} catch (Throwable $e) {

    $message = $e->getMessage();

}

header(
    "Location: products.php?message=" . urlencode($message)
);

exit;
?>
```
