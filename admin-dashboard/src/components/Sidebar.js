import React from "react";
import { Link } from "react-router-dom";
import "./Sidebar.css"; // Import CSS for styling

const Sidebar = () => {
  return (
    <div className="sidebar">
      <h2 className="sidebar-logo">Admin Dasboard</h2>
      <ul className="sidebar-links">
        <li><Link to="/">🏠 Dashboard</Link></li>
        <li><Link to="/users">👥 User Management</Link></li>
        <li><Link to="/match-requests">💌 Match Requests</Link></li>
        <li><Link to="/reports">📊 Reports & Analytics</Link></li>
        <li><Link to="/settings">⚙️ Settings</Link></li>
      </ul>
    </div>
  );
};

export default Sidebar;
