<?php
// Подключаем файл конфигурации базы данных
include_once '../../config/database.php';

// Получаем значение post_id из параметра запроса
$post_id = $_GET['post_id'];

// Создаем экземпляр класса Database и получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// SQL-запрос для получения комментариев по идентификатору поста и имен пользователей
$query = "SELECT comments.id, comments.content, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id = :post_id";

// Подготавливаем запрос к выполнению
$stmt = $db->prepare($query);

// Привязываем параметр post_id к подготовленному запросу
$stmt->bindParam(':post_id', $post_id);

// Выполняем запрос
$stmt->execute();

// Извлекаем все записи в виде ассоциативного массива
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Преобразуем массив комментариев в формат JSON и выводим его
echo json_encode($comments);
?>
