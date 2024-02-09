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
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Note</title>
</head>
<body>
    <h2>Edit Note</h2>
    <form method="post" action="">
        <textarea name="noteInput" rows="4"><?php echo $noteContent; ?></textarea><br>
        <button type="submit">Save Changes</button>
    </form>
    <a href="dashboard.php">Cancel</a>
</body>
</html>
