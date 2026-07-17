import React, { useState, useEffect } from "react";
import api from "../api/axios";
import { Link } from "react-router-dom";

const Courses = () => {
  const [courses, setCourses] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    fetchCourses();
  }, []);

  const fetchCourses = async () => {
    try {
      const response = await api.get("/courses");
      setCourses(response.data.data || []);
      setLoading(false);
    } catch (err) {
      setError("Không thể tải danh sách khóa học");
      setLoading(false);
    }
  };

  if (loading) return <div className="container">Đang tải...</div>;
  if (error)
    return (
      <div className="container" style={{ color: "red" }}>
        {error}
      </div>
    );

  return (
    <div className="container">
      <h1 style={{ marginBottom: "30px" }}>📚 Danh sách khóa học</h1>

      {courses.length === 0 ? (
        <p>Chưa có khóa học nào.</p>
      ) : (
        <div className="grid">
          {courses.map((course) => (
            <div key={course.id} className="card">
              {course.thumbnail && (
                <img
                  src={course.thumbnail}
                  alt={course.title}
                  style={{
                    width: "100%",
                    height: "200px",
                    objectFit: "cover",
                    borderRadius: "4px",
                  }}
                />
              )}
              <h3 style={{ margin: "15px 0 10px" }}>{course.title}</h3>
              <p style={{ color: "#666", marginBottom: "10px" }}>
                {course.description?.substring(0, 100)}...
              </p>
              <p>
                <strong>Giảng viên:</strong>{" "}
                {course.instructor_name || "Chưa có"}
              </p>
              <p>
                <strong>Giá:</strong>{" "}
                {course.price === 0 ? "Miễn phí" : `${course.price} VND`}
              </p>
              <Link
                to={`/courses/${course.id}`}
                className="btn btn-primary"
                style={{ marginTop: "10px" }}
              >
                Xem chi tiết
              </Link>
            </div>
          ))}
        </div>
      )}
    </div>
  );
};

export default Courses;
