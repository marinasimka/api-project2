<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: X-API-Key, Content-Type");

// 1. Получаем данные запроса
$method = $_SERVER['REQUEST_METHOD'];
$url = $_GET['url'] ?? '';
$parts = explode('/', $url);
$endpoint = $parts[0] ?? '';
$id = $parts[1] ?? null;

// 2. Проверяем API ключ
$api_key = $_SERVER['HTTP_X_API_KEY'] ?? $_GET['api_key'] ?? '';
$valid_key = '$2y$10$EKNJanRosYtQWp0TlUv0PeBsBnTwikCQ9IP0psIWkk7071rh0PhXC';

if ($api_key !== $valid_key) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Invalid API key']);
    exit;
}

// 3. Подключаемся к БД
try {
    $pdo = new PDO(
        "mysql:host=127.0.0.1;port=3307;dbname=api_db_navrotskaya;charset=utf8mb4",
        "root",
        "newpassword",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// 4. Обрабатываем запросы
if ($method == 'GET' && $endpoint == 'addresses') {
    if ($id) {
        // GET /addresses/{id} - один адрес
        $stmt = $pdo->prepare("SELECT id, user_id, street, city, zip_code, created_at FROM addresses WHERE id = ?");
        $stmt->execute([$id]);
        $address = $stmt->fetch();
        
        if ($address) {
            echo json_encode(['success' => true, 'data' => $address]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Address not found']);
        }
    } else {
        // GET /addresses - все адреса
        $stmt = $pdo->query("SELECT id, user_id, street, city, zip_code, created_at FROM addresses");
        $addresses = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'data' => $addresses,
            'count' => count($addresses),
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
} elseif ($method == 'POST' && $endpoint == 'addresses') {
    // POST /addresses - создать адрес
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || empty($input['street']) || empty($input['city']) || empty($input['zip_code'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }
    
    $stmt = $pdo->prepare("INSERT INTO addresses (user_id, street, city, zip_code) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $input['user_id'] ?? 1,
        $input['street'],
        $input['city'],
        $input['zip_code']
    ]);
    
    $new_id = $pdo->lastInsertId();
    $stmt = $pdo->prepare("SELECT id, user_id, street, city, zip_code, created_at FROM addresses WHERE id = ?");
    $stmt->execute([$new_id]);
    $new_address = $stmt->fetch();
    
    http_response_code(201);
    echo json_encode(['success' => true, 'message' => 'Address created', 'data' => $new_address]);
    
} elseif ($method == 'PUT' && $endpoint == 'addresses' && $id) {
    // PUT /addresses/{id} - обновить адрес
    $input = json_decode(file_get_contents('php://input'), true);
    
    $stmt = $pdo->prepare("UPDATE addresses SET street = ?, city = ?, zip_code = ?, updated_at = NOW() WHERE id = ?");
    $stmt->execute([
        $input['street'] ?? '',
        $input['city'] ?? '',
        $input['zip_code'] ?? '',
        $id
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Address updated']);
    
} elseif ($method == 'DELETE' && $endpoint == 'addresses' && $id) {
    // DELETE /addresses/{id} - удалить адрес
    $stmt = $pdo->prepare("DELETE FROM addresses WHERE id = ?");
    $stmt->execute([$id]);
    
    echo json_encode(['success' => true, 'message' => 'Address deleted']);
    
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Endpoint not found']);
}
?>