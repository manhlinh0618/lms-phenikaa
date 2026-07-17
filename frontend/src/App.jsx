import React from "react";
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import { AuthProvider } from "./contexts/AuthContext";
import ProtectedRoute from "./components/common/ProtectedRoute";
import Header from "./components/common/Header";
import Login from "./components/auth/Login";
import Register from "./components/auth/Register";
import Home from "./pages/Home";
import Courses from "./pages/Courses";
import CourseDetailPage from "./pages/CourseDetailPage";
import CreateCourse from "./pages/CreateCourse";
import MyCourses from "./pages/MyCourses";
import Profile from "./pages/Profile";
import "./styles/index.css"; // ← ĐÃ SỬA ĐÚNG ĐƯỜNG DẪN

function App() {
  return (
    <Router>
      <AuthProvider>
        <div className="App">
          <Header />
          <main style={{ minHeight: "calc(100vh - 160px)", padding: "20px" }}>
            <Routes>
              <Route path="/" element={<Home />} />
              <Route path="/login" element={<Login />} />
              <Route path="/register" element={<Register />} />
              <Route path="/courses" element={<Courses />} />
              <Route path="/courses/:id" element={<CourseDetailPage />} />
              <Route
                path="/courses/create"
                element={
                  <ProtectedRoute roles={["teacher", "admin"]}>
                    <CreateCourse />
                  </ProtectedRoute>
                }
              />
              <Route
                path="/my-courses"
                element={
                  <ProtectedRoute>
                    <MyCourses />
                  </ProtectedRoute>
                }
              />
              <Route
                path="/profile"
                element={
                  <ProtectedRoute>
                    <Profile />
                  </ProtectedRoute>
                }
              />
            </Routes>
          </main>
        </div>
      </AuthProvider>
    </Router>
  );
}

export default App;
