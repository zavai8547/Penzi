import React, { useEffect, useState } from "react";
import {
  LineChart,
  Line,
  BarChart,
  Bar,
  XAxis,
  YAxis,
  Tooltip,
  Legend,
  CartesianGrid,
} from "recharts";

const ReportsAnalytics = () => {
  const [userGrowth, setUserGrowth] = useState([]);
  const [matchPerformance, setMatchPerformance] = useState({
    requests: [],
    confirmations: [],
  });
  const [topLocations, setTopLocations] = useState([]);

  useEffect(() => {
    fetchData("user_growth", setUserGrowth, []);
    fetchData("match_performance", setMatchPerformance, {
      requests: [],
      confirmations: [],
    });
    fetchData("top_locations", setTopLocations, []);
  }, []);

  const fetchData = async (action, setter, defaultValue) => {
    try {
      const response = await fetch(
        `http://127.0.0.1/PENZI/Endpoints/Admin%20API'S/penzi_reports_api.php?action=${action}`
      );
      if (!response.ok) throw new Error(`API error: ${response.statusText}`);
      const data = await response.json();

      if (action === "match_performance") {
        setter({
          requests: Array.isArray(data.data) ? data.data : [],
          confirmations: Array.isArray(data.data)
            ? data.data.map((d) => ({
                date: d.date,
                confirmations: d.confirmations,
              }))
            : [],
        });
      } else if (action === "top_locations") {
        setter(Array.isArray(data.data) ? data.data : []);
      } else {
        setter(Array.isArray(data.data) ? data.data : []);
      }
    } catch (error) {
      console.error("Error fetching data:", error);
      setter(defaultValue);
    }
  };

  return (
    <div className="p-4">
      {/* Top Bar with Download Button */}
      <div className="flex justify-between items-center mb-4">
        <h1 className="text-2xl font-bold">Reports & Analytics</h1>
        <button
          className="bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-6 py-2 rounded-lg shadow-md hover:scale-105 transition"
          onClick={() =>
            window.open(
              "http://127.0.0.1/PENZI/Endpoints/Admin%20API'S/penzi_reports_api.php?action=export"
            )
          }
        >
          📥 Download CSV Report
        </button>
      </div>

      {/* Charts Section - FLEX for SIDE-BY-SIDE positioning */}
<div className="flex gap-6">
  {/* User Growth Trends */}
  <div className="border p-4 rounded-lg shadow-md bg-white flex-1 min-w-[500px]">
    <h2 className="text-xl font-bold mb-3">📈 User Growth Trends</h2>
    <LineChart width={500} height={300} data={userGrowth}>
      <CartesianGrid strokeDasharray="3 3" />
      <XAxis dataKey="date" />
      <YAxis />
      <Tooltip />
      <Legend />
      <Line type="monotone" dataKey="total" stroke="#4f46e5" strokeWidth={3} />
    </LineChart>
  </div>

  {/* Matchmaking Performance */}
  <div className="border p-4 rounded-lg shadow-md bg-white flex-1 min-w-[500px]">
    <h2 className="text-xl font-bold mb-3">💞 Matchmaking Performance</h2>
    <BarChart width={500} height={300} data={matchPerformance.requests}>
      <CartesianGrid strokeDasharray="3 3" />
      <XAxis dataKey="date" />
      <YAxis />
      <Tooltip />
      <Legend />
      <Bar dataKey="requests" fill="#22c55e" />
    </BarChart>
    <BarChart width={500} height={300} data={matchPerformance.confirmations}>
      <Bar dataKey="confirmations" fill="#d946ef" />
    </BarChart>
  </div>
</div>

      {/* Top Locations Section */}
      <div className="mt-6 border p-4 rounded-lg shadow-md bg-white">
        <h2 className="text-xl font-bold mb-3">🌍 Top Locations</h2>
        <table className="w-full border-collapse border border-gray-300">
          <thead>
            <tr className="bg-gray-200">
              <th className="border border-gray-300 px-4 py-2">#</th>
              <th className="border border-gray-300 px-4 py-2">Location</th>
              <th className="border border-gray-300 px-4 py-2">User Count</th>
            </tr>
          </thead>
          <tbody>
            {topLocations.length > 0 ? (
              topLocations.map((location, index) => (
                <tr key={index} className="text-center bg-gray-50 hover:bg-gray-100">
                  <td className="border border-gray-300 px-4 py-2">{index + 1}</td>
                  <td className="border border-gray-300 px-4 py-2">{location.town}</td>
                  <td className="border border-gray-300 px-4 py-2 font-bold">{location.user_count}</td>
                </tr>
              ))
            ) : (
              <tr>
                <td colSpan="3" className="text-center py-4 text-gray-500">
                  No location data available.
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
};

export default ReportsAnalytics;
