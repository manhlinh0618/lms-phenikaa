<?php
class Quiz {
    private $conn;
    private $table_name = "quizzes";

    public $id;
    public $lesson_id;
    public $title;
    public $description;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy danh sách quiz của một bài học
    public function readByLesson($lesson_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE lesson_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $lesson_id);
        $stmt->execute();
        return $stmt;
    }

    // Lấy câu hỏi của quiz
    public function getQuestions() {
        $query = "SELECT id, question_text, options FROM questions WHERE quiz_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tính điểm
    public function calculateScore($quiz_id, $answers) {
        $query = "SELECT id, correct_option FROM questions WHERE quiz_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $quiz_id);
        $stmt->execute();

        $correct = 0;
        $total = $stmt->rowCount();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (isset($answers[$row['id']]) && $answers[$row['id']] === $row['correct_option']) {
                $correct++;
            }
        }

        return $total > 0 ? ($correct / $total) * 100 : 0;
    }
}
?>