<?php
class Database {
    // Thông tin kết nối - Cấu hình cho XAMPP (MySQL)
    private $host = "localhost";
    private $db_name = "lms_phenikaa";
    private $username = "root";
    private $password = "";
    public $conn;

    // Lấy kết nối database
    public function getConnection() {
        $this->conn = null;

        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            
            // Cấu hình PDO: báo lỗi khi có exception, trả về mảng kết hợp
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            echo "Lỗi kết nối cơ sở dữ liệu: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>
