<?php
require_once 'Database.php';

class Auth {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        
        if (!$this->conn) {
            $this->sendJsonError("Database connection failed", 500);
        }
    }
    
    public function validateApiKey() {
        $headers = getallheaders();
        $api_key = $headers['X-API-Key'] ?? ($_GET['api_key'] ?? '');
        
        if (empty($api_key)) {
            $this->sendJsonError("API key is required", 401);
            return false;
        }
        
        try {
            $query = "SELECT * FROM api_keys WHERE api_key = :api_key AND is_active = 1 LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":api_key", $api_key);
            $stmt->execute();
            
            if ($stmt->rowCount() === 0) {
                $this->sendJsonError("Invalid or inactive API key", 401);
                return false;
            }
            
            return true;
        } catch (Exception $e) {
            $this->sendJsonError("Database error: " . $e->getMessage(), 500);
            return false;
        }
    }
    
    private function sendJsonError($message, $code = 500) {
        http_response_code($code);
        echo json_encode([
            "success" => false,
            "message" => $message
        ]);
        exit();
    }
}
?>