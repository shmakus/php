<?php
session_start();
include 'db_connection.php';

// Проверка, если пользователь не авторизован или не является администратором, перенаправляем на его личную страницу
if (!isset($_SESSION["username"]) || $_SESSION["status"] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Получение списка всех пользователей из базы данных
$sql = "SELECT users.*, COUNT(notes.id) AS note_count FROM users LEFT JOIN notes ON users.username = notes.username GROUP BY users.id";
$result = $conn->query($sql);

// Обработка действий администратора (редактирование, удаление, изменение статуса)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Обработка редактирования пользователя
    if (isset($_POST['edit'])) {
        $user_id = $_POST["user_id"];
        $new_username = $_POST["new_username"];
        $new_password = $_POST["new_password"];
    
        // Проверка, было ли отправлено новое имя пользователя
        if (!empty($new_username)) {
            // Обновление имени пользователя в базе данных
            $update_username_sql = "UPDATE users SET username=? WHERE id=?";
            $stmt = $conn->prepare($update_username_sql);
            $stmt->bind_param("si", $new_username, $user_id);
            $stmt->execute();
            $stmt->close();
        }
    
        // Проверка, был ли отправлен новый пароль
        if (!empty($new_password)) {
            // Хеширование нового пароля
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
            // Обновление пароля пользователя в базе данных
            $update_password_sql = "UPDATE users SET password=? WHERE id=?";
            $stmt = $conn->prepare($update_password_sql);
            $stmt->bind_param("si", $hashed_password, $user_id);
            $stmt->execute();
            $stmt->close();
        }
    }
    

    // Обработка изменения статуса пользователя
    if (isset($_POST['change_status'])) {
        $user_id = $_POST["user_id"];
        $new_status = $_POST["new_status"];

        // Обновление статуса пользователя в базе данных
        $update_status_sql = "UPDATE users SET status=? WHERE id=?";
        $stmt = $conn->prepare($update_status_sql);
        $stmt->bind_param("si", $new_status, $user_id);
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

    // Обработка добавления нового пользователя
    if (isset($_POST['add_user'])) {
        $new_username = $_POST["new_username"];
        $new_password = $_POST["new_password"];
        $new_status = $_POST["new_status"];
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT); // Хеширование пароля
    
        // Вставка нового пользователя в базу данных
        $insert_sql = "INSERT INTO users (username, password, status) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("sss", $new_username, $hashed_password, $new_status);
        $stmt->execute();
        $stmt->close();
    }

    // Перенаправление после обработки действия
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        header {
            background-color: #333;
            padding: 10px;
            text-align: center;
            color: white;
        }
        nav {
            display: flex;
            justify-content: space-around;
            background-color: #555;
            padding: 10px;
        }
        nav a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        nav a:hover {
            background-color: #777;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #555;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .btn {
            padding: 8px 15px;
            background-color: #555;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #777;
        }
    </style>
</head>
<body>
    <header>
        <h1>Admin Panel</h1>
    </header>
    <nav>
        <a href="admin_panel.php">Admin Panel</a>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </nav>
    <div class="container">
        <h2>User List</h2>
        <table>
            <tr>
                <th>Username</th>
                <th>Status</th>
                <th>Notes Count</th>
                <th>Action</th>
            </tr>
            <?php
            // Вывод списка пользователей
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["username"] . "</td>";
                    echo "<td>" . $row["status"] . "</td>";
                    echo "<td>" . $row["note_count"] . "</td>"; // Выводим количество заметок
                    echo "<td>";
                    echo "<form method='post'>";
                    echo "<input type='hidden' name='user_id' value='" . $row["id"] . "'>";
                    echo "<input type='text' name='new_username' placeholder='New username'>";
                    echo "<input type='password' name='new_password' placeholder='New password'>";
                    echo "<select name='new_status'>";
                    echo "<option value='user'>User</option>";
                    echo "<option value='admin'>Admin</option>";
                    echo "</select>";
                    echo "<button class='btn' type='submit' name='edit'>Edit</button>";
                    echo "<button class='btn' type='submit' name='change_status'>Change Status</button>";
                    echo "<button class='btn' type='submit' name='delete'>Delete</button>";
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
    <div class="container">
        <h2>Add New User</h2>
        <form method="post">
            <input type="text" name="new_username" placeholder="Username">
            <input type="password" name="new_password" placeholder="Password">
            <select name="new_status">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
            <button class="btn" type="submit" name="add_user">Add User</button>
        </form>
    </div>
    <script>
        // Автоматическая отправка формы после ее заполнения
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    form.submit();
                });
            });
        });
    </script>
</body>
</html>
