<?php
session_start();
include 'db_connection.php';

// Проверка, если пользователь уже авторизован, перенаправьте его на страницу дашборда
if (isset($_SESSION["username"])) {
    // Получение идентификатора пользователя из сессии
    $user_id_session = $_SESSION["user_id"];
    header("Location: dashboard.php?user_id=" . $user_id_session);
    exit();
}

// Проверка, была ли отправлена форма
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Проверка обязательного заполнения полей
    if (!empty($_POST["username"]) && !empty($_POST["password"])) {
        // Проверка, содержат ли поля только латинские символы
        if (preg_match('/^[a-zA-Z0-9]+$/', $_POST["username"])) {
            $username = $_POST["username"];
            $password = $_POST["password"];

            // Получение хэша пароля из базы данных
            $sql = "SELECT id, password, status FROM users WHERE username=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $hashed_password = $row["password"];

                // Проверка соответствия введенного пароля хэшу из базы данных
                if (password_verify($password, $hashed_password)) {
                    // Пользователь найден, создаем сессию и перенаправляем его на личную страницу
                    $_SESSION["username"] = $username;
                    $_SESSION["user_id"] = $row["id"]; // Получение ID пользователя
                    $_SESSION["status"] = $row["status"]; // Получение статуса пользователя

                    header("Location: dashboard.php?user_id=" . $_SESSION["user_id"]); // Передача ID пользователя через URL
                    exit();
                } else {
                    // Пользователь не найден или пароль не совпадает, выводим сообщение об ошибке
                    echo "Неправильный логин или пароль.";
                }
            } else {
                // Пользователь не найден, выводим сообщение об ошибке
                echo "Неправильный логин или пароль.";
            }
        } else {
            echo "В имени пользователя допускается использование только латинских символов и цифр.";
        }
    } else {
        echo "Требуются имя пользователя и пароль.";
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
        <p style="text-align: center;">У вас нет аккаунта? <a href="register.php">Регистрация</a></p>
    </div>
</body>
</html>

