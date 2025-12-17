<?php
// Указываем абсолютный путь
$base_dir = dirname(__DIR__);
require_once $base_dir . '/config/Database.php';

class AddressModel {
    private $conn;
    private $table = "addresses";
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    // Получить все адреса
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    // Получить один адрес по ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt;
    }
    
    // Создать новый адрес
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " (user_id, street, city, zip_code) VALUES (:user_id, :street, :city, :zip_code)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":user_id", $data['user_id']);
        $stmt->bindParam(":street", $data['street']);
        $stmt->bindParam(":city", $data['city']);
        $stmt->bindParam(":zip_code", $data['zip_code']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    // Обновить адрес
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " SET user_id = :user_id, street = :street, city = :city, zip_code = :zip_code WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":user_id", $data['user_id']);
        $stmt->bindParam(":street", $data['street']);
        $stmt->bindParam(":city", $data['city']);
        $stmt->bindParam(":zip_code", $data['zip_code']);
        
        return $stmt->execute();
    }
    
    // Удалить адрес
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }
}
?>