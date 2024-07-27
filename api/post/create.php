<?php
// Подключаем файл конфигурации базы данных
include_once '../../config/database.php';

// Получаем данные из тела запроса в формате JSON и декодируем их в объект PHP
$data = json_decode(file_get_contents("php://input"));

// Извлекаем значения полей user_id и content из декодированных данных
$user_id = $data->user_id;
$content = $data->content;

// Создаем экземпляр класса Database и получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// SQL-запрос для вставки нового поста в таблицу posts
$query = "INSERT INTO posts (user_id, content) VALUES (:user_id, :content)";

// Подготавливаем запрос к выполнению
$stmt = $db->prepare($query);

// Привязываем параметры user_id и content к подготовленному запросу
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':content', $content);

// Выполняем запрос и проверяем результат
if($stmt->execute()) {
    // Если запрос выполнен успешно, отправляем сообщение об успешном создании поста в формате JSON
    echo json_encode(["message" => "Post created successfully."]);
} else {
    // Если запрос не выполнен, отправляем сообщение о неудачном создании поста в формате JSON
    echo json_encode(["message" => "Unable to create the post."]);
}
?>
