<?php
session_start();
// Проверка, если пользователь уже авторизован, перенаправьте его на страницу дашборда
if (isset($_SESSION["username"])) {
    header("Location: dashboard.php");
    exit();
}
// Остальной код страницы
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login or Register</title>
</head>
<body>
    <h2>Login or Register</h2>
    <p>Login<a href="login.php">Login</a></p>

    <p>Don't have an account? <a href="register.php">Register</a></p>
</body>
</html>
