<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/Security.php';

class AuthController {
    private $db;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    public function register($data) {
        // Validate dữ liệu đầu vào
        if(empty($data->email) || empty($data->password) || empty($data->fullname) || empty($data->role)) {
            http_response_code(400);
            return json_encode(["message" => "Vui lòng điền đầy đủ thông tin."]);
        }

        if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            return json_encode(["message" => "Email không hợp lệ."]);
        }

        $this->user->email = $data->email;

        // Kiểm tra email đã tồn tại
        if($this->user->emailExists()) {
            http_response_code(400);
            return json_encode(["message" => "Email đã được sử dụng."]);
        }

        $this->user->fullname = $data->fullname;
        // Chỉ cho phép đăng ký student hoặc teacher (admin phải được tạo riêng)
        $this->user->role = in_array($data->role, ['student', 'teacher']) ? $data->role : 'student';
        
        // Hash password sử dụng bcrypt (cost = 12 theo yêu cầu)
        $options = ['cost' => 12];
        $this->user->password_hash = password_hash($data->password, PASSWORD_BCRYPT, $options);

        if($this->user->create()) {
            http_response_code(201);
            return json_encode(["message" => "Đăng ký thành công."]);
        } else {
            http_response_code(503);
            return json_encode(["message" => "Đăng ký thất bại, lỗi hệ thống."]);
        }
    }

    public function login($data) {
        if(empty($data->email) || empty($data->password)) {
            http_response_code(400);
            return json_encode(["message" => "Vui lòng nhập email và mật khẩu."]);
        }

        $this->user->email = $data->email;

        if($this->user->emailExists()) {
            // Verify password
            if(password_verify($data->password, $this->user->password_hash)) {
                // Tạo session (Chống Session Hijacking)
                session_start();
                session_regenerate_id(true); // Tạo ID mới mỗi lần login
                
                $_SESSION['user_id'] = $this->user->id;
                $_SESSION['role'] = $this->user->role;
                $_SESSION['fullname'] = $this->user->fullname;

                http_response_code(200);
                return json_encode([
                    "message" => "Đăng nhập thành công.",
                    "user" => [
                        "id" => $this->user->id,
                        "email" => $this->user->email,
                        "role" => $this->user->role,
                        "fullname" => $this->user->fullname
                    ]
                ]);
            }
        }
        
        http_response_code(401);
        return json_encode(["message" => "Email hoặc mật khẩu không chính xác."]);
    }

    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        http_response_code(200);
        return json_encode(["message" => "Đăng xuất thành công."]);
    }
}
?>
