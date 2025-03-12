import React, { useState, useEffect } from "react";

const UserManagement = () => {
    const [users, setUsers] = useState([]);
    const [editingUser, setEditingUser] = useState(null);
    const [formData, setFormData] = useState({
        UserID: "",
        name: "",
        Age: "",
        Gender: "",
        County: "",
        Town: "",
        PhoneNumber: ""
    });

    useEffect(() => {
        fetch("http://localhost/PENZI/Endpoints/Admin API'S/get_users.php")
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
        fetch("http://localhost/PENZI/Endpoints/Admin API'S/edit_user.php", {
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
            fetch(`http://localhost/PENZI/Endpoints/Admin API'S/delete_user.php?id=${userID}`, { method: "DELETE" })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    setUsers(users.filter(user => user.UserID !== userID)); // Remove from state
                })
                .catch(error => console.error("Error deleting user:", error));
        }
    };

    return (
        <div>
            <h1>User Management</h1>
            <table border="1" cellPadding="10" cellSpacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>County</th>
                        <th>Town</th>
                        <th>Phone Number</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {users.map(user => (
                        <tr key={user.UserID}>
                            <td>{user.UserID}</td>
                            <td>{user.name}</td>
                            <td>{user.Age}</td>
                            <td>{user.Gender}</td>
                            <td>{user.County}</td>
                            <td>{user.Town}</td>
                            <td>{user.PhoneNumber}</td>
                            <td>
                                <button onClick={() => handleEdit(user)}>Edit</button>
                                <button onClick={() => handleDelete(user.UserID)}>Delete</button>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>

            {/* Edit Form */}
            {editingUser && (
                <div>
                    <h2>Edit User</h2>
                    <form onSubmit={(e) => { e.preventDefault(); handleUpdate(); }}>
                        <input type="text" name="name" value={formData.name} onChange={handleInputChange} required />
                        <input type="number" name="Age" value={formData.Age} onChange={handleInputChange} required />
                        <input type="text" name="Gender" value={formData.Gender} onChange={handleInputChange} required />
                        <input type="text" name="County" value={formData.County} onChange={handleInputChange} required />
                        <input type="text" name="Town" value={formData.Town} onChange={handleInputChange} required />
                        <input type="text" name="PhoneNumber" value={formData.PhoneNumber} onChange={handleInputChange} required />
                        <button type="submit">Update</button>
                        <button type="button" onClick={() => setEditingUser(null)}>Cancel</button>
                    </form>
                </div>
            )}
        </div>
    );
};

export default UserManagement;
