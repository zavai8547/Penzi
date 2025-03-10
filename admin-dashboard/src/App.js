import React from "react";
import { BrowserRouter as Router, Route, Routes } from "react-router-dom";
import Sidebar from "./components/Sidebar";

const Dashboard = () => <h1>Dashboard Overview</h1>;
const UserManagement = () => <h1>User Management</h1>;
const MatchRequests = () => <h1>Match Requests</h1>;
const Reports = () => <h1>Reports & Analytics</h1>;

function App() {
  return (
    <Router>
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
    </Router>
  );
}

export default App;
