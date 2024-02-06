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
                echo "<li>" . $row["content"] . "</li>";
            }
        } else {
            echo "No notes found.";
        }
        ?>
    </ul>
    <a href="logout.php">Logout</a>
</body>
</html>
