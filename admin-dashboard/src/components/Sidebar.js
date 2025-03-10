import React from "react";
import { Link } from "react-router-dom";
import "./Sidebar.css"; // Import CSS for styling

const Sidebar = () => {
  const handleLogout = () => {
    // Clear user session or token (implement this based on your authentication)
    alert("Logging out...");
    // Redirect to login page (if applicable)
    window.location.href = "/login"; // Change this if using a login route
  };

  return (
    <div className="sidebar">
      <h2 className="sidebar-logo">Admin Dashboard</h2>
      <ul className="sidebar-links">
        <li><Link to="/">🏠 Dashboard</Link></li>
        <li><Link to="/users">👥 User Management</Link></li>
        <li><Link to="/match-requests">💌 Match Requests</Link></li>
        <li><Link to="/reports">📊 Reports & Analytics</Link></li>
        <li><Link to="/settings">⚙️ Settings</Link></li>
      </ul>
      <button className="logout-btn" onClick={handleLogout}>🚪 Logout</button>
    </div>
  );
};

export default Sidebar;
