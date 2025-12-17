<?php
class Database {
    public function getConnection() {
        try {
            // ОБРАТИТЕ ВНИМАНИЕ НА ПОРТ 3307!
            $conn = new PDO(
                "mysql:host=127.0.0.1;port=3307;dbname=api_db_navrotskaya;charset=utf8mb4",
                "root",
                "newpassword", // ← Тот же пароль
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
            return $conn;
        } catch (PDOException $e) {
            // Если не сработало с 127.0.0.1:3307, пробуем localhost
            try {
                $conn = new PDO(
                    "mysql:host=localhost;port=3307;dbname=api_db_navrotskaya;charset=utf8mb4",
                    "root",
                    "newpassword",
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
                return $conn;
            } catch (PDOException $e2) {
                error_log("MySQL Error: " . $e2->getMessage());
                return null;
            }
        }
    }
}
?>