import React from "react";    
import { Route, Routes } from "react-router-dom";
import Sidebar from "./components/Sidebar";
import UserManagement from "./pages/UserManagement"; 
import MatchRequests from "./pages/MatchRequests";    
import Reports from "./pages/Reports";
import Dashboard from "./pages/Dashboard"; // Import the actual Dashboard component
import "./App.css";

function App() {
  return (
    <div className="app-container">
      <Sidebar />
      <div className="content">
        <Routes>
          <Route path="/" element={<Dashboard />} />
          <Route path="/users" element={<UserManagement />} />
          <Route path="/match-requests" element={<MatchRequests />} />
          <Route path="/reports" element={<Reports />} />
        </Routes>
      </div>
    </div>
  );
}

export default App;
