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
    <title>Registration</title>
</head>
<body>
    <h2>Registration</h2>
    <form method="post" action="">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" pattern="[a-zA-Z0-9]+" required><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" pattern="[a-zA-Z0-9]+" required><br><br>
        <input type="submit" value="Register">
    </form>
</body>
</html>
