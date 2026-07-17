<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Quiz.php';
require_once __DIR__ . '/../models/Submission.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class QuizController {
    private $db;
    private $quiz;
    private $submission;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->quiz = new Quiz($this->db);
        $this->submission = new Submission($this->db);
    }

    // GET /api/quizzes?lesson_id=1
    public function getQuizzesByLesson() {
        if (!isset($_GET['lesson_id'])) {
            http_response_code(400);
            return json_encode(["message" => "Thiếu lesson_id."]);
        }

        $stmt = $this->quiz->readByLesson($_GET['lesson_id']);
        $quizzes_arr = array();
        $quizzes_arr["data"] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($quizzes_arr["data"], $row);
        }
        http_response_code(200);
        return json_encode($quizzes_arr);
    }

    // GET /api/quizzes/{id}/questions
    public function getQuizQuestions($id) {
        AuthMiddleware::checkAuthenticated();
        
        $this->quiz->id = $id;
        $questions = $this->quiz->getQuestions();
        
        if($questions) {
            http_response_code(200);
            return json_encode(["data" => $questions]);
        } else {
            http_response_code(404);
            return json_encode(["message" => "Quiz không tồn tại."]);
        }
    }

    // POST /api/quizzes/submit
    public function submitQuiz($data) {
        AuthMiddleware::checkAuthenticated();

        if(empty($data->quiz_id) || empty($data->answers)) {
            http_response_code(400);
            return json_encode(["message" => "Thiếu thông tin."]);
        }

        $this->submission->user_id = $_SESSION['user_id'];
        $this->submission->quiz_id = $data->quiz_id;
        $this->submission->answers = json_encode($data->answers);

        // Tính điểm
        $score = $this->quiz->calculateScore($data->quiz_id, $data->answers);
        $this->submission->score = $score;

        if($this->submission->create()) {
            http_response_code(201);
            return json_encode([
                "message" => "Nộp bài thành công.",
                "score" => $score
            ]);
        } else {
            http_response_code(503);
            return json_encode(["message" => "Lỗi hệ thống."]);
        }
    }
}
?>