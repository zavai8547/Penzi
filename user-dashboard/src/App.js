import React, { useState } from "react";
import Message from "./components/Message";
import "./App.css";

function App() {
    const [messages, setMessages] = useState([]);
    const [input, setInput] = useState("");
    const [apiStep, setApiStep] = useState(1);

    const authToken =
        "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlhdCI6MTc0MTMyNTA4NCwiZXhwIjoxNzQzOTE3MDg0fQ.Tk8CTCsADc7pqh4_sT3AiLXEw1UsZjUXUaBenBLPfNY";

    const sendMessage = async () => {
        if (input.trim() === "") return;

        // Add user message to UI
        setMessages((prev) => [...prev, { text: input, isUser: true }]);
        setInput("");

        try {
            let response, data;
            const headers = { "Content-Type": "application/json" };

            // Add Authorization header after step 1
            if (apiStep > 1) {
                headers["Authorization"] = `Bearer ${authToken}`;
            }

            let apiUrl = "";
            let bodyContent = JSON.stringify({ message: input });

            // Determine API endpoint
            switch (apiStep) {
                case 1:
                    apiUrl = "http://localhost/penzi/Endpoints/first.php";
                    break;
                case 2:
                    apiUrl = "http://localhost/penzi/Endpoints/userregistration.php";
                    break;
                case 3:
                    apiUrl = "http://localhost/penzi/Endpoints/useradddetails.php";
                    break;
                case 4:
                    apiUrl = "http://localhost/penzi/Endpoints/api4.php";
                    break;
                case 5:
                    apiUrl = "http://localhost/penzi/Endpoints/api5.php";
                    break;
                case 6:
                    apiUrl = "http://localhost/penzi/Endpoints/api6.php";
                    break;
                case 7:
                    const targetPhoneNumber = input.includes("#") ? input.split("#")[1] : "";
                    apiUrl = `http://localhost/penzi/Endpoints/getSelfDescription.php?phoneNumber=${targetPhoneNumber}`;
                    bodyContent = null; // No body for GET request
                    break;
                case 8:
                    apiUrl = "http://localhost/penzi/Endpoints/api8.php";
                    break;
                default:
                    console.error("⚠️ Invalid API step");
                    return;
            }

            console.log(`🔹 Sending API Step ${apiStep}:`, { apiUrl, bodyContent });

            // Make API request
            response = await fetch(apiUrl, {
                method: bodyContent ? "POST" : "GET",
                headers,
                ...(bodyContent ? { body: bodyContent } : {}), // Prevent body in GET requests
            });

            if (!response.ok) {
                throw new Error(`API ${apiStep} failed with status ${response.status}`);
            }

            data = await response.json();
            console.log(`✅ API ${apiStep} Response:`, data);

            // Handle API response
            if (data.reply) {
                setMessages((prev) => [...prev, { text: data.reply, isUser: false }]);
                setApiStep((prevStep) => prevStep + 1); // Increment API step
            } else {
                setMessages((prev) => [...prev, { text: "⚠️ No response received.", isUser: false }]);
            }
        } catch (error) {
            console.error("❌ API Error:", error);
            setMessages((prev) => [...prev, { text: `⚠️ ${error.message}`, isUser: false }]);
        }
    };

    return (
        <div className="app-container">
            <div className="chat-container">
                <div className="messages-container">
                    {messages.map((msg, index) => (
                        <Message key={index} text={msg.text} isUser={msg.isUser} />
                    ))}
                </div>
                <div className="input-container">
                    <input
                        type="text"
                        value={input}
                        onChange={(e) => setInput(e.target.value)}
                        placeholder="Type your messages..."
                    />
                    <button onClick={sendMessage}>Send</button>
                </div>
            </div>
        </div>
    );
}

export default App;
