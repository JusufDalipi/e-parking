# 🅿️ E-Parking — Parking Management System

A web-based parking management system built with **PHP**, **MySQL**, **JavaScript**, and **CSS**. Supports multiple user roles, slot reservations, vehicle entry/exit tracking, payments, and XML reporting.

---

## 🚀 Tech Stack

| Technology | Usage |
|---|---|
| PHP 8+ | Backend & server-side logic |
| MySQL | Relational database |
| JavaScript | Interactivity & animations |
| CSS | Styling & responsive design |
| XAMPP | Local development server |
| XML | Data export |
| JWT | Secure authentication |

---

## 👥 User Roles

| Role | Description |
|---|---|
| 🔑 **Admin** | Full control — users, slots, payments, reports |
| 🛡️ **Guard** | Monitors vehicle entries/exits and slot status in real time |
| 🚗 **Driver** | Makes reservations and views personal payments |

---

## ✨ Features

- 📊 **Interactive Dashboard** with live statistics and animated counters
- 🅿️ **Slot Management** — add, edit, and delete parking slots
- 📋 **Reservations** — drivers reserve specific parking slots
- 🚗 **Entry / Exit Tracking** — guards register vehicle arrivals and departures
- 💳 **Payments** — full payment tracking with history
- 📦 **Subscriptions** — subscription plans for drivers
- 👤 **User Management** — full CRUD with roles and status control
- 🔐 **Register & Login** — session-based authentication with JWT support
- 📋 **Login Logs** — complete login history for all users
- 📤 **XML Export** — export payment data in XML format
- ⚠️ **Notifications** — admin is alerted for pending password reset requests

---

## 📂 Project Structure

```
e-parking/
├── php/
│   ├── config.php          # Database configuration
│   ├── auth.php            # Authentication & authorization
│   ├── header.php          # Main navigation
│   ├── jwt.php             # JWT helper
│   └── ...
├── css/                    # CSS stylesheets
├── js/                     # JavaScript files
├── assets/                 # Icons and images
├── sql/                    # Database schema
├── xml/                    # XML export
├── index.php               # Main dashboard
├── login.php               # Login page
├── users.php               # User management
├── slots.php               # Slot management
├── reservations.php        # Reservations
├── entries.php             # Vehicle entry/exit
├── payments.php            # Payments
├── subscriptions.php       # Subscriptions
├── roles.php               # System roles
└── login_logs.php          # Login history
```

---

## ⚙️ Local Installation

### Requirements
- [XAMPP](https://www.apachefriends.org/) (PHP 8+ & MySQL)
- A modern browser (Chrome, Firefox, Edge)

### Steps

**1. Clone the repository:**
```bash
git clone https://github.com/JusufDalipi/e-parking.git
cd e-parking
```

**2. Place the project:**
Copy the folder to `c:\xampp\htdocs\`

**3. Import the database:**
- Open [phpMyAdmin](http://localhost/phpmyadmin)
- Create a new database: `parking_management`
- Import the file: `sql/parking_management.sql`

**4. Configure the connection:**
Edit `php/config.php`:
```php
$host   = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'parking_management';
```

**5. Open the application:**
```
http://localhost/e-parking/
```

---

## 🗄️ Database Tables

| Table | Description |
|---|---|
| `users` | All system users |
| `roles` / `user_roles` | Role definitions and assignments |
| `parking_slots` | Parking slot records |
| `reservations` | Driver reservations |
| `vehicle_entries` | Vehicle entry/exit logs |
| `payments` | Payment records |
| `subscriptions` | Driver subscription plans |
| `login_logs` | Login history |
| `password_resets` | Password reset requests |

---

## 📤 XML Export

Payment data can be exported in XML format at:
```
/xml/export_payments.php
```

---

## 👨‍💻 Author

**Jusuf Dalipi**  
🔗 [github.com/JusufDalipi](https://github.com/JusufDalipi)

---

## 📄 License

This project was built for academic purposes.
