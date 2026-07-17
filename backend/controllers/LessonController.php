<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Lesson.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class LessonController {
    private $db;
    private $lesson;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->lesson = new Lesson($this->db);
    }

    // GET /api/lessons?course_id=1
    public function getLessonsByCourse() {
        if (!isset($_GET['course_id'])) {
            http_response_code(400);
            return json_encode(["message" => "Thiếu course_id."]);
        }

        $stmt = $this->lesson->readByCourse($_GET['course_id']);
        
        $lessons_arr = array();
        $lessons_arr["data"] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($lessons_arr["data"], $row);
        }
        http_response_code(200);
        return json_encode($lessons_arr);
    }

    // POST /api/lessons
    public function createLesson($data) {
        AuthMiddleware::checkRole(['teacher', 'admin']);

        if(empty($data->course_id) || empty($data->title) || empty($data->lesson_order)) {
            http_response_code(400);
            return json_encode(["message" => "Thiếu thông tin bắt buộc."]);
        }

        $this->lesson->course_id = $data->course_id;
        $this->lesson->title = $data->title;
        $this->lesson->video_url = $data->video_url ?? '';
        $this->lesson->content = $data->content ?? '';
        $this->lesson->lesson_order = $data->lesson_order;

        if($this->lesson->create()) {
            http_response_code(201);
            return json_encode(["message" => "Tạo bài giảng thành công."]);
        } else {
            http_response_code(503);
            return json_encode(["message" => "Lỗi hệ thống, không thể tạo bài giảng."]);
        }
    }
}
?>
