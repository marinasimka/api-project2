<?php
echo "<h3>Тест подключения к MySQL на порту 3307</h3>";

$configs = [
    ['host' => '127.0.0.1:3307', 'user' => 'root', 'pass' => 'newpassword'],
    ['host' => 'localhost:3307', 'user' => 'root', 'pass' => 'newpassword'],
    ['host' => '127.0.0.1:3307', 'user' => 'root', 'pass' => ''],
    ['host' => 'localhost:3307', 'user' => 'root', 'pass' => ''],
];

foreach ($configs as $config) {
    echo "Пробуем: {$config['user']}@{$config['host']}... ";
    
    try {
        $conn = new PDO(
            "mysql:host=" . explode(':', $config['host'])[0] . 
            ";port=" . explode(':', $config['host'])[1] . 
            ";dbname=api_db_navrotskaya",
            $config['user'],
            $config['pass']
        );
        echo "<span style='color:green;'>✓ УСПЕХ!</span><br>";
    } catch (Exception $e) {
        echo "<span style='color:red;'>✗ " . $e->getMessage() . "</span><br>";
    }
}
?>