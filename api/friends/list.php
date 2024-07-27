<?php
// Подключаем файл конфигурации базы данных
include_once '../../config/database.php';

// Получаем значение user_id из параметра запроса
$user_id = $_GET['user_id'];

// Создаем экземпляр класса Database и получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// SQL-запрос для получения списка друзей пользователя по user_id
$query = "SELECT users.id, users.username FROM friends JOIN users ON friends.friend_id = users.id WHERE friends.user_id = :user_id";

// Подготавливаем запрос к выполнению
$stmt = $db->prepare($query);

// Привязываем параметр user_id к подготовленному запросу
$stmt->bindParam(':user_id', $user_id);

// Выполняем запрос
$stmt->execute();

// Извлекаем все записи в виде ассоциативного массива
$friends = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Преобразуем массив друзей в формат JSON и выводим его
echo json_encode($friends);
?>
