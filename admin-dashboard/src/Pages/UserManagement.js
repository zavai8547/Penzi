import React, { useState, useEffect } from "react";

const UserManagement = () => {
    const [users, setUsers] = useState([]);
    const [editingUser, setEditingUser] = useState(null);
    const [formData, setFormData] = useState({
        UserID: "",
        Name: "",
        Age: "",
        Gender: "",
        County: "",
        Town: "",
        PhoneNumber: ""
    });

    useEffect(() => {
        fetch("http://localhost:8000/Admin API'S/get_users.php")
            .then(response => response.json())
            .then(data => setUsers(data))
            .catch(error => console.error("Error fetching users:", error));
    }, []);

    const handleEdit = (user) => {
        setEditingUser(user.UserID);
        setFormData({ ...user });
    };

    const handleInputChange = (e) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    const handleUpdate = () => {
        fetch("http://localhost:8000/Admin API'S/edit_user.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            setEditingUser(null);
            window.location.reload();
        })
        .catch(error => console.error("Error updating user:", error));
    };

    const handleDelete = (userID) => {
        if (window.confirm("Are you sure you want to delete this user?")) {
            fetch(`http://localhost:8000/Admin API'S/delete_user.php?id=${userID}`, { method: "DELETE" })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    setUsers(users.filter(user => user.UserID !== userID)); 
                })
                .catch(error => console.error("Error deleting user:", error));
        }
    };

    return (
        <div style={{ padding: "20px" }}>
            <h1 style={{ textAlign: "center", marginBottom: "20px" }}>User Management</h1>
            <table style={{ width: "100%", borderCollapse: "collapse", fontSize: "18px" }}>
                <thead>
                    <tr style={{ backgroundColor: "#007BFF", color: "white" }}>
                        <th style={headerStyle}>ID</th>
                        <th style={headerStyle}>Name</th>
                        <th style={headerStyle}>Age</th>
                        <th style={headerStyle}>Gender</th>
                        <th style={headerStyle}>County</th>
                        <th style={headerStyle}>Town</th>
                        <th style={headerStyle}>Phone Number</th>
                        <th style={headerStyle}>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {users.map((user, index) => (
                        <tr key={user.UserID} style={index % 2 === 0 ? evenRowStyle : oddRowStyle}>
                            <td style={cellStyle}>{user.UserID}</td>
                            <td style={cellStyle}>{user.Name}</td>
                            <td style={cellStyle}>{user.Age}</td>
                            <td style={cellStyle}>{user.Gender}</td>
                            <td style={cellStyle}>{user.County}</td>
                            <td style={cellStyle}>{user.Town}</td>
                            <td style={cellStyle}>{user.PhoneNumber}</td>
                            <td style={cellStyle}>
                                <button style={editButton} onClick={() => handleEdit(user)}>Edit</button>
                                <button style={deleteButton} onClick={() => handleDelete(user.UserID)}>Delete</button>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>

            {/* Edit Form */}
            {editingUser && (
                <div style={formContainer}>
                    <h2>Edit User</h2>
                    <form onSubmit={(e) => { e.preventDefault(); handleUpdate(); }}>
                        <input style={inputStyle} type="text" Name="Name" value={formData.name} onChange={handleInputChange} required />
                        <input style={inputStyle} type="number" Name="Age" value={formData.Age} onChange={handleInputChange} required />
                        <input style={inputStyle} type="text" Name="Gender" value={formData.Gender} onChange={handleInputChange} required />
                        <input style={inputStyle} type="text" Name="County" value={formData.County} onChange={handleInputChange} required />
                        <input style={inputStyle} type="text" Name="Town" value={formData.Town} onChange={handleInputChange} required />
                        <input style={inputStyle} type="text" Name="PhoneNumber" value={formData.PhoneNumber} onChange={handleInputChange} required />
                        <button style={submitButton} type="submit">Update</button>
                        <button style={cancelButton} type="button" onClick={() => setEditingUser(null)}>Cancel</button>
                    </form>
                </div>
            )}
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

const editButton = {
    backgroundColor: "#28a745",
    color: "white",
    border: "none",
    padding: "8px 12px",
    marginRight: "5px",
    cursor: "pointer",
    borderRadius: "4px",
};

const deleteButton = {
    backgroundColor: "#dc3545",
    color: "white",
    border: "none",
    padding: "8px 12px",
    cursor: "pointer",
    borderRadius: "4px",
};

const formContainer = {
    marginTop: "20px",
    padding: "15px",
    backgroundColor: "#f8f9fa",
    borderRadius: "8px",
    boxShadow: "0px 0px 10px rgba(0, 0, 0, 0.1)",
    width: "50%",
    margin: "auto",
};

const inputStyle = {
    display: "block",
    width: "100%",
    padding: "8px",
    marginBottom: "10px",
    border: "1px solid #ccc",
    borderRadius: "4px",
};

const submitButton = {
    backgroundColor: "#007BFF",
    color: "white",
    border: "none",
    padding: "10px 15px",
    cursor: "pointer",
    borderRadius: "4px",
    marginRight: "5px",
};

const cancelButton = {
    backgroundColor: "#6c757d",
    color: "white",
    border: "none",
    padding: "10px 15px",
    cursor: "pointer",
    borderRadius: "4px",
};

export default UserManagement;
