<?php
// Подключаем файл конфигурации базы данных
include_once '../../config/database.php';

// Получаем данные из тела запроса в формате JSON и декодируем их в объект PHP
$data = json_decode(file_get_contents("php://input"));

// Извлекаем значения полей post_id, user_id и content из декодированных данных
$post_id = $data->post_id;
$user_id = $data->user_id;
$content = $data->content;

// Создаем экземпляр класса Database и получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// SQL-запрос для вставки нового комментария в таблицу comments
$query = "INSERT INTO comments (post_id, user_id, content) VALUES (:post_id, :user_id, :content)";

// Подготавливаем запрос к выполнению
$stmt = $db->prepare($query);

// Привязываем параметры post_id, user_id и content к подготовленному запросу
$stmt->bindParam(':post_id', $post_id);
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':content', $content);

// Выполняем запрос и проверяем результат
if($stmt->execute()) {
    // Если запрос выполнен успешно, отправляем сообщение об успешном добавлении комментария в формате JSON
    echo json_encode(["message" => "Comment added successfully."]);
} else {
    // Если запрос не выполнен, отправляем сообщение о неудачном добавлении комментария в формате JSON
    echo json_encode(["message" => "Unable to add comment."]);
}
?>
