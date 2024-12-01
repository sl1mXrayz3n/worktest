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

// Получение данных из API
$postsUrl = "https://jsonplaceholder.typicode.com/posts";
$commentsUrl = "https://jsonplaceholder.typicode.com/comments";

$posts = json_decode(file_get_contents($postsUrl), true);
$comments = json_decode(file_get_contents($commentsUrl), true);

if (!$posts || !$comments) {
    die("Не удалось получить данные из API.\n");
}

echo "Данные из API успешно получены.\n";

// Очистка таблиц перед загрузкой
$pdo->exec("DELETE FROM comments");
$pdo->exec("DELETE FROM posts");

// Загрузка записей в таблицу posts
$insertPost = $pdo->prepare("INSERT INTO posts (id, title, body) VALUES (:id, :title, :body)");
foreach ($posts as $post) {
    $insertPost->execute([
        ':id' => $post['id'],
        ':title' => $post['title'],
        ':body' => $post['body']
    ]);
}

// Загрузка комментариев в таблицу comments
$insertComment = $pdo->prepare("INSERT INTO comments (id, post_id, name, email, body) VALUES (:id, :post_id, :name, :email, :body)");
foreach ($comments as $comment) {
    $insertComment->execute([
        ':id' => $comment['id'],
        ':post_id' => $comment['postId'],
        ':name' => $comment['name'],
        ':email' => $comment['email'],
        ':body' => $comment['body']
    ]);
}

// Вывод сообщения о результатах
echo "Загружено " . count($posts) . " записей и " . count($comments) . " комментариев.\n";
