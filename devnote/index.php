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
    <title>DevNote - Твоя книга разработчика</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        header {
            background-color: #333;
            padding: 10px;
            text-align: center;
            color: white;
        }
        nav {
            display: flex;
            justify-content: space-around;
            background-color: #555;
            padding: 10px;
        }
        nav a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        nav a:hover {
            background-color: #777;
        }
        .content {
            padding: 20px;
            text-align: center;
        }
        form {
            margin-top: 20px;
        }
        input[type="text"], input[type="password"] {
            width: 300px;
            padding: 8px;
            margin: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px 20px;
            border: none;
            background-color: #555;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #777;
        }
    </style>
</head>
<body>
    <header>
        <h1>DevNote.local</h1>
    </header>

    <nav>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    </nav>

    <div class="content">
        <h2>Войдите или создайте учетную запись</h2>
        
    </div>
</body>
</html>

