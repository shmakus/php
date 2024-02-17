<?php
$servername = "my_mysql_container";  // Имя контейнера MySQL в вашем docker-compose.yml
$username = "user";
$password = "pass";
$dbname = "php";

// Создание подключения
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка подключения
if ($conn->connect_error) {
    die("Ошибка подключения " . $conn->connect_error);
}
?>
