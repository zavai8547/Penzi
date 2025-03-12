import React, { useEffect, useState } from "react";
import { FaUsers, FaHeart, FaEnvelope, FaUserCheck } from "react-icons/fa";

const Dashboard = () => {
  const [stats, setStats] = useState({
    totalUsers: 0,
    activeInterests: 0,
    totalMatchRequests: 0,
    totalMatches: 0,
  });

  useEffect(() => {
    fetch("http://localhost/PENZI/Endpoints/Admin API'S/getDashboardStats.php")
      .then((response) => response.json())
      .then((data) => setStats(data))
      .catch((error) => console.error("Error fetching dashboard stats:", error));
  }, []);

  // Styles
  const styles = {
    container: {
      minHeight: "100vh",
      backgroundColor: "#f8fafc",
      padding: "20px",
      textAlign: "center",
    },
    title: {
      fontSize: "24px",
      fontWeight: "bold",
      marginBottom: "20px",
    },
    grid: {
      display: "grid",
      gridTemplateColumns: "repeat(auto-fit, minmax(250px, 1fr))",
      gap: "20px",
      justifyContent: "center",
      padding: "20px",
    },
    card: (bgColor) => ({
      backgroundColor: bgColor,
      color: "white",
      padding: "20px",
      borderRadius: "12px",
      boxShadow: "0 4px 10px rgba(0, 0, 0, 0.1)",
      display: "flex",
      alignItems: "center",
      justifyContent: "center",
      transition: "transform 0.3s ease, box-shadow 0.3s ease",
      cursor: "pointer",
    }),
    cardHover: {
      transform: "scale(1.05)",
      boxShadow: "0 6px 15px rgba(0, 0, 0, 0.2)",
    },
    icon: {
      fontSize: "40px",
      marginRight: "15px",
    },
    text: {
      textAlign: "left",
    },
  };

  return (
    <div style={styles.container}>
      <h1 style={styles.title}>Dashboard Overview</h1>
      <div style={styles.grid}>
        <div
          style={styles.card("#3B82F6")}
          onMouseOver={(e) => (e.currentTarget.style.transform = "scale(1.05)")}
          onMouseOut={(e) => (e.currentTarget.style.transform = "scale(1)")}
        >
          <FaUsers style={styles.icon} />
          <div style={styles.text}>
            <h3 style={{ fontSize: "18px", fontWeight: "bold" }}>Total Users</h3>
            <p style={{ fontSize: "24px" }}>{stats.totalUsers}</p>
          </div>
        </div>

        <div
          style={styles.card("#EC4899")}
          onMouseOver={(e) => (e.currentTarget.style.transform = "scale(1.05)")}
          onMouseOut={(e) => (e.currentTarget.style.transform = "scale(1)")}
        >
          <FaHeart style={styles.icon} />
          <div style={styles.text}>
            <h3 style={{ fontSize: "18px", fontWeight: "bold" }}>Active Interests</h3>
            <p style={{ fontSize: "24px" }}>{stats.activeInterests}</p>
          </div>
        </div>

        <div
          style={styles.card("#EF4444")}
          onMouseOver={(e) => (e.currentTarget.style.transform = "scale(1.05)")}
          onMouseOut={(e) => (e.currentTarget.style.transform = "scale(1)")}
        >
          <FaEnvelope style={styles.icon} />
          <div style={styles.text}>
            <h3 style={{ fontSize: "18px", fontWeight: "bold" }}>Match Requests</h3>
            <p style={{ fontSize: "24px" }}>{stats.totalMatchRequests}</p>
          </div>
        </div>

        <div
          style={styles.card("#10B981")}
          onMouseOver={(e) => (e.currentTarget.style.transform = "scale(1.05)")}
          onMouseOut={(e) => (e.currentTarget.style.transform = "scale(1)")}
        >
          <FaUserCheck style={styles.icon} />
          <div style={styles.text}>
            <h3 style={{ fontSize: "18px", fontWeight: "bold" }}>Total Matches</h3>
            <p style={{ fontSize: "24px" }}>{stats.totalMatches}</p>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Dashboard;
