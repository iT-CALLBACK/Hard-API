<?php
// Определяем класс Database для работы с базой данных
class Database {
    // Устанавливаем свойства класса для параметров подключения к базе данных
    private $host = "localhost"; // Адрес сервера базы данных
    private $db_name = "social_network"; // Имя базы данных
    private $username = "root"; // Имя пользователя базы данных
    private $password = ""; // Пароль пользователя базы данных
    public $conn; // Свойство для хранения соединения с базой данных

    // Метод для получения соединения с базой данных
    public function getConnection() {
        // Инициализируем свойство соединения как null
        $this->conn = null;
        try {
            // Пытаемся установить соединение с базой данных с использованием PDO
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            // Устанавливаем кодировку для соединения
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            // Если произошла ошибка при подключении, выводим сообщение об ошибке
            echo "Connection error: " . $exception->getMessage();
        }
        // Возвращаем соединение с базой данных
        return $this->conn;
    }
}
?>
