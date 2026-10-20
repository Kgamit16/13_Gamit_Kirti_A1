
<?php
require_once "db.php";

if (!isset($_SESSION["captcha_a"])) {
    $_SESSION["captcha_a"] = rand(1, 9);
    $_SESSION["captcha_b"] = rand(1, 9);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $answer = isset($_POST["captcha"])
        ? (int)$_POST["captcha"] : -1;

    $correctAnswer =
        $_SESSION["captcha_a"] + $_SESSION["captcha_b"];

    if ($answer != $correctAnswer) {
        $message = "Incorrect CAPTCHA. Please try again.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email address.";
    } elseif (strlen($password) < 8) {
        $message = "Password must contain at least 8 characters.";
    } else {
       $salt = '$2y$10$' . substr(
    str_replace('.', '/', uniqid('', true)),
    0,
    22
);

$hash = crypt($password, $salt);

        $stmt = $conn->prepare(
            "INSERT INTO users (name, email, password)
             VALUES (?, ?, ?)"
        );

        $stmt->bind_param("sss", $name, $email, $hash);

        if ($stmt->execute()) {
            unset($_SESSION["captcha_a"]);
            unset($_SESSION["captcha_b"]);

            header("Location: login.php");
            exit;
        } else {
            $message = "Registration failed. Email may already exist.";
        }

        $stmt->close();
    }

    $_SESSION["captcha_a"] = rand(1, 9);
    $_SESSION["captcha_b"] = rand(1, 9);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 350px;
            margin: 50px auto;
            padding: 25px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 6px;
            color: #333;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 3px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .message {
            color: red;
            text-align: center;
        }

        .links {
            text-align: center;
            margin-top: 15px;
        }

        .links a {
            color: #0066cc;
            text-decoration: none;
        }

        .links a:hover {
            text-decoration: underline;
        }

        .captcha {
            background-color: #eee;
            padding: 10px;
            margin-bottom: 10px;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>User Registration</h2>

    <p class="message">
        <?php
        echo htmlspecialchars($message, ENT_QUOTES, "UTF-8");
        ?>
    </p>

    <form method="post" action="">

        <label>Name:</label>
        <input type="text" name="name" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <label>CAPTCHA:</label>

        <div class="captcha">
            <?php
            echo $_SESSION["captcha_a"] . " + "
                 . $_SESSION["captcha_b"] . " = ?";
            ?>
        </div>

        <input type="number" name="captcha"
               placeholder="Enter CAPTCHA answer" required>

        <button type="submit">Register</button>

    </form>

    <div class="links">
        <a href="login.php">Already registered? Login</a>
        <br><br>
        <a href="index.php">Back to Home</a>
    </div>

</div>

</body>
</html>
