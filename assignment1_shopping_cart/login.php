<?php

session_start();
require_once "config/db.php";

if (isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare(
        "SELECT id, name, email, password
         FROM users
         WHERE email = ?"
    );

    $stmt->execute([$email]);

    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {

        session_regenerate_id(true);

        $_SESSION["user_id"] = $user["id"];
        $_SESSION["user_name"] = $user["name"];

        header("Location: index.php");
        exit;

    } else {

        $error = "Invalid email or password.";

    }
}

?>

<!DOCTYPE html>

<html>
<head>
    <title>User Login</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="form-container">

```
<h2>User Login</h2>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST">

    <label>Email</label>
    <input type="email" name="email" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <button type="submit">Login</button>

</form>

<p><a href="register.php">Create an account</a></p>
<p><a href="index.php">Back to Store</a></p>
```

</div>

</body>
</html>
