<?php
// Подключаем файл конфигурации базы данных
include_once '../../config/database.php';

// Получаем данные из тела запроса в формате JSON и декодируем их в объект PHP
$data = json_decode(file_get_contents("php://input"));

// Извлекаем значения полей user_id и friend_id из декодированных данных
$user_id = $data->user_id;
$friend_id = $data->friend_id;

// Создаем экземпляр класса Database и получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// SQL-запрос для вставки новой записи в таблицу friends
$query = "INSERT INTO friends (user_id, friend_id) VALUES (:user_id, :friend_id)";

// Подготавливаем запрос к выполнению
$stmt = $db->prepare($query);

// Привязываем параметры user_id и friend_id к подготовленному запросу
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':friend_id', $friend_id);

// Выполняем запрос и проверяем результат
if($stmt->execute()) {
    // Если запрос выполнен успешно, отправляем сообщение об успешном добавлении друга в формате JSON
    echo json_encode(["message" => "Друг добавлен успешно."]);
} else {
    // Если запрос не выполнен, отправляем сообщение о неудачном добавлении друга в формате JSON
    echo json_encode(["message" => "Не удалось добавить друга."]);
}
?>
