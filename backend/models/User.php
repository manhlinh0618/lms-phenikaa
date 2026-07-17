<?php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $email;
    public $password_hash;
    public $role;
    public $fullname;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Kiểm tra email đã tồn tại
    public function emailExists() {
        $query = "SELECT id, password_hash, role, fullname FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        
        $this->email = htmlspecialchars(strip_tags($this->email));
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->password_hash = $row['password_hash'];
            $this->role = $row['role'];
            $this->fullname = $row['fullname'];
            return true;
        }
        return false;
    }

    // Tạo người dùng mới
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (email, password_hash, role, fullname) VALUES (:email, :password_hash, :role, :fullname)";
        $stmt = $this->conn->prepare($query);

        // Sanitize data (Ngăn chặn XSS)
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->role = htmlspecialchars(strip_tags($this->role));
        $this->fullname = htmlspecialchars(strip_tags($this->fullname));

        // Bind parameters (Ngăn chặn SQL Injection)
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password_hash', $this->password_hash);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':fullname', $this->fullname);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
