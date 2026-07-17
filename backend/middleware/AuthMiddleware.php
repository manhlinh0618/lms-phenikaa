<?php
class AuthMiddleware {
    public static function checkAuthenticated() {
        session_start();
        if(!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(["message" => "Yêu cầu đăng nhập."]);
            exit();
        }
    }

    public static function checkRole($requiredRoles) {
        self::checkAuthenticated();
        
        if(!in_array($_SESSION['role'], $requiredRoles)) {
            http_response_code(403);
            echo json_encode(["message" => "Bạn không có quyền truy cập chức năng này."]);
            exit();
        }
    }
}
?>
