import React, { useState, useEffect } from "react";
import { useParams, useNavigate } from "react-router-dom";
import api from "../api/axios";
import { useAuth } from "../contexts/AuthContext";

const CourseDetailPage = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const { user } = useAuth();
  const [course, setCourse] = useState(null);
  const [lessons, setLessons] = useState([]);
  const [loading, setLoading] = useState(true);
  const [enrolled, setEnrolled] = useState(false);
  const [enrolling, setEnrolling] = useState(false);

  useEffect(() => {
    fetchCourseDetail();
    fetchLessons();
    checkEnrollment();
  }, [id]);

  const fetchCourseDetail = async () => {
    try {
      const response = await api.get(`/courses/${id}`);
      setCourse(response.data);
    } catch (err) {
      console.error("Error fetching course:", err);
    }
  };

  const fetchLessons = async () => {
    try {
      const response = await api.get(`/lessons?course_id=${id}`);
      setLessons(response.data.data || []);
    } catch (err) {
      console.error("Error fetching lessons:", err);
    } finally {
      setLoading(false);
    }
  };

  const checkEnrollment = async () => {
    try {
      const response = await api.get("/enrollments");
      const enrolledCourses = response.data.data || [];
      setEnrolled(enrolledCourses.some((e) => e.course_id === parseInt(id)));
    } catch (err) {
      console.error("Error checking enrollment:", err);
    }
  };

  const handleEnroll = async () => {
    setEnrolling(true);
    try {
      await api.post("/enrollments", { course_id: id });
      setEnrolled(true);
      alert("Đăng ký khóa học thành công!");
    } catch (err) {
      alert("Đăng ký thất bại!");
    } finally {
      setEnrolling(false);
    }
  };

  if (loading) return <div className="container">Đang tải...</div>;
  if (!course) return <div className="container">Không tìm thấy khóa học</div>;

  return (
    <div className="container">
      <div className="card">
        <h1>{course.title}</h1>
        <p style={{ color: "#666" }}>{course.description}</p>
        <p>
          <strong>Giảng viên:</strong> {course.instructor_name}
        </p>
        <p>
          <strong>Giá:</strong>{" "}
          {course.price === 0 ? "Miễn phí" : `${course.price} VND`}
        </p>

        {user && !enrolled && (
          <button
            onClick={handleEnroll}
            disabled={enrolling}
            className="btn btn-success"
            style={{ marginTop: "15px" }}
          >
            {enrolling ? "Đang đăng ký..." : "Đăng ký khóa học"}
          </button>
        )}

        {enrolled && (
          <p style={{ color: "#27ae60", marginTop: "15px" }}>
            ✅ Đã đăng ký khóa học này
          </p>
        )}
      </div>

      {enrolled && lessons.length > 0 && (
        <div>
          <h2>📖 Danh sách bài học</h2>
          {lessons.map((lesson) => (
            <div key={lesson.id} className="card">
              <h3>
                {lesson.lesson_order}. {lesson.title}
              </h3>
              {lesson.video_url && (
                <video
                  controls
                  style={{
                    width: "100%",
                    maxWidth: "600px",
                    marginTop: "10px",
                  }}
                >
                  <source src={lesson.video_url} type="video/mp4" />
                </video>
              )}
              {lesson.content && (
                <p style={{ marginTop: "10px" }}>{lesson.content}</p>
              )}
            </div>
          ))}
        </div>
      )}
    </div>
  );
};

export default CourseDetailPage;
