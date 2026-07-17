import React, { useState, useEffect } from "react";
import { Link } from "react-router-dom";
import api from "../api/axios";

const MyCourses = () => {
  const [courses, setCourses] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchMyCourses();
  }, []);

  const fetchMyCourses = async () => {
    try {
      const response = await api.get("/enrollments");
      setCourses(response.data.data || []);
      setLoading(false);
    } catch (err) {
      console.error("Error fetching courses:", err);
      setLoading(false);
    }
  };

  if (loading) return <div className="container">Đang tải...</div>;

  return (
    <div className="container">
      <h1 style={{ marginBottom: "30px" }}>📚 Khóa học của tôi</h1>

      {courses.length === 0 ? (
        <div className="card text-center">
          <p>Bạn chưa đăng ký khóa học nào.</p>
          <Link
            to="/courses"
            className="btn btn-primary"
            style={{ marginTop: "10px" }}
          >
            Xem khóa học
          </Link>
        </div>
      ) : (
        <div className="grid">
          {courses.map((course) => (
            <div key={course.id} className="card">
              <h3>{course.title}</h3>
              <p style={{ color: "#666" }}>
                Giảng viên: {course.instructor_name}
              </p>
              <p>
                <strong>Đăng ký:</strong>{" "}
                {new Date(course.enrolled_at).toLocaleDateString("vi-VN")}
              </p>
              <Link
                to={`/courses/${course.course_id}`}
                className="btn btn-primary"
                style={{ marginTop: "10px" }}
              >
                Học ngay
              </Link>
            </div>
          ))}
        </div>
      )}
    </div>
  );
};

export default MyCourses;
