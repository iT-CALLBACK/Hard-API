<?php
// Подключаем файл конфигурации базы данных
include_once '../../config/database.php';

// Получаем данные из тела запроса в формате JSON и декодируем их в объект PHP
$data = json_decode(file_get_contents("php://input"));

// Извлекаем значение поля comment_id из декодированных данных
$comment_id = $data->comment_id;

// Создаем экземпляр класса Database и получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// SQL-запрос для удаления комментария по его идентификатору
$query = "DELETE FROM comments WHERE id = :comment_id";

// Подготавливаем запрос к выполнению
$stmt = $db->prepare($query);

// Привязываем параметр comment_id к подготовленному запросу
$stmt->bindParam(':comment_id', $comment_id);

// Выполняем запрос и проверяем результат
if($stmt->execute()) {
    // Если запрос выполнен успешно, отправляем сообщение об успешном удалении комментария в формате JSON
    echo json_encode(["message" => "Comment removed successfully."]);
} else {
    // Если запрос не выполнен, отправляем сообщение о неудачном удалении комментария в формате JSON
    echo json_encode(["message" => "Unable to remove comment."]);
}
?>
