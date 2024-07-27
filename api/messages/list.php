<?php
// Подключаем файл конфигурации базы данных
include_once '../../config/database.php';

// Получаем значения user_id и friend_id из параметров запроса
$user_id = $_GET['user_id'];
$friend_id = $_GET['friend_id'];

// Создаем экземпляр класса Database и получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// SQL-запрос для получения идентификатора чата между двумя пользователями
$query = "SELECT id FROM chats WHERE (user1_id = :user1 AND user2_id = :user2) OR (user1_id = :user2 AND user2_id = :user1)";
$stmt = $db->prepare($query);

// Привязываем параметры user1 и user2 к подготовленному запросу
$stmt->bindParam(':user1', $user_id);
$stmt->bindParam(':user2', $friend_id);

// Выполняем запрос
$stmt->execute();

// Извлекаем данные чата в виде ассоциативного массива
$chat = $stmt->fetch(PDO::FETCH_ASSOC);

if ($chat) {
    // Если чат найден, получаем его идентификатор
    $chat_id = $chat['id'];

    // SQL-запрос для получения сообщений по идентификатору чата, отсортированных по идентификатору сообщения в порядке убывания
    $query = "SELECT * FROM messages WHERE chat_id = :chat_id ORDER BY id DESC";
    $stmt = $db->prepare($query);

    // Привязываем параметр chat_id к подготовленному запросу
    $stmt->bindParam(':chat_id', $chat_id);

    // Выполняем запрос
    $stmt->execute();

    // Извлекаем все сообщения в виде ассоциативного массива
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Преобразуем массив сообщений в формат JSON и выводим его
    echo json_encode($messages);
} else {
    // Если чат не найден, отправляем пустой массив в формате JSON
    echo json_encode([]);
}
?>
