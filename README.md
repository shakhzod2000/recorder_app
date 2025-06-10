# 🎤 Voice Recorder Web App

**A full-stack web application for recording voice memos and sending them via email, with secure authentication and cross-device compatibility.**

## 📸 Screenshots
<p align="left">
  <img src="https://github.com/user-attachments/assets/f6f7b33c-4e55-4990-be65-81e045e8fd36" width="300" height="350" alt="Login" />
  <img src="https://github.com/user-attachments/assets/2a49cd75-aac1-447f-8c12-c2c7ec536a24" width="300" height="350" alt="register" />
  <img src="https://github.com/user-attachments/assets/91dc08fb-5617-48e6-907a-e1970246729f" width="400" height="350" alt="reset" />
  <img src="https://github.com/user-attachments/assets/87e5d1ec-b4fe-48b4-8aa5-77f0d38cc458" width="470" height="350" alt="main" />
  <img src="https://github.com/user-attachments/assets/425f1a74-6441-4a67-a084-eb7c5edc4afb" width="470" height="350" alt="options" />
</p>


## ✨ Features

### 🎙️ Audio Processing
- **Browser-based recording** using MediaRecorder API
- **WebM to MP3 conversion** via FFmpeg backend (60% size reduction)
- **Auto-cleanup** of temporary files post-processing

### ✉️ Email Delivery
- **MIME-compliant attachments** for audio/text (99% deliverability)
- **SMTP authentication** with PHPMailer (Gmail/Outlook/iCloud tested)
- **Dynamic recipient selection** with user-configured email targets

### 🔒 Authentication
- **SHA-256 token-based auth** with device-aware sessions:
  - 90-day expiry for mobile (iOS/Android)
  - 30-day expiry for desktop
- **SameSite cookie policies** for CSRF protection
- **Secure token rotation** on each login

## 🛠️ Tech Stack

### Frontend
- JavaScript (MediaRecorder API, jQuery)
- Bootstrap 5 (responsive UI)
- Dynamic textarea with auto-resizing

### Backend
- **PHP** (custom authentication flow)
- **FFmpeg** (audio conversion: `-vn -ar 44100 -ac 2 -b:a 192k`)
- **MySQL** (user tokens + email configurations)
- **PHPMailer** (SMTP with TLS/SSL)

## 🚀 Installation

1. **Prerequisites**:
   ```bash
   # FFmpeg (Debian/Ubuntu)
   sudo apt install ffmpeg php-ffmpeg

2. **Configure**:
   ```bash
   Set your SMTP credentials and Database credentials

3. **Run**:
   ```bash
   php -S localhost:8000

## 🌟 Highlights

  - ✅ **Cross-device support** (iOS Safari workarounds implemented)
  - ✅ **Zero client-side dependencies** (vanilla JS + Bootstrap CDN)
  - ✅ **Memory-efficient** audio chunking (no browser crashes)

_Developed by **Shakhzod Shermatov**_
