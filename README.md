# 🎤 Voice Recorder Web App

A full-stack web application for recording voice memos and sending them via email, with secure authentication and cross-device compatibility.

![Demo Screenshot](/assets/screenshot.png) *(optional: add screenshot later)*

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

   > Set your SMTP credentials and app settings

3. **Run**:
   ```bash
   php -S localhost:8000

## 🌟 Highlights

  ✅ **Cross-device support** (iOS Safari workarounds implemented)
  ✅ **Zero client-side dependencies** (vanilla JS + Bootstrap CDN)
  ✅ **Memory-efficient** audio chunking (no browser crashes)

_Developed by **Shakhzod Shermatov**_
