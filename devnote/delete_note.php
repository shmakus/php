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

// Удаляем заметку из базы данных
$sql = "DELETE FROM notes WHERE id = '$note_id'";
$conn->query($sql);

// Закрываем подключение к базе данных
$conn->close();

// Перенаправляем на страницу Dashboard после удаления заметки
header("Location: dashboard.php");
exit;
?>
