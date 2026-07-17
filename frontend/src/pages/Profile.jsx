import React, { useState, useEffect } from 'react';
import api from '../api/axios';
import { useAuth } from '../contexts/AuthContext';

const Profile = () => {
  const { user } = useAuth();
  const [fullname, setFullname] = useState(user?.fullname || '');
  const [loading, setLoading] = useState(false);
  const [message, setMessage] = useState('');

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setMessage('');

    try {
      await api.put('/users/profile', { fullname });
      setMessage('Cập nhật thành công!');
      // Update user in context
      user.fullname = fullname;
      localStorage.setItem('user', JSON.stringify(user));
    } catch (err) {
      setMessage('Cập nhật thất bại!');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="container">
      <div className="card" style={{ maxWidth: '500px', margin: '0 auto' }}>
        <h2>👤 Thông tin cá nhân</h2>
        
        <form onSubmit={handleSubmit}>
          <div style={{ marginBottom: '15px' }}>
            <label style={{ display: 'block', marginBottom: '5px' }}>Email</label>
            <input
              type="email"
              value={user?.email || ''}
              disabled
              style={{ width: '100%', padding: '10px', borderRadius: '4px', border: '1px solid #ddd', background: '#f5f5f5' }}
            />
          </div>

          <div style={{ marginBottom: '15px' }}>
            <label style={{ display: 'block', marginBottom: '5px' }}>Họ và tên</label>
            <input
              type="text"
              value={fullname}
              onChange={(e) => setFullname(e.target.value)}
              required
              style={{ width: '100%', padding: '10px', borderRadius: '4px', border: '1px solid #ddd' }}
            />
          </div>

          <div style={{ marginBottom: '15px' }}>
            <label style={{ display: 'block', marginBottom: '5px' }}>Vai trò</label>
            <input
              type="text"
              value={user?.role === 'student' ? 'Học viên' : 'Giảng viên'}
              disabled
              style={{ width: '100%', padding: '10px', borderRadius: '4px', border: '1px solid #ddd', background: '#f5f5f5' }}
            />
          </div>

          {message && (
            <div style={{ 
              color: message.includes('thành công') ? '#27ae60' : '#e74c3c',
              marginBottom: '15px'
            }}>
              {message}
            </div>
          )}

          <button type="submit" disabled={loading} className="btn btn-primary">
            {loading ? 'Đang cập nhật...' : 'Cập nhật'}
          </button>
        </form>
      </div>
    </div>
  );
};

export default Profile;