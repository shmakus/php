<?php
session_start();
include 'db_connection.php';

// Проверка авторизации пользователя
if (!isset($_SESSION["username"])) {
    header("Location: index.php");
    exit;
}

// Проверка, был ли передан идентификатор заметки
if (!isset($_GET["id"]) || empty($_GET["id"])) {
    header("Location: dashboard.php");
    exit;
}

// Получаем идентификатор заметки
$note_id = $_GET["id"];

// Проверяем, существует ли заметка с указанным идентификатором и принадлежит ли она текущему пользователю
$username = $_SESSION["username"];
$sql = "SELECT * FROM notes WHERE id = '$note_id' AND username = '$username'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    // Заметка не найдена или не принадлежит текущему пользователю, перенаправляем на страницу Dashboard
    header("Location: dashboard.php");
    exit;
}

// Обработка изменения заметки
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Проверка, была ли отправлена форма изменения заметки
    if (isset($_POST["noteInput"])) {
        // Получаем текст заметки из формы
        $noteContent = $_POST["noteInput"];
        
        // Подготавливаем SQL запрос для обновления заметки в базе данных
        $sql = "UPDATE notes SET content = ? WHERE id = ?";
        
        // Подготавливаем и выполняем запрос к базе данных с использованием подготовленных выражений
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $noteContent, $note_id);
        $stmt->execute();
        
        // Закрываем подготовленное выражение
        $stmt->close();
        
        // Перенаправляем на страницу Dashboard после изменения заметки
        header("Location: dashboard.php");
        exit();
    }
}

// Получаем содержимое заметки для предзаполнения формы
$row = $result->fetch_assoc();
$noteContent = $row["content"];

// Закрываем подключение к базе данных
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 600px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            
        }
        h2 {
            text-align: center;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            background-color: #f9f9f9;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
        }
        form {
            margin: 60px;
            text-align: center;
            width: auto;
        }
        textarea {
            width: calc(100% - 20px);
            margin: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        input[type="submit"] {
            width: calc(100% - 20px);
            margin: 20px;
            padding: 20px;
            border: none;
            border-radius: 5px;
            background-color: #333;
            color: #fff;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #555;
        }
        
        .edit-note {
            text-align: right;
            margin-bottom: 20px;
        }
        .edit-note button {
            background-color: #555;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .edit-note button:hover {
            background-color: #777;
        }
        .cancel {
            text-align: right;
            margin-bottom: 20px;
        }
        .cancel button {
            background-color: #555;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .cancel button:hover {
            background-color: #777;
        }
    </style>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование заметки</title>
</head>
<body>
    <h2>Редактирование</h2>
    <div class="edit-note">
    <form method="post" action="">
        <textarea name="noteInput" rows="4"><?php echo $noteContent; ?></textarea><br>
        <button type="submit">Сохранить</button>
    </form>
    </div>
    <div class="cancel">
    <form action="dashboard.php">
        <button type="submit">Отмена</button>
    </form>
    </div>
</body>
</html>
