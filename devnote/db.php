<?php
$servername = "mysql";  // Имя контейнера MySQL в вашем docker-compose.yml
$username = "user";
$password = "pass";
$dbname = "php";

// Подключение к базе данных
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка подключения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Создание таблицы заметок, если она не существует
$sql = "CREATE TABLE IF NOT EXISTS notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT NOT NULL
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'notes' created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

// Закрытие соединения
$conn->close();
?>
