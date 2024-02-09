<?php
session_start();
include 'db_connection.php';
// Проверка, если пользователь уже авторизован, перенаправьте его на страницу дашборда
if (isset($_SESSION["username"])) {
    header("Location: dashboard.php");
    exit();
}
// Остальной код страницы
// Проверка, была ли отправлена форма
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Проверка обязательного заполнения полей
    if (!empty($_POST["username"]) && !empty($_POST["password"])) {
        // Проверка, содержат ли поля только латинские символы
        if (preg_match('/^[a-zA-Z0-9]+$/', $_POST["username"]) && preg_match('/^[a-zA-Z0-9]+$/', $_POST["password"])) {
            $username = $_POST["username"];
            $password = $_POST["password"];

            // Проверка наличия пользователя в базе данных
            $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
            $result = $conn->query($sql);

            if ($result->num_rows == 1) {
                // Пользователь найден, создаем сессию и перенаправляем его на личную страницу
                $_SESSION["username"] = $username;
                header("Location: dashboard.php");
                exit();
            } else {
                // Пользователь не найден, выводим сообщение об ошибке
                echo "Invalid username or password.";
            }
        } else {
            echo "Only Latin characters and digits are allowed for username and password.";
        }
    } else {
        echo "Both username and password are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 300px;
            margin: 100px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
        }
        form {
            margin-top: 20px;
        }
        input[type="text"],
        input[type="password"] {
            width: calc(100% - 20px);
            margin: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        input[type="submit"] {
            width: calc(100% - 20px);
            margin: 10px;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #333;
            color: #fff;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #555;
        }
        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form method="post" action="">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="submit" value="Login">
        </form>
        <?php if(isset($error_message)) { ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php } ?>
        <p style="text-align: center;">Don't have an account? <a href="register.php">Register</a></p>
    </div>
</body>
</html>

