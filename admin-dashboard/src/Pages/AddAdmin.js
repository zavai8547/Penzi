import React, { useState } from "react";

const AddAdmin = () => {
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");
  const [message, setMessage] = useState("");

  const handleCreateAdmin = async (e) => {
    e.preventDefault();
    const response = await fetch("http://localhost/PENZI/Endpoints/create_admin.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({ username, password }),
    });

    const data = await response.json();
    setMessage(data.message);
  };

  return (
    <div>
      <h2>Create New Admin</h2>
      {message && <p>{message}</p>}
      <form onSubmit={handleCreateAdmin}>
        <input type="text" placeholder="Username" onChange={(e) => setUsername(e.target.value)} required />
        <input type="password" placeholder="Password" onChange={(e) => setPassword(e.target.value)} required />
        <button type="submit">Create Admin</button>
      </form>
    </div>
  );
};

export default AddAdmin;
