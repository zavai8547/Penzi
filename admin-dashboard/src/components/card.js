import React from "react";

const Card = ({ children, className }) => {
  return (
    <div className={`p-4 rounded-lg shadow-md ${className}`}>
      {children}
    </div>
  );
};

const CardContent = ({ children }) => {
  return <div>{children}</div>;
};

export { Card, CardContent };
