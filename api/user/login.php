<?php
// Подключаем файл конфигурации базы данных
include_once '../../config/database.php';

// Получаем данные из тела запроса в формате JSON и декодируем их в объект PHP
$data = json_decode(file_get_contents("php://input"));

// Извлекаем значения полей email и password из декодированных данных
$email = $data->email;
$password = $data->password;

// Создаем экземпляр класса Database и получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// SQL-запрос для получения данных пользователя по email
$query = "SELECT * FROM users WHERE email = :email";

// Подготавливаем запрос к выполнению
$stmt = $db->prepare($query);

// Привязываем параметр email к подготовленному запросу
$stmt->bindParam(':email', $email);

// Выполняем запрос
$stmt->execute();

// Извлекаем данные пользователя в виде ассоциативного массива
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Проверяем, найден ли пользователь и совпадает ли хэш пароля
if ($user && password_verify($password, $user['password'])) {
    // Если пользователь найден и пароль совпадает, отправляем данные пользователя в формате JSON
    echo json_encode([
        "user" => [
            "id" => $user['id'], 
            "username" => $user['username'], 
            "email" => $user['email']
        ]
    ]);
} else {
    // Если пользователь не найден или пароль не совпадает, отправляем сообщение об ошибке в формате JSON
    echo json_encode(["message" => "Неверные учетные данные"]);
}
?>
