<?php
class Submission {
    private $conn;
    private $table_name = "submissions";

    public $id;
    public $user_id;
    public $quiz_id;
    public $score;
    public $answers;
    public $submitted_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Tạo submission mới
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (user_id, quiz_id, score, answers) 
                  VALUES (:user_id, :quiz_id, :score, :answers)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":quiz_id", $this->quiz_id);
        $stmt->bindParam(":score", $this->score);
        $stmt->bindParam(":answers", $this->answers);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Lấy điểm của user cho một quiz
    public function getScore($user_id, $quiz_id) {
        $query = "SELECT score FROM " . $this->table_name . " 
                  WHERE user_id = :user_id AND quiz_id = :quiz_id 
                  ORDER BY submitted_at DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":quiz_id", $quiz_id);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['score'];
        }
        return null;
    }
}
?>