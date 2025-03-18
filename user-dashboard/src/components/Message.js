import React from "react";
import "./Message.css";

const Message = ({ text, isUser }) => {
  return (
    <div className={`message ${isUser ? "user-message" : "system-message"}`}>
      {text}
    </div>
  );
};

export default Message;
