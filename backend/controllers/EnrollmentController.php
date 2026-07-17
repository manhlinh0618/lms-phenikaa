<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Enrollment.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class EnrollmentController {
    private $db;
    private $enrollment;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->enrollment = new Enrollment($this->db);
    }

    // GET /api/enrollments
    public function getMyCourses() {
        AuthMiddleware::checkAuthenticated();
        
        $stmt = $this->enrollment->readByUser($_SESSION['user_id']);
        $enrollments_arr = array();
        $enrollments_arr["data"] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($enrollments_arr["data"], $row);
        }
        http_response_code(200);
        return json_encode($enrollments_arr);
    }

    // POST /api/enrollments
    public function enrollCourse($data) {
        AuthMiddleware::checkAuthenticated();

        if(empty($data->course_id)) {
            http_response_code(400);
            return json_encode(["message" => "Thiếu course_id."]);
        }

        $this->enrollment->user_id = $_SESSION['user_id'];
        $this->enrollment->course_id = $data->course_id;

        if($this->enrollment->create()) {
            http_response_code(201);
            return json_encode(["message" => "Đăng ký khóa học thành công."]);
        } else {
            http_response_code(503);
            return json_encode(["message" => "Lỗi hệ thống hoặc đã đăng ký."]);
        }
    }

    // DELETE /api/enrollments/{course_id}
    public function unenrollCourse($course_id) {
        AuthMiddleware::checkAuthenticated();

        $this->enrollment->user_id = $_SESSION['user_id'];
        $this->enrollment->course_id = $course_id;

        if($this->enrollment->delete()) {
            http_response_code(200);
            return json_encode(["message" => "Hủy đăng ký thành công."]);
        } else {
            http_response_code(404);
            return json_encode(["message" => "Không tìm thấy đăng ký."]);
        }
    }
}
?>