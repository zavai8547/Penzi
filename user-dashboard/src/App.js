import React, { useState } from "react";
import Message from "./components/Message";
import "./App.css";

function App() {
    const [messages, setMessages] = useState([]);
    const [input, setInput] = useState("");
    const [apiStep, setApiStep] = useState(1);
    const [userPhone, setUserPhone] = useState(null);
    const [authToken, setAuthToken] = useState("YOUR_AUTH_TOKEN_HERE");

    const sendMessage = async () => {
        if (!input.trim()) return;

        setMessages((prev) => [...prev, { text: input, isUser: true }]);
        setInput("");

        try {
            const headers = { "Content-Type": "application/json" };
            let apiUrl = "";
            let bodyContent = {};

            if (apiStep > 1) {
                if (!authToken) {
                    setMessages((prev) => [...prev, { text: "⚠️ Authentication token missing.", isUser: false }]);
                    return;
                }
                headers["Authorization"] = `Bearer ${authToken}`;
            }

            switch (apiStep) {
                case 1:
                    apiUrl = "http://localhost/penzi/Endpoints/first.php";
                    bodyContent = { message: input };
                    break;

                case 2:
                    apiUrl = "http://localhost/penzi/Endpoints/userregistration.php";
                    bodyContent = { message: input }; 
                    break;

                case 3:
                case 4:
                    if (!userPhone) {
                        setMessages((prev) => [...prev, { text: "⚠️ Missing phone number. Try registering first.", isUser: false }]);
                        return;
                    }
                    apiUrl = `http://localhost/penzi/Endpoints/${apiStep === 3 ? "useradddetails.php" : "selfdescription.php"}`;
                    bodyContent = { phone: userPhone, message: input };
                    break;
                    case 5:
                    apiUrl = "http://localhost/penzi/Endpoints/matchrequest.php";
                    break;

                case 6:
                    apiUrl = "http://localhost/penzi/Endpoints/getmorematches.php";
                    break;

                case 7:
                    const targetPhoneNumber = input.includes("#") ? input.split("#")[1] : "";
                    if (!targetPhoneNumber.trim()) {
                        setMessages((prev) => [...prev, { text: "⚠️ Please provide a valid phone number.", isUser: false }]);
                        return;
                    }
                    apiUrl = `http://localhost/penzi/Endpoints/getSelfDescription.php?phoneNumber=${targetPhoneNumber}`;
                    break;

                case 8:
                    apiUrl = "http://localhost/penzi/Endpoints/notify-interest";
                    bodyContent = undefined;
                    break;
                    
                case 9:
                    apiUrl = "http://localhost/penzi/Endpoints/confirmationAPI.php";
                    bodyContent = undefined;
                    break;

                case 10:
                    apiUrl = "http://localhost/penzi/Endpoints/confirmationAPI.php";
                    break;

                default:
                    console.error("⚠️ Invalid API step");
                    return;
            }

            console.log(`📤 Sending API Step ${apiStep}:`, { apiUrl, bodyContent });

            const response = await fetch(apiUrl, {
                method: bodyContent ? "POST" : "GET",
                headers,
                ...(bodyContent ? { body: JSON.stringify(bodyContent) } : {}),
            });

            if (!response.ok) {
                if (response.status === 401) {
                    throw new Error("⚠️ Unauthorized! Check token or user authentication.");
                }
                throw new Error(`API ${apiStep} failed with status ${response.status}`);
            }

            const data = await response.json();
            console.log(`✅ API ${apiStep} Response:`, data);

            if (apiStep === 2 && data.phone) {
                setUserPhone(data.phone);
                console.log("✅ Stored user phone:", data.phone);
            }

            const responseMessage = data.reply || data.message || "⚠️ No response received.";
            setMessages((prev) => [...prev, { text: responseMessage, isUser: false }]);

            if (apiStep !== 2 || data.phone) {
                setApiStep((prevStep) => prevStep + 1);
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