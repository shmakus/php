<?php
session_start();
include 'db_connection.php';

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
</head>
<body>
    <h2>Login</h2>
    <form method="post" action="">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" pattern="[a-zA-Z0-9]+" required><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" pattern="[a-zA-Z0-9]+" required><br><br>
        <input type="submit" value="Login">
    </form>
</body>
</html>
