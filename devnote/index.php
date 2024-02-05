<!-- index.php -->
<?php include './db.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevNote - Your Developer's Notebook</title>
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
        .content {
            padding: 20px;
        }

        .notes {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        }

        .note-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            width: 200px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .note-card textarea {
            width: 100%;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <header>
        <h1>DevNote</h1>
    </header>

    <nav>
        <a href="#">Home</a>
        <a href="#">About</a>
        <a href="#">Contact</a>
        <a href="#">Login</a>
        <a href="#">Register</a>
    </nav>

    <div class="content">
        <h2>Your Quick Notes</h2>
        <div class="notes">
            <!-- Здесь будут отображаться ваши заметки -->
        </div>
    </div>
</body>
</html>


<div class="content">
    <h2>Your Quick Notes</h2>
    <div class="notes">
        <form id="noteForm" method="post" action="add_note.php" onsubmit="return addNote()">
            <div class="note-card">
                <textarea name="noteInput" id="noteInput" rows="4" placeholder="Add a new note..."></textarea>
                <button type="submit" onclick="addNote()">Add Note</button>
            </div>
        </form>
        <ul id="noteList">
            <?php
            // Подключение к базе данных
            $conn = new mysqli("mysql", "user", "pass", "php");

            // Проверка подключения
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Выполнение запроса
            $result = $conn->query("SELECT * FROM notes");

            // Проверка успешности выполнения запроса
            if ($result !== false) {
                while ($row = $result->fetch_assoc()) {
                    echo '<li class="note-card">' . $row["content"] . '</li>';
                }
            } else {
                echo "Error fetching notes: " . $conn->error;
            }

            // Закрытие соединения
            $conn->close();
            ?>
        </ul>
    </div>
</div>

<script>
    function addNote() {
    var noteInput = document.getElementById("noteInput").value;
    if (noteInput.trim() !== "") {
        var noteList = document.getElementById("noteList");
        var listItem = document.createElement("li");
        var noteCard = document.createElement("div");
        noteCard.className = "note-card";
        noteCard.appendChild(document.createTextNode(noteInput));
        listItem.appendChild(noteCard);
        noteList.appendChild(listItem);
        document.getElementById("noteInput").value = "";
        return false;  // Добавлено: предотвращение отправки формы
    }
    return true;  // Добавлено: разрешение отправки формы, если поле пусто
}

</script>