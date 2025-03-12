import React from "react";  
import { Route, Routes } from "react-router-dom";
import Sidebar from "./components/Sidebar";
import UserManagement from "./pages/UserManagement";  // Import correctly
import "./App.css";

const Dashboard = () => <h1>Dashboard Overview</h1>;
const MatchRequests = () => <h1>Match Requests</h1>;
const Reports = () => <h1>Reports & Analytics</h1>;

function App() {
  return (
    <div className="app-container">
      <Sidebar />
      <div className="content">
        <Routes>
          <Route path="/" element={<Dashboard />} />
          <Route path="/users" element={<UserManagement />} />  {/* Corrected */}
          <Route path="/match-requests" element={<MatchRequests />} />
          <Route path="/reports" element={<Reports />} />
        </Routes>
      </div>
    </div>
  );
}

export default App;
