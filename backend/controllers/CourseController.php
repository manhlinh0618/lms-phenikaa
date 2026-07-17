<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class CourseController {
    private $db;
    private $course;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->course = new Course($this->db);
    }

    // GET /api/courses
    public function getAllCourses() {
        $stmt = $this->course->read();
        $num = $stmt->rowCount();

        if($num > 0) {
            $courses_arr = array();
            $courses_arr["data"] = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                array_push($courses_arr["data"], $row);
            }
            http_response_code(200);
            return json_encode($courses_arr);
        } else {
            http_response_code(200);
            return json_encode(["data" => [], "message" => "Chưa có khóa học nào."]);
        }
    }

    // GET /api/courses/{id}
    public function getCourse($id) {
        $this->course->id = $id;
        $course_data = $this->course->readOne();

        if($course_data != null) {
            http_response_code(200);
            return json_encode($course_data);
        } else {
            http_response_code(404);
            return json_encode(["message" => "Không tìm thấy khóa học."]);
        }
    }

    // POST /api/courses
    public function createCourse($data) {
        // Chỉ Teacher và Admin mới được tạo khóa học
        AuthMiddleware::checkRole(['teacher', 'admin']);

        if(empty($data->title) || empty($data->description)) {
            http_response_code(400);
            return json_encode(["message" => "Thiếu thông tin bắt buộc."]);
        }

        $this->course->title = $data->title;
        $this->course->description = $data->description;
        $this->course->price = $data->price ?? 0;
        $this->course->thumbnail = $data->thumbnail ?? '';
        $this->course->instructor_id = $_SESSION['user_id']; // Lấy từ phiên đăng nhập hiện tại

        if($this->course->create()) {
            http_response_code(201);
            return json_encode(["message" => "Tạo khóa học thành công."]);
        } else {
            http_response_code(503);
            return json_encode(["message" => "Lỗi hệ thống, không thể tạo khóa học."]);
        }
    }
}
?>
