# FoodSaver BD 🍃

FoodSaver BD is a web-based food management and community-sharing platform designed to help users reduce food waste, track food inventory, monitor expiry dates, and share surplus food with their community.

---

## 🌱 About the Project

Food waste is a major problem in Bangladesh and around the world. Many food items are thrown away simply because people forget their expiry dates or have more food than they can consume.

FoodSaver BD provides a simple solution by allowing users to:

* Manage their personal food inventory
* Track food expiry dates
* Receive expiry-related alerts
* Share surplus food with the community
* Reduce unnecessary food waste

---

## ✨ Features

### 📦 Smart Food Inventory

Users can add and manage food items with important information such as:

* Food name
* Quantity
* Unit
* Category
* Purchase date
* Expiry date
* Storage location
* Food image
* Additional notes

### ⏰ Expiry Tracking

Users can easily monitor the expiry status of their food items:

* Fresh
* Expiring Soon
* Expired

### 🤝 Community Sharing

Users can share surplus food with other people in their community.

Sharing information includes:

* Food name
* Shared quantity
* Pickup location
* Contact number
* Description
* Food image

### 👤 User Authentication

The system includes:

* User registration
* Secure login
* Password hashing
* Session-based authentication
* Profile management
* Password change functionality

### 📊 Dashboard

Users can view important information through a dashboard, including:

* Total food items
* Expiring food
* Expired food
* Shared food
* Recent inventory activities

### 🏠 Public Homepage

The homepage includes:

* Navigation bar
* Announcement banner
* Hero section
* Features section
* Impact statistics
* How It Works section
* Call-to-action section
* Footer

---

## 🛠️ Technologies Used

### Frontend

* HTML5
* CSS3
* JavaScript
* Poppins Font
* Font Awesome Icons

### Backend

* PHP

### Database

* MySQL

### Development Environment

* XAMPP
* Apache
* phpMyAdmin

---

## 📁 Project Structure

```text
FoodSaverBD/
│
├── assets/
│   ├── css/
│   │   ├── style.css
│   │   └── home.css
│   │
│   ├── images/
│   │
│   └── js/
│
├── auth/
│   ├── login.php
│   ├── register.php
│   └── logout.php
│
├── config/
│   └── db.php
│
├── dashboard/
│   └── index.php
│
├── food/
│   ├── add_food.php
│   ├── edit_food.php
│   ├── delete_food.php
│   ├── index.php
│   └── view_food.php
│
├── home/
│   ├── index.php
│   │
│   └── components/
│       ├── navbar.php
│       ├── banner.php
│       ├── hero.php
│       ├── features.php
│       ├── impact.php
│       ├── how-it-works.php
│       ├── cta.php
│       └── footer.php
│
├── profile/
│   ├── index.php
│   ├── edit_profile.php
│   └── change_password.php
│
├── sharing/
│   ├── index.php
│   ├── share_food.php
│   ├── edit_share.php
│   └── delete_share.php
│
├── uploads/
│
└── README.md
## ⚙️ Installation & Setup

### 1. Clone the Repository

```bash
git clone https://github.com/YOUR-USERNAME/FoodSaverBD.git
2. Move the Project

Move the project folder into:

C:\xampp\htdocs\

The final path should be:

C:\xampp\htdocs\FoodSaverBD
3. Start XAMPP

Open XAMPP and start:

Apache
MySQL
4. Create the Database

Open phpMyAdmin:

http://localhost/phpmyadmin

Create a database named:

foodsaver_bd

Then import the project database SQL file.

5. Configure Database Connection

Open:

config/db.php

Update your database credentials:

$host = "localhost";
$user = "root";
$password = "";
$database = "foodsaver_bd";
6. Run the Project

Open:

http://localhost/FoodSaverBD/home/index.php
🔑 Demo Login Credentials

Use the following credentials to test the application:

Email:

test@test.com

Password:

01724928494

These credentials are provided for demonstration and testing purposes only.

🔐 Security Features
Passwords are securely hashed using password_hash()
Password verification is handled using password_verify()
Prepared statements are used for database queries
Session-based authentication is implemented
User data is protected using authenticated sessions
🎯 Project Goals

The main goals of FoodSaver BD are to:

Reduce household food waste
Help users manage food efficiently
Prevent food from expiring unused
Encourage community food sharing
Promote sustainable consumption
Contribute to a more environmentally responsible society
🚀 Future Improvements

Future versions of FoodSaver BD may include:

Email expiry notifications
SMS notifications
Mobile application
AI-based food expiry prediction
Food donation organization integration
Location-based food sharing
Food image recognition
Smart recipe suggestions based on available food
Admin dashboard
Community rating and review system
👩‍💻 Author

Abida Sultana

Computer Science and Engineering Student

📄 License

This project was developed for educational and academic purposes.