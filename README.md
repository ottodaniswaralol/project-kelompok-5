🏢 Room & Inventory Booking System (Project Kelompok 5)
Sistem manajemen peminjaman ruangan dan inventaris kampus berbasis web. Proyek ini dirancang untuk memodernisasi proses peminjaman yang sebelumnya manual menjadi digital, meningkatkan efisiensi antara User (Peminjam) dan Admin (Pengelola), serta mendukung transparansi data ketersediaan ruangan.

📖 Latar Belakang & Fitur Utama
Masalah utama yang diselesaikan proyek ini adalah ketidakefisienan dalam pengecekan jadwal dan pengajuan ruangan kampus.

Fitur Unggulan:

- Digital Booking: Pengajuan peminjaman ruangan dan barang secara online.
- Real-time Availability: Pengecekan ketersediaan ruangan (mencegah bentrok jadwal).
- Role Management: Membedakan akses antara Mahasiswa/Staff dan Admin.
- Priority System (Backend Ready): Logika backend telah mendukung prioritas peminjaman (VIP) khusus untuk Rektor/Petinggi kampus agar permohonan mereka diutamakan dalam antrian sistem (UI Frontend untuk fitur ini sedang dalam tahap pengembangan).

🛠️ Arsitektur & Alur Teknologi
Proyek ini menggunakan arsitektur Terpisah (Decoupled) antara Frontend dan Backend untuk skalabilitas yang lebih baik. Berikut adalah teknologi yang digunakan dan fungsinya dalam ekosistem proyek ini:
====================================================================================================================================================================
| Teknologi 	| Kategori 	  | Fungsi & Alur Kerja 													   |
====================================================================================================================================================================
| React (Vite)	| Frontend 	  | Membangun antarmuka pengguna (UI) yang cepat dan responsif. Berada di folder `/client`. 					   |
| PHP (Native) 	| Backend 	  | Menangani logika bisnis, API Endpoints, dan keamanan data. Berada di folder `/server`. 					   |
| MySQL		| Database	  | Menyimpan data user, ruangan, dan transaksi peminjaman. 									   |
| Git Bash	| Terminal 	  | Digunakan untuk eksekusi perintah git dan manajemen versi di lokal komputer. 						   |
| GitHub	| Version Control | Tempat kolaborasi kode (repository) antara anggota kelompok. 								   |
| DBeaver	| DB Tool 	  | Aplikasi GUI untuk memvisualisasikan, mengedit, dan me-manage database MySQL secara lokal maupun remote. 			   |
| Railway	| Cloud Backend   | Layanan cloud untuk men-deploy **Backend (PHP)** dan **Database (MySQL)** agar bisa diakses internet. 			   |
| Netlify       | Cloud Frontend  | Layanan cloud utk deploy Frontend (React). Netlify mengambil build dari React dan menghubungkannya ke API yang ada di Railway. |
====================================================================================================================================================================
Alur Kerja Sistem:

1. User mengakses web via Netlify.
2. Frontend me-request data via API ke Railway (Server PHP).
3. Server PHP memproses request dan mengambil data dari Database (di Railway).
4. Data dikirim balik ke Frontend untuk ditampilkan ke User.

📂 Struktur Proyek
Berikut adalah struktur direktori dari proyek ini:

Project-Kelompok-5/
├── .git/
├── client/                     # Frontend Application (React + Vite)
│   ├── public/
│   │   └── vite.svg
│   ├── src/
│   │   ├── assets/
│   │   │   └── react.svg
│   │   ├── pages/
│   │   │   └── Login.jsx       # Halaman Login
│   │   ├── services/
│   │   │   └── api.js          # Konfigurasi Axios/Fetch ke Backend
│   │   ├── app.css
│   │   ├── app.jsx
│   │   ├── index.css
│   │   └── main.jsx
│   ├── .DS_Store
│   ├── .gitignore
│   ├── eslint.config.js
│   ├── index.html
│   ├── package-lock.json
│   ├── package.json
│   ├── postcss.config.js
│   ├── README.md
│   ├── tailwind.config.js      # Konfigurasi Styling
│   └── vite.config.js          # Konfigurasi Build Tool
├── database/
│   └── room_booking.sql        # File Import Database
├── server/                     # Backend Application (PHP API)
│   ├── api/                    # API Endpoints
│   │   ├── approval/
│   │   │   ├── list.php
│   │   │   └── reject.php
│   │   ├── auth/
│   │   │   ├── cors.php        # Handle Cross-Origin Resource Sharing
│   │   │   ├── login.php
│   │   │   └── register.php
│   │   ├── booking/
│   │   │   ├── cek_data.php
│   │   │   ├── check_availability.php
│   │   │   ├── create.php
│   │   │   ├── delete.php
│   │   │   ├── detail.php
│   │   │   └── list.php
│   │   ├── feedback/
│   │   │   └── submit.php
│   │   ├── inventory/
│   │   │   └── list.php
│   │   └── rooms/
│   │       └── list.php
│   ├── config/
│   │   └── database.php        # Koneksi Database (PDO/MySQLi)
│   ├── uploads/
│   │   └── memos/              # Folder penyimpanan bukti/memo (Empty initially)
│   ├── composer.json           # Dependencies Backend (jika pakai library tambahan)
│   ├── index.php               # Entry point (Opsional/Routing)
│   └── test.php
├── test.db.php                 # File testing koneksi database root
└── README.md                   # Dokumentasi Proyek

🚀 Instalasi & Cara Menjalankan (Local Development)
Ikuti langkah ini untuk menjalankan proyek di komputer lokal (Localhost).

1. Persiapan Database
  - Pastikan XAMPP/Laragon (MySQL) sudah berjalan.
  - Buka DBeaver atau phpMyAdmin.
  - Buat database baru dengan nama room_booking (atau sesuaikan dengan config).
  - Import file database/room_booking.sql ke dalam database yang baru dibuat.

2. Setup Backend (Server)
Karena ini memisahkan frontend dan backend, kita perlu menjalankan server PHP secara independen atau melalui XAMPP.

  - Opsi A (Menggunakan PHP Built-in Server - Recommended):
    1. Buka terminal (Git Bash/CMD), arahkan ke folder server.
    2. Jalankan perintah:
       cd server
       php -S localhost:8000
    3. Backend sekarang berjalan di http://localhost:8000.

  - Konfigurasi Koneksi:
    - Buka file server/config/database.php.
    - Pastikan kredensial DB sesuai (Host: localhost, User: root, Pass: [kosong], DB: room_booking).

3. Setup Frontend (Client)
Pastikan Node.js sudah terinstall di komputer.

   1. Buka terminal baru (jangan matikan terminal Backend).
   2. Masuk ke folder client:
      cd client
   3. Install dependencies (Wajib dilakukan pertama kali):
      npm install
   4. Jalankan mode development:
      npm run dev
   5. Aplikasi akan berjalan (biasanya di http://localhost:5173). Buka link tersebut di browser.

Catatan Penting: Pastikan konfigurasi URL API di client/src/services/api.js mengarah ke alamat backend lokal kamu (misal: http://localhost:8000/api/).

📡 Dokumentasi API (Endpoints)
Backend menyediakan endpoint berikut untuk dikonsumsi oleh Frontend:
Auth:
POST /api/auth/login.php - Autentikasi user
POST /api/auth/register.php - Pendaftaran user baru

Booking (Peminjaman):
GET /api/booking/list.php - Melihat daftar peminjaman
POST /api/booking/create.php - Membuat peminjaman baru
GET /api/booking/check_availability.php - Cek ketersediaan ruangan

Rooms & Inventory:

GET /api/rooms/list.php - List semua ruangan
GET /api/inventory/list.php - List inventaris

Approval (Admin):
PUT/POST /api/approval/reject.php - Menolak peminjaman


--------------------------------------Dibuat untuk memenuhi tugas akhir mata kuliah E-Bussiness and Web Based Programming----------------------------------------------