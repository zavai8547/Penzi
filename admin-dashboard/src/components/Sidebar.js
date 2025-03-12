import React from "react";
import { Link } from "react-router-dom";
import "./Sidebar.css"; 

const Sidebar = () => {
  const handleLogout = () => {
   
    alert("Logging out...");
    
    window.location.href = "/login"; // Change this if using a login route
  };

  return (
    <div className="sidebar">
      <h1 className="sidebar-logo">Admin Dashboard</h1>
      <ul className="sidebar-links">
        
       <h1><li><Link to="/">🏠 Dashboard </Link></li> </h1>
        <h1><li><Link to="/users">👥 User Management</Link></li></h1>
        <h1><li><Link to="/match-requests">💌 Match Requests</Link></li></h1>
        <h1><li><Link to="/reports">📊 Reports & Analytics</Link></li></h1>
      </ul>
    
      <button className="logout-btn" onClick={handleLogout}>🚪 Logout</button>
    </div>
  );
};

export default Sidebar;
