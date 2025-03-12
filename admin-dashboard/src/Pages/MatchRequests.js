import React, { useEffect, useState } from "react";

const MatchRequests = () => {
  const [matchRequests, setMatchRequests] = useState([]);

  useEffect(() => {
    fetch("http://localhost/PENZI/Endpoints/Admin API'S/getMatchRequests.php")
      .then((response) => response.json())
      .then((data) => setMatchRequests(data))
      .catch((error) => console.error("Error fetching data:", error));
  }, []);

  return (
    <div style={{ padding: "20px" }}>
      <h1 style={{ textAlign: "center", marginBottom: "20px" }}>Match Requests</h1>
      <table style={{ width: "100%", borderCollapse: "collapse", fontSize: "18px" }}>
        <thead>
          <tr style={{ backgroundColor: "#4CAF50", color: "white" }}>
            <th style={headerStyle}>MatchRequestID</th>
            <th style={headerStyle}>UserID</th>
            <th style={headerStyle}>AgeRange</th>
            <th style={headerStyle}>Town</th>
            <th style={headerStyle}>RequestDate</th>
          </tr>
        </thead>
        <tbody>
          {matchRequests.map((match, index) => (
            <tr key={match.MatchRequestID} style={index % 2 === 0 ? evenRowStyle : oddRowStyle}>
              <td style={cellStyle}>{match.MatchRequestID}</td>
              <td style={cellStyle}>{match.UserID}</td>
              <td style={cellStyle}>{match.AgeRange}</td>
              <td style={cellStyle}>{match.Town}</td>
              <td style={cellStyle}>{match.RequestDate}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
};


const headerStyle = {
  padding: "12px",
  textAlign: "left",
  borderBottom: "2px solid black",
};

const cellStyle = {
  padding: "12px",
  borderBottom: "1px solid #ddd",
};

const evenRowStyle = {
  backgroundColor: "#f2f2f2",
};

const oddRowStyle = {
  backgroundColor: "#ffffff",
};

export default MatchRequests;
