<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h3>Тест подключения к MySQL из PHP</h3>";

// Проверим длину строк
$username = "root";
$password = "";

echo "Username: '" . $username . "' (длина: " . strlen($username) . ")<br>";
echo "Password: '" . $password . "' (длина: " . strlen($password) . ")<br>";

try {
    $conn = new PDO(
        "mysql:host=localhost;dbname=api_db_navrotskaya;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    echo "<div style='color: green; padding: 10px; border: 2px solid green;'>✓ Прямое подключение УСПЕШНО!</div>";
    
    // Проверим содержимое таблиц
    echo "<h4>Таблица api_keys:</h4>";
    $stmt = $conn->query("SELECT * FROM api_keys");
    $keys = $stmt->fetchAll();
    
    if (empty($keys)) {
        echo "Таблица пуста!<br>";
    } else {
        foreach ($keys as $key) {
            echo "ID: " . $key['id'] . ", API Key: " . substr($key['api_key'], 0, 30) . "...<br>";
        }
    }
    
    echo "<h4>Таблица addresses:</h4>";
    $stmt = $conn->query("SELECT COUNT(*) as count FROM addresses");
    $result = $stmt->fetch();
    echo "Количество адресов: " . $result['count'] . "<br>";
    
} catch (PDOException $e) {
    echo "<div style='color: red; padding: 10px; border: 2px solid red;'>✗ Ошибка: " . $e->getMessage() . "</div>";
}
?>