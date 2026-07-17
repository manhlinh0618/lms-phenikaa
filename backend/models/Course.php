<?php
class Course {
    private $conn;
    private $table_name = "courses";

    public $id;
    public $title;
    public $description;
    public $instructor_id;
    public $price;
    public $thumbnail;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy tất cả khóa học
    public function read() {
        $query = "SELECT c.id, c.title, c.description, c.price, c.thumbnail, u.fullname as instructor_name 
                  FROM " . $this->table_name . " c
                  LEFT JOIN users u ON c.instructor_id = u.id
                  ORDER BY c.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lấy chi tiết 1 khóa học
    public function readOne() {
        $query = "SELECT c.id, c.title, c.description, c.price, c.thumbnail, c.instructor_id, u.fullname as instructor_name 
                  FROM " . $this->table_name . " c
                  LEFT JOIN users u ON c.instructor_id = u.id
                  WHERE c.id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $this->title = $row['title'];
            $this->description = $row['description'];
            $this->price = $row['price'];
            $this->thumbnail = $row['thumbnail'];
            $this->instructor_id = $row['instructor_id'];
            return $row;
        }
        return null;
    }

    // Tạo khóa học mới (Dành cho Giảng viên/Admin)
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (title, description, instructor_id, price, thumbnail) 
                  VALUES (:title, :description, :instructor_id, :price, :thumbnail)";
        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->thumbnail = htmlspecialchars(strip_tags($this->thumbnail));

        // Bind
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":instructor_id", $this->instructor_id);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":thumbnail", $this->thumbnail);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
