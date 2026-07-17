import React from "react";
import { Link } from "react-router-dom";
import { useAuth } from "../contexts/AuthContext";

const Home = () => {
  const { user } = useAuth();

  return (
    <div className="container">
      <div className="text-center" style={{ padding: "60px 0" }}>
        <h1 style={{ fontSize: "48px", marginBottom: "20px" }}>
          🎓 Hệ thống Quản lý Học tập
        </h1>
        <p style={{ fontSize: "20px", color: "#666", marginBottom: "30px" }}>
          Nền tảng học trực tuyến dành cho sinh viên và giảng viên
        </p>

        {!user ? (
          <div>
            <Link
              to="/register"
              className="btn btn-success"
              style={{ marginRight: "10px" }}
            >
              Đăng ký ngay
            </Link>
            <Link to="/login" className="btn btn-primary">
              Đăng nhập
            </Link>
          </div>
        ) : (
          <div>
            <h2>Chào mừng, {user.fullname}!</h2>
            <p>
              Vai trò: {user.role === "student" ? "Học viên" : "Giảng viên"}
            </p>
            <Link
              to="/courses"
              className="btn btn-primary"
              style={{ marginTop: "20px" }}
            >
              Xem khóa học
            </Link>
          </div>
        )}
      </div>
    </div>
  );
};

export default Home;
