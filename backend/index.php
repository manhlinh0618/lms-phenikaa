<?php
// Bắt đầu session trước khi gửi bất kỳ output nào
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Tắt hiển thị lỗi trên production, bật trên development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cấu hình CORS (Cross-Origin Resource Sharing)
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Max-Age: 86400"); // Cache preflight request 24h

// Xử lý preflight request OPTIONS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Hàm xử lý response dạng JSON
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

// Lấy URI và method
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Parse URL để loại bỏ query string
$uri = parse_url($requestUri, PHP_URL_PATH);
$uriSegments = explode('/', trim($uri, '/'));

// Debug: In ra các segments để kiểm tra
error_log("URI Segments: " . print_r($uriSegments, true));

// Kiểm tra nếu là API request
if (isset($uriSegments[0]) && $uriSegments[0] === 'api') {
    $endpoint = $uriSegments[1] ?? '';
    $subEndpoint = $uriSegments[2] ?? null;
    $subSubEndpoint = $uriSegments[3] ?? null;
    
    // Lấy dữ liệu từ body request
    $inputData = json_decode(file_get_contents("php://input"), false);
    
    // Debug: Log request
    error_log("Endpoint: " . $endpoint);
    error_log("Method: " . $requestMethod);
    error_log("Data: " . print_r($inputData, true));
    
    try {
        switch ($endpoint) {
            // ==================== AUTH ROUTES ====================
            case 'auth':
                require_once __DIR__ . '/controllers/AuthController.php';
                $auth = new AuthController();
                
                if ($requestMethod === 'POST') {
                    switch ($subEndpoint) {
                        case 'register':
                            echo $auth->register($inputData);
                            break;
                        case 'login':
                            echo $auth->login($inputData);
                            break;
                        case 'logout':
                            echo $auth->logout();
                            break;
                        default:
                            sendJsonResponse(['error' => 'Auth action not found'], 404);
                    }
                } else {
                    sendJsonResponse(['error' => 'Method not allowed'], 405);
                }
                break;
            
            // ==================== COURSE ROUTES ====================
            case 'courses':
                require_once __DIR__ . '/controllers/CourseController.php';
                $courseCtrl = new CourseController();
                
                if ($requestMethod === 'GET') {
                    if ($subEndpoint) {
                        echo $courseCtrl->getCourse($subEndpoint);
                    } else {
                        echo $courseCtrl->getAllCourses();
                    }
                } elseif ($requestMethod === 'POST') {
                    echo $courseCtrl->createCourse($inputData);
                } elseif ($requestMethod === 'PUT' && $subEndpoint) {
                    // Cập nhật khóa học (nếu có)
                    echo $courseCtrl->updateCourse($subEndpoint, $inputData);
                } elseif ($requestMethod === 'DELETE' && $subEndpoint) {
                    // Xóa khóa học (nếu có)
                    echo $courseCtrl->deleteCourse($subEndpoint);
                } else {
                    sendJsonResponse(['error' => 'Method not allowed'], 405);
                }
                break;
            
            // ==================== LESSON ROUTES ====================
            case 'lessons':
                require_once __DIR__ . '/controllers/LessonController.php';
                $lessonCtrl = new LessonController();
                
                if ($requestMethod === 'GET') {
                    if ($subEndpoint) {
                        echo $lessonCtrl->getLesson($subEndpoint);
                    } elseif (isset($_GET['course_id'])) {
                        echo $lessonCtrl->getLessonsByCourse();
                    } else {
                        sendJsonResponse(['message' => 'Thiếu tham số course_id'], 400);
                    }
                } elseif ($requestMethod === 'POST') {
                    echo $lessonCtrl->createLesson($inputData);
                } elseif ($requestMethod === 'PUT' && $subEndpoint) {
                    echo $lessonCtrl->updateLesson($subEndpoint, $inputData);
                } elseif ($requestMethod === 'DELETE' && $subEndpoint) {
                    echo $lessonCtrl->deleteLesson($subEndpoint);
                } else {
                    sendJsonResponse(['error' => 'Method not allowed'], 405);
                }
                break;
            
            // ==================== ENROLLMENT ROUTES ====================
            case 'enrollments':
                require_once __DIR__ . '/controllers/EnrollmentController.php';
                $enrollmentCtrl = new EnrollmentController();
                
                if ($requestMethod === 'GET') {
                    echo $enrollmentCtrl->getMyCourses();
                } elseif ($requestMethod === 'POST') {
                    echo $enrollmentCtrl->enrollCourse($inputData);
                } elseif ($requestMethod === 'DELETE' && $subEndpoint) {
                    echo $enrollmentCtrl->unenrollCourse($subEndpoint);
                } else {
                    sendJsonResponse(['error' => 'Method not allowed'], 405);
                }
                break;
            
            // ==================== QUIZ ROUTES ====================
            case 'quizzes':
                require_once __DIR__ . '/controllers/QuizController.php';
                $quizCtrl = new QuizController();
                
                if ($requestMethod === 'GET') {
                    if ($subEndpoint && $subSubEndpoint === 'questions') {
                        echo $quizCtrl->getQuizQuestions($subEndpoint);
                    } elseif (isset($_GET['lesson_id'])) {
                        echo $quizCtrl->getQuizzesByLesson();
                    } else {
                        sendJsonResponse(['message' => 'Thiếu tham số'], 400);
                    }
                } elseif ($requestMethod === 'POST' && $subEndpoint === 'submit') {
                    echo $quizCtrl->submitQuiz($inputData);
                } elseif ($requestMethod === 'POST') {
                    echo $quizCtrl->createQuiz($inputData);
                } else {
                    sendJsonResponse(['error' => 'Method not allowed'], 405);
                }
                break;
            
            // ==================== USER ROUTES ====================
            case 'users':
                require_once __DIR__ . '/controllers/UserController.php';
                $userCtrl = new UserController();
                
                if ($requestMethod === 'GET' && $subEndpoint === 'profile') {
                    echo $userCtrl->getProfile();
                } elseif ($requestMethod === 'PUT' && $subEndpoint === 'profile') {
                    echo $userCtrl->updateProfile($inputData);
                } else {
                    sendJsonResponse(['error' => 'Not found'], 404);
                }
                break;
            
            // ==================== DEFAULT ====================
            default:
                sendJsonResponse(['error' => 'API endpoint not found'], 404);
                break;
        }
    } catch (Exception $e) {
        // Xử lý exception
        error_log("Error: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        sendJsonResponse([
            'error' => 'Internal server error',
            'message' => $e->getMessage()
        ], 500);
    }
} else {
    // Root path
    echo json_encode([
        'message' => 'LMS Backend API is running!',
        'version' => '1.0.0',
        'endpoints' => [
            'auth' => '/api/auth/{register|login|logout}',
            'courses' => '/api/courses',
            'lessons' => '/api/lessons?course_id={id}',
            'enrollments' => '/api/enrollments',
            'quizzes' => '/api/quizzes?lesson_id={id}'
        ]
    ]);
}
?>