<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h3>Прямой тест PHP → MySQL</h3>";

// Пробуем разные варианты подключения
$variants = [
    ["host" => "localhost", "user" => "root", "pass" => "", "desc" => "root с пустым паролем"],
    ["host" => "127.0.0.1", "user" => "root", "pass" => "", "desc" => "root @ 127.0.0.1"],
    ["host" => "localhost", "user" => "root", "pass" => "root", "desc" => "root с паролем root"],
    ["host" => "localhost", "user" => "api_user", "pass" => "api_password", "desc" => "api_user"],
    ["host" => "localhost", "user" => "api_user", "pass" => "", "desc" => "api_user без пароля"],
];

foreach ($variants as $variant) {
    echo "<h4>Пробуем: " . $variant['desc'] . "</h4>";
    
    try {
        $conn = new PDO(
            "mysql:host=" . $variant['host'] . ";dbname=api_db_navrotskaya;charset=utf8mb4",
            $variant['user'],
            $variant['pass'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
        
        echo "<div style='color: green;'>✓ УСПЕХ! Подключились как " . $variant['user'] . "</div>";
        
        // Проверим данные
        $stmt = $conn->query("SELECT COUNT(*) as count FROM addresses");
        $result = $stmt->fetch();
        echo "Адресов в базе: " . $result['count'] . "<br>";
        
        $conn = null;
        
        // Если этот вариант работает, запомним его
        $working_config = $variant;
        break;
        
    } catch (PDOException $e) {
        echo "<div style='color: red;'>✗ Ошибка: " . $e->getMessage() . "</div>";
    }
    
    echo "<hr>";
}

// Если нашли рабочий вариант
if (isset($working_config)) {
    echo "<h3 style='color: green;'>Рабочий вариант найден!</h3>";
    echo "Используй эти настройки в Database.php:<br>";
    echo "host: " . $working_config['host'] . "<br>";
    echo "username: " . $working_config['user'] . "<br>";
    echo "password: '" . $working_config['pass'] . "'<br>";
}
?>