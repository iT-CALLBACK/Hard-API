<?php
// Подключаем файл конфигурации базы данных
include_once '../../config/database.php';

// Получаем данные из тела запроса в формате JSON и декодируем их в объект PHP
$data = json_decode(file_get_contents("php://input"));

// Извлекаем значения полей username, email и password из декодированных данных
$username = $data->username;
$email = $data->email;
$password = password_hash($data->password, PASSWORD_BCRYPT); // Хэшируем пароль с использованием алгоритма BCRYPT

// Создаем экземпляр класса Database и получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// SQL-запрос для вставки нового пользователя в таблицу users
$query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";

// Подготавливаем запрос к выполнению
$stmt = $db->prepare($query);

// Привязываем параметры к подготовленному запросу
$stmt->bindParam(':username', $username);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':password', $password);

// Выполняем запрос и проверяем результат
if($stmt->execute()) {
    // Если запрос выполнен успешно, отправляем сообщение об успешной регистрации в формате JSON
    echo json_encode(["message" => "User registered successfully."]);
} else {
    // Если запрос не выполнен, отправляем сообщение о неудачной регистрации в формате JSON
    echo json_encode(["message" => "Unable to register the user."]);
}
?>
