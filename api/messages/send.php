<?php
// Подключаем файл конфигурации базы данных
include_once '../../config/database.php';

// Получаем данные из тела запроса в формате JSON и декодируем их в объект PHP
$data = json_decode(file_get_contents("php://input"));

// Извлекаем значения полей sender_id, receiver_id и content из декодированных данных
$sender_id = $data->sender_id;
$receiver_id = $data->receiver_id;
$content = $data->content;

// Создаем экземпляр класса Database и получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// SQL-запрос для проверки существования чата между двумя пользователями
$query = "SELECT id FROM chats WHERE (user1_id = :user1 AND user2_id = :user2) OR (user1_id = :user2 AND user2_id = :user1)";
$stmt = $db->prepare($query);

// Привязываем параметры user1 и user2 к подготовленному запросу
$stmt->bindParam(':user1', $sender_id);
$stmt->bindParam(':user2', $receiver_id);

// Выполняем запрос
$stmt->execute();

// Извлекаем данные чата в виде ассоциативного массива
$chat = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$chat) {
    // Если чат не найден, создаем новый чат
    $query = "INSERT INTO chats (user1_id, user2_id) VALUES (:user1, :user2)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user1', $sender_id);
    $stmt->bindParam(':user2', $receiver_id);
    $stmt->execute();
    // Получаем идентификатор нового чата
    $chat_id = $db->lastInsertId();
} else {
    // Если чат найден, используем его идентификатор
    $chat_id = $chat['id'];
}

// SQL-запрос для вставки нового сообщения в таблицу messages
$query = "INSERT INTO messages (sender_id, receiver_id, content, chat_id) VALUES (:sender_id, :receiver_id, :content, :chat_id)";
$stmt = $db->prepare($query);

// Привязываем параметры sender_id, receiver_id, content и chat_id к подготовленному запросу
$stmt->bindParam(':sender_id', $sender_id);
$stmt->bindParam(':receiver_id', $receiver_id);
$stmt->bindParam(':content', $content);
$stmt->bindParam(':chat_id', $chat_id);

// Выполняем запрос и проверяем результат
if ($stmt->execute()) {
    // Если запрос выполнен успешно, отправляем сообщение об успешной отправке в формате JSON
    echo json_encode(["message" => "Сообщение отправлено успешно."]);
} else {
    // Если запрос не выполнен, отправляем сообщение о неудачной отправке в формате JSON
    echo json_encode(["message" => "Не удалось отправить сообщение."]);
}
?>
