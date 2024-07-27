<?php
// Подключаем файл конфигурации базы данных
include_once '../../config/database.php';

// Получаем идентификатор пользователя из параметра запроса
$user_id = $_GET['id'];

// Создаем экземпляр класса Database и получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// SQL-запрос для получения данных пользователя по идентификатору
$query = "SELECT * FROM users WHERE id = :id";

// Подготавливаем запрос к выполнению
$stmt = $db->prepare($query);

// Привязываем параметр id к подготовленному запросу
$stmt->bindParam(':id', $user_id);

// Выполняем запрос
$stmt->execute();

// Извлекаем данные пользователя в виде ассоциативного массива
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Проверяем, найден ли пользователь
if($user) {
    // Если пользователь найден, отправляем его данные в формате JSON
    echo json_encode($user);
} else {
    // Если пользователь не найден, отправляем сообщение об ошибке в формате JSON
    echo json_encode(["message" => "User not found."]);
}
?>
