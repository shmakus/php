<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Подключение к базе данных
    $conn = new mysqli("mysql", "user", "pass", "php");

    // Проверка подключения
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Получение данных из формы и защита от SQL-инъекций
    $noteInput = $conn->real_escape_string($_POST["noteInput"]);

    // Подготовка и выполнение запроса на добавление заметки
    $sql = "INSERT INTO notes (content) VALUES ('$noteInput')";

    if ($conn->query($sql) === TRUE) {
        echo "Note added successfully";
    } else {
        echo "Error adding note: " . $conn->error;
    }

    // Закрытие соединения
    $conn->close();
} else {
    echo "Invalid request";
}
?>
