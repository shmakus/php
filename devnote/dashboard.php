<?php
session_start();
include 'db_connection.php';

// Проверка авторизации пользователя
if (!isset($_SESSION["username"])) {
    header("Location: index.php");
    exit;
}

// Получение заметок пользователя
$username = $_SESSION["username"];
$sql = "SELECT * FROM notes WHERE username = '$username'";
$result = $conn->query($sql);

// Обработка добавления новой заметки
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Проверка, была ли отправлена форма добавления заметки
    if (isset($_POST["noteInput"])) {
        // Получаем текст заметки из формы
        $noteContent = $_POST["noteInput"];
        
        // Подготавливаем SQL запрос для вставки заметки в базу данных
        $sql = "INSERT INTO notes (username, content) VALUES (?, ?)";
        
        // Подготавливаем и выполняем запрос к базе данных с использованием подготовленных выражений
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $_SESSION["username"], $noteContent);
        $stmt->execute();
        
        // Закрываем подготовленное выражение
        $stmt->close();
        
        // Перезагружаем страницу для обновления списка заметок
        header("Location: dashboard.php");
        exit();
    }
}

// Закрываем подключение к базе данных
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
            word-wrap: break-word;
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
            margin-top: 20px;
            text-align: center;
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
        
        .add-note {
            text-align: right;
            margin-bottom: 20px;
        }
        .add-note button {
            background-color: #555;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .add-note button:hover {
            background-color: #777;
        }
        .logout {
            text-align: right;
            margin-bottom: 20px;
        }
        .logout button {
            background-color: #555;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .logout button:hover {
            background-color: #777;
        }
        a:focus {
            outline: none;
        }

    </style>
</head>
<body>

    <div class="container">
        <h2>Добро пожаловать, <?php echo $_SESSION["username"]; ?></h2>
        <h3>Твои заметки:</h3>
        <ul>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<li>" . $row["content"] . " <a href='edit_note.php?id=" . $row["id"] . "'><i class='fas fa-edit'></i></a> <a href='delete_note.php?id=" . $row["id"] . "'><i class='fas fa-trash-alt'></i></a></li>";
            }
        } else {
            echo "No notes found.";
        }
        ?>
    </ul>
    <div class="add-note">
        <form method="post" action="">
            <textarea name="noteInput" rows="4" placeholder="Add a new note..."></textarea><br>
            <button type="submit">Добавить заметку</button>
        </form>
    </div>

    <div class="logout">
            <form action="logout.php" method="post">
                <button type="submit">Выйти</button>
            </form>
        </div>
    </div>
</body>
</html>