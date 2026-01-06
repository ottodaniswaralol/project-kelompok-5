# 🏢 Room & Inventory Booking System (Fullstack Project)

Sistem manajemen peminjaman ruangan dan inventaris kampus/kantor berbasis web. Project ini memisahkan antara Frontend (React) dan Backend (PHP API) untuk performa dan skalabilitas yang lebih baik.

## 📂 Struktur Folder Proyek (Full Structure)

Project-Kelompok-5/
├── client/                     # FRONTEND (React.js + Vite)
│   ├── public/                 # Static Assets
│   ├── src/
│   │   ├── assets/             # Images & Icons
│   │   ├── Pages/              # Page Components (Login, Dashboard, Peminjaman)
│   │   ├── services/           # API Fetch Logic
│   │   ├── App.jsx             # Main Logic & Routing
│   │   ├── main.jsx            # Entry Point
│   │   └── index.css           # Global Styling (Tailwind)
│   ├── package.json            # Dependencies & Scripts
│   └── vite.config.js          # Vite Configuration
│
├── server/                     # BACKEND (PHP Native API)
│   ├── api/
│   │   ├── auth/
│   │   │   ├── cors.php        # Centralized CORS Security Policy
│   │   │   └── login.php       # Authentication Logic
│   │   ├── booking/
│   │   │   ├── create.php      # Submit Peminjaman
│   │   │   ├── list.php        # Get All Bookings
│   │   │   ├── detail.php      # Get Booking Detail
│   │   │   ├── delete.php      # Remove Booking
│   │   │   ├── cek_data.php    # Debugging Utility
│   │   │   └── check_availability.php # Real-time Slot Checker
│   │   ├── inventory/
│   │   │   └── list.php        # Inventory Management API
│   │   └── rooms/
│   │       └── list.php        # Room List API
│   ├── config/
│   │   └── database.php        # Database Connection (Railway Env Ready)
│   └── index.php               # Backend Health Check
│
└── README.md                   # Dokumentasi Proyek


Laporan Teknis & Penanganan Masalah (Troubleshooting)

1. Keamanan Lintas Domain (CORS Policy)
Frontend (Netlify) dan Backend (Railway) berada pada domain yang berbeda. Browser secara otomatis memblokir permintaan data ini. Solusi yang dilakukan adalah membuat file `cors.php` terpusat yang mengirimkan header `Access-Control-Allow-Origin` dan menangani metode `OPTIONS` (Preflight) sehingga data bisa mengalir dengan aman.
2. Masalah Server 502 Bad Gateway (FrankenPHP)
Saat dihosting di Railway yang menggunakan server **FrankenPHP**, file `.htaccess` standar Apache menyebabkan konflik dan membuat server crash (Stopping Container). Masalah diselesaikan dengan menghapus file `.htaccess` dan memindahkan semua konfigurasi keamanan langsung ke dalam kode PHP.
3. Sinkronisasi Data JSO
React mengirimkan payload dalam format JSON, bukan form-data biasa. Hal ini menyebabkan variabel `$_POST` di PHP kosong. Solusi yang diimplementasikan adalah menggunakan `json_decode(file_get_contents("php://input"))` di setiap endpoint API untuk menangkap data mentah dari frontend.
4. Koneksi Database Internal
Awalnya koneksi ke database menggunakan Public URL yang lambat dan berbayar. Kami mengoptimalkan `database.php` agar menggunakan internal networking Railway (`MYSQLHOST`, `MYSQLPORT`) yang jauh lebih cepat, stabil, dan aman karena berada dalam satu jaringan private.

⚙️ Cara Menjalankan Proyek

Backend (Railway Deployment)
1. Hubungkan repository GitHub ke Railway.
2. Tambahkan layanan MySQL.
3. Masukkan variabel environment: `MYSQLHOST`, `MYSQLPORT`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLDATABASE` di tab Variables.

Frontend (Netlify Deployment)
1. Hubungkan folder `client` ke Netlify.
2. Atur build command: `npm run build` dan publish directory: `dist`.
3. Pastikan URL endpoint di `App.jsx` sudah mengarah ke domain Railway yang aktif.