<?php
// Подключаем файл конфигурации базы данных
include_once '../../config/database.php';

// Получаем значение user_id из параметра запроса
$user_id = $_GET['user_id'];

// Создаем экземпляр класса Database и получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// SQL-запрос для получения информации о чатах и пользователях, с которыми общается данный пользователь
$query = "SELECT users.id, users.username FROM chats 
          JOIN users ON (chats.user1_id = users.id OR chats.user2_id = users.id) 
          WHERE (chats.user1_id = :user_id OR chats.user2_id = :user_id) AND users.id != :user_id";

// Подготавливаем запрос к выполнению
$stmt = $db->prepare($query);

// Привязываем параметр user_id к подготовленному запросу
$stmt->bindParam(':user_id', $user_id);

// Выполняем запрос
$stmt->execute();

// Извлекаем все записи в виде ассоциативного массива
$chats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Преобразуем массив чатов в формат JSON и выводим его
echo json_encode($chats);
?>
