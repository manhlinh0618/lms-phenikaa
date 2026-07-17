<?php
class Enrollment {
    private $conn;
    private $table_name = "enrollments";

    public $id;
    public $user_id;
    public $course_id;
    public $enrolled_at;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy danh sách khóa học đã đăng ký của user
    public function readByUser($user_id) {
        $query = "SELECT e.*, c.title, c.thumbnail, c.price, u.fullname as instructor_name 
                  FROM " . $this->table_name . " e
                  JOIN courses c ON e.course_id = c.id
                  JOIN users u ON c.instructor_id = u.id
                  WHERE e.user_id = ? AND e.status = 'active'
                  ORDER BY e.enrolled_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        return $stmt;
    }

    // Tạo đăng ký mới
    public function create() {
        // Kiểm tra đã đăng ký chưa
        $check_query = "SELECT id FROM " . $this->table_name . " 
                        WHERE user_id = :user_id AND course_id = :course_id";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bindParam(":user_id", $this->user_id);
        $check_stmt->bindParam(":course_id", $this->course_id);
        $check_stmt->execute();

        if($check_stmt->rowCount() > 0) {
            return false;
        }

        $query = "INSERT INTO " . $this->table_name . " (user_id, course_id) 
                  VALUES (:user_id, :course_id)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":course_id", $this->course_id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Hủy đăng ký
    public function delete() {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = 'inactive' 
                  WHERE user_id = :user_id AND course_id = :course_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":course_id", $this->course_id);

        if($stmt->execute() && $stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }
}
?>