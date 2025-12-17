<?php
require_once 'config/Database.php';

$database = new Database();
$conn = $database->getConnection();

if ($conn) {
    echo "Database connection successful!<br>";
    
    // Проверим таблицы
    $stmt = $conn->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Tables in database:<br>";
    foreach ($tables as $table) {
        echo "- " . $table . "<br>";
    }
} else {
    echo "Database connection failed!";
}
?>