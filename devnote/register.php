<?php
session_start();
include 'db_connection.php';
// Проверка, если пользователь уже авторизован, перенаправьте его на страницу дашборда
if (isset($_SESSION["username"])) {
    header("Location: dashboard.php");
    exit();
}
// Остальной код страницы
// Проверка, была ли отправлена форма регистрации
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Проверка обязательного заполнения полей
    if (!empty($_POST["username"]) && !empty($_POST["password"])) {
        $username = $_POST["username"];
        $password = $_POST["password"];

        // Попытка вставки данных в базу данных
        $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
        if ($conn->query($sql) === TRUE) {
            // Установка сессии для пользователя
            $_SESSION["username"] = $username;

            // Перенаправление на дашборд
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Данный логин занят" ;
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
    <title>Register</title>
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
        <h2>Register</h2>
        <form method="post" action="">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="submit" value="Register">
        </form>
        <?php if(isset($error_message)) { ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php } ?>
        <p style="text-align: center;">Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>
</html>