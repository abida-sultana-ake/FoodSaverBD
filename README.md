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