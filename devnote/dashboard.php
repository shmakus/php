<?php
session_start();
include 'db_connection.php';

// Проверка, если пользователь не авторизован, перенаправьте его на страницу логина
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

// Получение идентификатора пользователя из сессии
$user_id_session = $_SESSION["user_id"];

// Получение статуса пользователя из сессии
$user_status = $_SESSION["status"];

// Проверка статуса пользователя на администраторский
$isAdmin = $user_status === 'admin';

// Проверка, был ли передан идентификатор пользователя через URL
if(isset($_GET['user_id'])){
    $user_id_url = $_GET['user_id'];

    // Сравнение идентификаторов пользователя из сессии и из URL
    if ($user_id_session != $user_id_url) {
        // Если идентификаторы не совпадают, перенаправляем пользователя на его собственную страницу dashboard
        header("Location: dashboard.php?user_id=" . $user_id_session);
        exit();
    }
} 

// Получение заметок пользователя
$username = $_SESSION["username"];
$sql = "SELECT * FROM notes WHERE username = '$username'";
$result = $conn->query($sql);

$image_path = ""; // Инициализация переменной перед использованием

// Обработка добавления новой заметки
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Проверка, была ли отправлена форма добавления заметки
    if (isset($_POST["noteInput"])) {
        // Получаем текст заметки из формы
        $noteContent = $_POST["noteInput"];

        // Инициализация переменной перед использованием
        $image_path = "";

        // Обработка загрузки изображения
        if(isset($_FILES['noteImage']) && $_FILES['noteImage']['error'] === UPLOAD_ERR_OK) {
            // Получаем информацию о файле
            $file_name = $_FILES['noteImage']['name'];
            $file_tmp = $_FILES['noteImage']['tmp_name'];
            $file_size = $_FILES['noteImage']['size'];
            $file_error = $_FILES['noteImage']['error'];

            // Проверяем расширение файла
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');

            if (in_array($file_ext, $allowed_extensions)) {
                // Генерируем уникальное имя файла
                $file_new_name = uniqid('', true) . '.' . $file_ext;
                $file_destination = 'uploads/' . $file_new_name;

                // Перемещаем файл из временного места в папку загрузок
                if (move_uploaded_file($file_tmp, $file_destination)) {
                    // Файл успешно загружен, сохраняем путь к изображению в базе данных
                    $image_path = $file_destination;
                } else {
                    echo "Произошла ошибка при загрузке файла.";
                }
            } else {
                echo "Недопустимый формат файла. Разрешены только JPG, JPEG, PNG и GIF.";
            }
        }
        
        // Подготавливаем SQL запрос для вставки заметки в базу данных
        // Подготавливаем SQL запрос для вставки заметки в базу данных
$sql = "INSERT INTO notes (username, content, image_path) VALUES (?, ?, ?)";

    // Подготавливаем запрос к базе данных
    $stmt = $conn->prepare($sql);
    if ($stmt) { // Проверяем успешность подготовки запроса
        // Если запрос подготовлен успешно, привязываем параметры и выполняем его
        $stmt->bind_param("sss", $_SESSION["username"], $noteContent, $image_path);
        $stmt->execute();

        // Закрываем подготовленное выражение
        $stmt->close();

        // Перезагружаем страницу для обновления списка заметок
        header("Location: dashboard.php");
        exit();
    } else {
        // Если запрос не удалось подготовить, выводим сообщение об ошибке
        echo "Ошибка при подготовке запроса: " . $conn->error;
    }

        
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
        .admin-panel-btn {
            display: <?php echo $isAdmin ? 'block' : 'none'; ?>;
            background-color: #555;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            margin-bottom: 20px;
            text-align: center;
        }
        .admin-panel-btn:hover {
            background-color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Добро пожаловать, <?php echo $_SESSION["username"]; ?></h2>
        <a href="admin_panel.php" class="admin-panel-btn">Перейти в админ-панель</a>
        <h3>Твои заметки:</h3>
        <ul>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<li>" . $row["content"];
                    
                    // Вывод изображений, если они есть
                    if(!empty($row["image_path"])) {
                        echo "<br><img src='" . $row["image_path"] . "' alt='Note Image' style='max-width: 200px;'>";
                    }

                    echo " <a href='edit_note.php?id=" . $row["id"] . "'><i class='fas fa-edit'></i></a> <a href='delete_note.php?id=" . $row["id"] . "'><i class='fas fa-trash-alt'></i></a></li>";
                }
            } else {
                echo "Список пуст";
            }
            ?>
        </ul>
        <div class="add-note">
            <form method="post" action="" enctype="multipart/form-data">
                <textarea name="noteInput" rows="4" placeholder="Add a new note..."></textarea><br>
                <input type="file" name="noteImage" accept="image/*"><br> <!-- Поле для выбора изображения -->
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
