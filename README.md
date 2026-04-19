<div align="center">

# ⚙️ NammaRide Backend API
### The Core Pricing & Routing Engine

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
![AlwaysData](https://img.shields.io/badge/AlwaysData-FF0055?style=for-the-badge&logo=linux&logoColor=white)
![Railway](https://img.shields.io/badge/Railway-0B0D0E?style=for-the-badge&logo=railway&logoColor=white)

This repository contains the custom **PHP/MySQL REST API** that powers the [NammaRide Android Application](https://github.com/Abubakker07/NammaRide-Android). It handles multi-environment database connections, dynamic Bangalore route generation, and an algorithm-driven dynamic pricing engine.

[Features](#-core-backend-features) • [API Endpoints](#-rest-api-endpoints) • [Architecture](#️-database--deployment-architecture) • [Local Setup](#-local-development)

</div>

---

## 🚀 Core Backend Features

### 🧠 O(n log n) QuickSort Pricing Algorithm
Fares aren't just pulled from a database; they are computed dynamically. The backend factors in base distance, 5% aggregator GST, and time-aware surge multipliers (e.g., peak traffic hours), and then utilizes a custom **QuickSort** implementation to instantly rank available vehicles by price before sending the JSON response.

### 🌍 Multi-Environment "God Mode" Configuration
The `db.php` file is engineered for high availability and seamless developer operations. It automatically detects the active host environment and dynamically applies the correct database credentials for:
1.  **Railway.app** (via internal environment variables)
2.  **AlwaysData** (via URL detection)
3.  **Localhost** (Fallback for XAMPP/MAMP testing)

### 🛡️ Secure Driver Fetching & Validation
Endpoints are designed to fetch driver and vehicle details while supporting the frontend's Google ML Kit Vision API, allowing the client to cross-verify live backend fare data against physical QR codes to prevent overcharging.

---

## 📡 REST API Endpoints

The API serves clean, structured JSON arrays to the Android client.

| Endpoint | Method | Description |
| :--- | :---: | :--- |
| `/get_routes.php` | `GET` | Returns available localized routes, geocoordinates, and destination names. |
| `/get_fares.php` | `GET` | Accepts a `route_id` parameter and returns computed, sorted vehicle options, dynamic surge metrics, and UI breakdown data. |
| `/auth.php` | `POST` | Handles user authentication data payloads from the mobile client. |

---

## 🏗️ Database & Deployment Architecture

* **Language:** PHP 8.x (Raw, dependency-free processing for maximum speed)
* **Database:** MySQL
* **Package Management:** Composer (`composer.json` configured explicitly for `ext-pdo_mysql` cloud installations)
* **Production Host:** AlwaysData / Railway.app

### Database Schema (Overview)
The relational database utilizes 4 primary tables:
1.  `routes`: Stores Bangalore destinations, geolocations, and base distances.
2.  `vehicles`: Defines vehicle types (Auto, Mini Cab, SUV, etc.) and base rate multipliers.
3.  `drivers`: Links simulated drivers to specific vehicle categories and ratings.
4.  `users`: Manages authenticated application users.

---

## 💻 Local Development

To run this backend locally on your machine:

### 1. Requirements
* A local server environment like **XAMPP**, **WAMP**, or **MAMP**.
* MySQL/MariaDB running on port `3306`.

### 2. Setup Instructions
```bash
# Clone this repository into your XAMPP htdocs folder
git clone [https://github.com/Abubakker07/nammaride-backend.git](https://github.com/Abubakker07/nammaride-backend.git)
cd nammaride-backend
