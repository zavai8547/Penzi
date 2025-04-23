# PENZI

PENZI is a matchmaking and dating system designed to facilitate structured, SMS-based communication between users and the system in order to mathone user with a prospective partner. It features user and admin dashboards and a lightweight PHP backend integrated with a MySQL database.

## Features
- **Dashboard Interface**:
  ### 1️ Messaging UI  
Facilitates messages between the user and the system.

Here is how the UI looks like:  
![UI Preview](screenshots/messaging-ui.png)

 **How it works:**  
The user activates the bot by sending **"PENZI"**, then follows the instructions given by the system.
  2. Admin Dashboard for:
     - User management page: here is how it looks like ![UI Preview](screenshots/usermanagment.png)
     **How it works:**  
The admin can delete the users from the system completely or edit the users information using the buttons on the ui.
     - Match requests viewing. Allows the admin to view all match requests being made by the users.Here is how the ui looks like:![UI Preview](screenshots/requests.png)

     - Real-time reports and analytics: Shows user growth in graphs and also shows tops locations where matchs are made. It also allows admins to download an exell docuent of all users. Here is how the ui looks like:
      ![UI Preview](screenshots/reports.png)

## Technologies Used
- **Frontend**: React
- **Backend**: PHP (Vanilla)
- **Database**: MySQL
- **API**: PHP

## Setup Instructions
1. **Clone the Repository**:

   git clone https://github.com/zavai8547/Penzi

2. **Import the Database**:
Use a MySQL client like DBeaver to import the database:

3.  **Configure Database Credentials**:
In your db.php file, use the following credentials:
  MYSQL_HOST=database
MYSQL_USER=root
MYSQL_PASSWORD=rodney
MYSQL_DB=penzi
 
4. **Run the Application**
