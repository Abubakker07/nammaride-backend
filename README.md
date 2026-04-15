# 🛺 NammaRide: Full-Stack Ride Aggregator Platform

![Kotlin](https://img.shields.io/badge/Kotlin-B125EA?style=for-the-badge&logo=kotlin&logoColor=white)
![Jetpack Compose](https://img.shields.io/badge/Jetpack_Compose-4285F4?style=for-the-badge&logo=android&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
![Railway](https://img.shields.io/badge/Railway-0B0D0E?style=for-the-badge&logo=railway&logoColor=white)

---

**NammaRide** is a native Android ride-hailing simulator built to tackle real-world transit challenges in major metropolitan areas like Bangalore.

Developed as a comprehensive full-stack platform, it features a custom **PHP/MySQL REST API** deployed on Railway, an **O(n log n) QuickSort** dynamic pricing engine, and integrated **Google ML Kit Vision AI** for on-device fraud prevention.

---

## 🚀 Key Features

### 🔐 AI Smart QR Fare Guard
Uses Google ML Kit (Vision API) to scan a driver's UPI QR code directly on the device. It parses payment data and cross-verifies it with backend fare data to prevent overcharging.

### ⚡ Dynamic Surge Pricing & Sorting
Implements:
- Time-based surge pricing (e.g., **1.25x during peak hours**)  
- **5% aggregator GST**  
- Backend **O(n log n) QuickSort algorithm** for fast fare comparison  

### 📍 Intelligent Geofencing
Restricts vehicle types based on routes:
- No autos/bikes for airport routes  
- Smart backend validation  

### 🗺️ OSRM Map Integration
- Uses **OSRM (Open Source Routing Machine)**  
- Draws real-time routes  
- Calculates:
  - Distance (km)
  - ETA (minutes)

### 🚨 Hardware-Linked Emergency SOS
- Uses Android accelerometer  
- Detects abnormal shaking  
- Automatically:
  - Sends SMS to emergency contact  
  - Calls **112** in critical cases  

---

## 📸 App Gallery

> 📌 Replace the image links with actual screenshots from your repository

<div align="center">
  <img src="screenshots/screen1.jpg" width="18%" />
  <img src="screenshots/screen2.jpg" width="18%" />
  <img src="screenshots/screen3.jpg" width="18%" />
  <img src="screenshots/screen4.jpg" width="18%" />
  <img src="screenshots/admin_panel.jpg" width="18%" />
</div>

---

## 🛠️ System Architecture & Tech Stack

### 📱 Frontend (Android)
- **Language:** Kotlin  
- **UI Toolkit:** Jetpack Compose  
- **Architecture:** MVVM  
- **Networking:** Retrofit2 + Gson  
- **Maps:** Osmdroid + OSRM API  
- **ML:** Google ML Kit (Barcode Scanning)

### 🌐 Backend (REST API)
- **Language:** PHP 8.x  
- **Database:** MySQL  
- **Deployment:** Railway (TCP Proxy)  
- **DB Tool:** TablePlus  
- **Admin Panel:** HTML + JavaScript  

---

## ⚙️ How It Works (Data Flow)

1. **Request**  
   Android app sends location data via Retrofit API.

2. **Compute**  
   Backend:
   - Calculates distance  
   - Applies surge pricing  
   - Adds GST  
   - Validates route rules  

3. **Sort**  
   QuickSort algorithm ranks ride options by price.

4. **Response**  
   Returns structured JSON.

5. **Render**  
   Jetpack Compose updates UI with:
   - Route polyline  
   - Fare breakdown  

---

## 💻 Local Development Setup

### 1️⃣ Clone Repository
```bash
git clone https://github.com/YourUsername/NammaRide.git
cd NammaRide
2️⃣ Database Setup
Import nammaride_backup.sql into MySQL (XAMPP/MAMP)
Update credentials in:
backend/db.php
3️⃣ Android Setup
Open project in Android Studio
Navigate to:
com.example.nammaride.network.NammaApi.kt
Update:
BASE_URL = "http://192.168.1.X/"
4️⃣ Run App
Connect emulator or physical device
Build & Run 🚀
📌 Notes
Ensure backend server is running before launching app
Replace API URL with Railway deployment for production
Use real device for best GPS + ML performance
