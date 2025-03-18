import React, { useState } from "react";
import Message from "./Message";
import "./Chat.css"; // Import chat styles

const Chat = () => {
  const [messages, setMessages] = useState([
    { text: "Welcome to the system!", type: "system" },
    { text: "Hello! How can I assist you?", type: "system" },
    { text: "Can you remind me of my appointments?", type: "user" },
  ]);

  return (
    <div className="chat-container">
      {messages.map((msg, index) => (
        <Message key={index} text={msg.text} type={msg.type} />
      ))}
    </div>
  );
};

export default Chat;
