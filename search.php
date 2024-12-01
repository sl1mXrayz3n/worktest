<?php
// Настройки подключения к базе данных
$host = 'localhost';
$dbname = 'blog';
$username = 'root';
$password = 'anton';

try {
    // Подключение к базе данных
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Подключение к базе данных успешно!\n";
} catch (PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}

// Проверка, если форма была отправлена
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search = trim($_POST['search']);

    // Проверка длины строки
    if (strlen($search) < 3) {
        echo "Для поиска введите минимум 3 символа.";
    } else {
        // Поиск по комментариям
        $stmt = $pdo->prepare("SELECT p.title, c.body 
                               FROM posts p 
                               JOIN comments c ON p.id = c.post_id
                               WHERE c.body LIKE :search");
        $stmt->execute(['search' => "%" . $search . "%"]);

        // Проверка наличия результатов
        if ($stmt->rowCount() > 0) {
            echo "<h2>Результаты поиска:</h2>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<strong>" . htmlspecialchars($row['title']) . "</strong><br>";
                echo "Комментарий: " . htmlspecialchars($row['body']) . "<br><br>";
            }
        } else {
            echo "Ничего не найдено.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поиск комментариев</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <h1>Поиск комментариев</h1>
    <form method="POST">
        <input type="text" name="search" placeholder="Введите текст для поиска" value="" required>
        <button type="submit">Найти</button>
    </form>
</body>

</html>