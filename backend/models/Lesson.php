<?php
class Lesson {
    private $conn;
    private $table_name = "lessons";

    public $id;
    public $course_id;
    public $title;
    public $video_url;
    public $content;
    public $lesson_order;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy danh sách bài học của một khóa học
    public function readByCourse($course_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE course_id = ? ORDER BY lesson_order ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $course_id);
        $stmt->execute();
        return $stmt;
    }

    // Tạo bài học mới
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (course_id, title, video_url, content, lesson_order) 
                  VALUES (:course_id, :title, :video_url, :content, :lesson_order)";
        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->content = htmlspecialchars(strip_tags($this->content));

        $stmt->bindParam(":course_id", $this->course_id);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":video_url", $this->video_url);
        $stmt->bindParam(":content", $this->content);
        $stmt->bindParam(":lesson_order", $this->lesson_order);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
