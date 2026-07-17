import React from "react";
import { Link, useNavigate } from "react-router-dom";
import { useAuth } from "../../contexts/AuthContext";

const Header = () => {
  const { user, logout, isAuthenticated, isTeacher } = useAuth();
  const navigate = useNavigate();

  const handleLogout = async () => {
    await logout();
    navigate("/login");
  };

  return (
    <nav style={styles.nav}>
      <div style={styles.container}>
        <Link to="/" style={styles.logo}>
          📚 LMS Phenikaa
        </Link>

        <div style={styles.links}>
          <Link to="/courses" style={styles.link}>
            Khóa học
          </Link>

          {isAuthenticated && (
            <>
              {isTeacher && (
                <Link to="/courses/create" style={styles.link}>
                  Tạo khóa học
                </Link>
              )}
              <Link to="/my-courses" style={styles.link}>
                Khóa học của tôi
              </Link>
              <span style={styles.userInfo}>{user?.fullname}</span>
              <button onClick={handleLogout} style={styles.logoutBtn}>
                Đăng xuất
              </button>
            </>
          )}

          {!isAuthenticated && (
            <>
              <Link to="/login" style={styles.link}>
                Đăng nhập
              </Link>
              <Link to="/register" style={styles.link}>
                Đăng ký
              </Link>
            </>
          )}
        </div>
      </div>
    </nav>
  );
};

const styles = {
  nav: {
    backgroundColor: "#2c3e50",
    padding: "1rem 0",
    color: "white",
    boxShadow: "0 2px 4px rgba(0,0,0,0.1)",
  },
  container: {
    maxWidth: "1200px",
    margin: "0 auto",
    padding: "0 20px",
    display: "flex",
    justifyContent: "space-between",
    alignItems: "center",
  },
  logo: {
    color: "white",
    fontSize: "1.5rem",
    fontWeight: "bold",
    textDecoration: "none",
  },
  links: {
    display: "flex",
    gap: "20px",
    alignItems: "center",
  },
  link: {
    color: "white",
    textDecoration: "none",
    padding: "5px 10px",
    borderRadius: "4px",
    transition: "background-color 0.3s",
  },
  userInfo: {
    color: "#ecf0f1",
    marginRight: "10px",
  },
  logoutBtn: {
    backgroundColor: "#e74c3c",
    color: "white",
    border: "none",
    padding: "5px 15px",
    borderRadius: "4px",
    cursor: "pointer",
  },
};

export default Header;
