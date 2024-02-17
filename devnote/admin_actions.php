<?php
session_start();
include 'db_connection.php';

// Проверка, если пользователь не авторизован или не является администратором, перенаправляем на его личную страницу
if (!isset($_SESSION["username"]) || $_SESSION["status"] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Обработка действий администратора (редактирование, удаление, изменение статуса)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Обработка редактирования пользователя
    if (isset($_POST['edit'])) {
        $user_id = $_POST["user_id"];
        $new_username = $_POST["new_username"];
        $new_password = $_POST["new_password"];
        $new_status = $_POST["new_status"];

        // Обновление данных пользователя в базе данных
        $update_sql = "UPDATE users SET username=?, password=?, status=? WHERE id=?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sssi", $new_username, $new_password, $new_status, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    // Обработка удаления пользователя
    if (isset($_POST['delete'])) {
        $user_id = $_POST["user_id"];

        // Удаление пользователя из базы данных
        $delete_sql = "DELETE FROM users WHERE id=?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        /* Стили здесь */
    </style>
</head>
<body>
    <header>
        <h1>Admin Panel</h1>
    </header>
    <nav>
        <!-- Навигационные ссылки здесь -->
    </nav>
    <div class="container">
        <h2>User List</h2>
        <table>
            <tr>
                <th>Username</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php
            // Вывод списка пользователей
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["username"] . "</td>";
                    echo "<td>" . $row["status"] . "</td>";
                    echo "<td>";
                    // Форма для редактирования
                    echo "<form method='post'>";
                    echo "<input type='hidden' name='user_id' value='" . $row["id"] . "'>";
                    echo "<input type='text' name='new_username' placeholder='New username'>";
                    echo "<input type='password' name='new_password' placeholder='New password'>";
                    echo "<select name='new_status'>";
                    echo "<option value='admin'>Admin</option>";
                    echo "<option value='user'>User</option>";
                    echo "</select>";
                    echo "<button type='submit' name='edit'>Edit</button>";
                    echo "</form>";
                    // Форма для удаления
                    echo "<form method='post'>";
                    echo "<input type='hidden' name='user_id' value='" . $row["id"] . "'>";
                    echo "<button type='submit' name='delete'>Delete</button>";
                    echo "</form>";
                    // Форма для изменения статуса
                    echo "<form method='post'>";
                    echo "<input type='hidden' name='user_id' value='" . $row["id"] . "'>";
                    echo "<select name='new_status'>";
                    echo "<option value='admin'>Admin</option>";
                    echo "<option value='user'>User</option>";
                    echo "</select>";
                    echo "<button type='submit' name='change_status'>Change Status</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No users found</td></tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>
