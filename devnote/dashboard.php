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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION["username"]; ?></h2>
    <h3>Your Notes:</h3>
    <ul>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<li>" . $row["content"] . " <a href='edit_note.php?id=" . $row["id"] . "'>Edit</a> <a href='delete_note.php?id=" . $row["id"] . "'>Delete</a></li>";
            }
        } else {
            echo "No notes found.";
        }
        ?>
    </ul>

    <form method="post" action="">
        <textarea name="noteInput" rows="4" placeholder="Add a new note..."></textarea><br>
        <button type="submit">Add Note</button>
    </form>

    <a href="logout.php">Logout</a>
</body>
</html>
