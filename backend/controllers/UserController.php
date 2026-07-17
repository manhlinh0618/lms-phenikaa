<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class UserController {
    private $db;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    // GET /api/users/profile
    public function getProfile() {
        AuthMiddleware::checkAuthenticated();
        
        $this->user->id = $_SESSION['user_id'];
        $query = "SELECT id, email, fullname, role, created_at FROM users WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $this->user->id);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user) {
            http_response_code(200);
            return json_encode($user);
        } else {
            http_response_code(404);
            return json_encode(["message" => "Không tìm thấy user."]);
        }
    }

    // PUT /api/users/profile
    public function updateProfile($data) {
        AuthMiddleware::checkAuthenticated();
        
        if(empty($data->fullname)) {
            http_response_code(400);
            return json_encode(["message" => "Vui lòng nhập họ tên."]);
        }

        $query = "UPDATE users SET fullname = :fullname WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":fullname", $data->fullname);
        $stmt->bindParam(":id", $_SESSION['user_id']);
        
        if($stmt->execute()) {
            http_response_code(200);
            return json_encode(["message" => "Cập nhật thành công."]);
        } else {
            http_response_code(503);
            return json_encode(["message" => "Cập nhật thất bại."]);
        }
    }
}
?>