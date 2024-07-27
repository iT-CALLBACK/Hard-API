<?php
// Подключаем файл конфигурации базы данных
include_once '../../config/database.php';

// Создаем экземпляр класса Database и получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// SQL-запрос для получения постов и имен пользователей, отсортированных по идентификатору постов в порядке убывания
$query = "SELECT posts.id, posts.content, users.username FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.id DESC";

// Подготавливаем запрос к выполнению
$stmt = $db->prepare($query);

// Выполняем запрос
$stmt->execute();

// Извлекаем все записи в виде ассоциативного массива
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Преобразуем массив постов в формат JSON и выводим его
echo json_encode($posts);
?>
