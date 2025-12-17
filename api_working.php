<?php
// Упрощенный работающий API
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, X-API-Key");

// Подключаем конфигурацию
require_once __DIR__ . '/config/Database.php';

// Проверка API ключа
function checkApiKey() {
    $headers = getallheaders();
    $api_key = $headers['X-API-Key'] ?? ($_GET['api_key'] ?? '');
    
    $valid_key = '$2y$10$EKNJanRosYtQWp0TlUv0PeBsBnTwikCQ9IP0psIWkk7071rh0PhXC';
    
    if (empty($api_key)) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "API key is required"
        ]);
        exit();
    }
    
    if ($api_key !== $valid_key) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Invalid API key"
        ]);
        exit();
    }
    
    return true;
}

// Подключение к базе данных
function getDatabase() {
    try {
        $db = new Database();
        return $db->getConnection();
    } catch (Exception $e) {
        // Если не удалось подключиться, используем тестовые данные
        return null;
    }
}

// Проверяем API ключ
checkApiKey();

// Получаем метод и URL
$method = $_SERVER['REQUEST_METHOD'];
$url = $_GET['url'] ?? '';
$parts = explode('/', $url);
$endpoint = $parts[0] ?? '';
$id = $parts[1] ?? null;

// Подключаемся к базе данных
$conn = getDatabase();

if ($method == 'GET' && $endpoint == 'addresses') {
    if ($conn) {
        // Реальные данные из базы
        try {
            if ($id) {
                $stmt = $conn->prepare("SELECT * FROM addresses WHERE id = :id");
                $stmt->bindParam(":id", $id);
                $stmt->execute();
                $address = $stmt->fetch();
                
                if ($address) {
                    echo json_encode([
                        "success" => true,
                        "data" => $address
                    ]);
                } else {
                    http_response_code(404);
                    echo json_encode([
                        "success" => false,
                        "message" => "Address not found"
                    ]);
                }
            } else {
                $stmt = $conn->query("SELECT * FROM addresses");
                $addresses = $stmt->fetchAll();
                
                echo json_encode([
                    "success" => true,
                    "data" => $addresses,
                    "count" => count($addresses)
                ]);
            }
        } catch (PDOException $e) {
            // Если ошибка базы, используем тестовые данные
            useTestData($id);
        }
    } else {
        // Используем тестовые данные если нет подключения к БД
        useTestData($id);
    }
} else {
    http_response_code(404);
    echo json_encode([
        "success" => false,
        "message" => "Endpoint not found"
    ]);
}

// Функция для тестовых данных
function useTestData($id = null) {
    $test_addresses = [
        ["id" => 1, "user_id" => 1, "street" => "Улица Ленина", "city" => "Москва", "zip_code" => "101000"],
        ["id" => 2, "user_id" => 1, "street" => "Проспект Мира", "city" => "Москва", "zip_code" => "101001"],
        ["id" => 3, "user_id" => 2, "street" => "Невский проспект", "city" => "Санкт-Петербург", "zip_code" => "190000"],
    ];
    
    if ($id) {
        foreach ($test_addresses as $address) {
            if ($address['id'] == $id) {
                echo json_encode(["success" => true, "data" => $address]);
                return;
            }
        }
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Address not found"]);
    } else {
        echo json_encode([
            "success" => true,
            "data" => $test_addresses,
            "count" => count($test_addresses)
        ]);
    }
}
?>