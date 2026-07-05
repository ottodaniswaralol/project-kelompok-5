# UBakrie Space

[![License](https://img.shields.io/badge/license-MIT-orange.svg?style=flat-square)](LICENSE)
[![Frontend](https://img.shields.io/badge/frontend-React%20%2B%20Vite-blue.svg?style=flat-square)](https://vitejs.dev/)
[![Backend](https://img.shields.io/badge/backend-PHP%208.x-purple.svg?style=flat-square)](https://www.php.net/)
[![Database](https://img.shields.io/badge/database-MySQL-orange.svg?style=flat-square)](https://www.mysql.com/)
[![Deployed on](https://img.shields.io/badge/frontend-Netlify-00C7B7.svg?style=flat-square)](https://netlify.com/)
[![Deployed on](https://img.shields.io/badge/backend-Railway-0B0D0E.svg?style=flat-square)](https://railway.app/)

> Sistem Informasi Peminjaman Ruangan Digital — Universitas Bakrie

**UBakrie Space** adalah aplikasi web berbasis arsitektur *decoupled* yang mendigitalisasi proses peminjaman ruang kelas dan inventaris di Universitas Bakrie. Sistem ini menggantikan proses manual berbasis formulir kertas dengan platform digital terpadu yang dapat diakses dari browser manapun.

🔗 **Live Demo:** [https://room-booking-group-5.netlify.app](https://room-booking-group-5.netlify.app)

🔗 **Backend API:** [https://project-kelompok-5-production.up.railway.app](https://project-kelompok-5-production.up.railway.app)

---

## 📋 Overview

UBakrie Space mendukung **5 role pengguna** dengan alur persetujuan multi-level:

| Role | Deskripsi | Akses Utama |
|---|---|---|
| **Mahasiswa / Dosen** | Pengguna peminjam ruang | Cek ketersediaan, buat booking, lihat status, feedback |
| **Marketing** | Validator ruang prioritas R1/R2 | Review & approve/reject booking ruang prioritas |
| **BIMA** | Approval level 1 | Review, approve/reject, tanda tangan digital |
| **GA** | Approval final semua permohonan | Approve/reject final, akses analytics |
| **BAA** | Administrator & monitor operasional | Dashboard analitik, ekspor laporan |

### Alur Persetujuan

**Ruang biasa (non R1/R2):**
```
Mahasiswa/Dosen → BIMA → GA → Selesai
```

**Ruang prioritas (R1/R2):**
```
Mahasiswa/Dosen → Marketing → BIMA → GA → Selesai
```

### Fitur Utama

- 🔐 **Autentikasi & RBAC** — Login berbasis role, redirect otomatis ke dashboard sesuai role
- 🏫 **Cek Ketersediaan Ruang** — Real-time availability check sebelum booking
- 📝 **Form Booking** — Pengajuan peminjaman lengkap dengan upload memo
- 🔄 **Recurring Booking** — Peminjaman berulang mingguan/bulanan dalam satu pengajuan (atomic transaction)
- ✅ **Multi-Level Approval Workflow** — Alur persetujuan bertingkat dengan notifikasi
- 📊 **Dashboard Analitik** — Bar chart booking per ruangan, filter periode, export CSV/PDF
- 📈 **Pola Penggunaan Ruangan** — Analisis hari & jam tersibuk per ruangan

---

## 🏗️ System Architecture

```
┌─────────────────────────────────────────────────┐
│              PRESENTATION LAYER                 │
│         React + Vite + Tailwind CSS             │
│              (Netlify CDN)                      │
└──────────────────────┬──────────────────────────┘
                       │ HTTP JSON over HTTPS
                       │ + CORS Header
┌──────────────────────▼──────────────────────────┐
│            BUSINESS LOGIC LAYER                 │
│         PHP 8.x Native (tanpa framework)        │
│               (Railway Cloud)                   │
└──────────────────────┬──────────────────────────┘
                       │ mysqli + Prepared Statement
┌──────────────────────▼──────────────────────────┐
│                 DATA LAYER                      │
│             MySQL Relational DB                 │
│               (Railway Cloud)                   │
└─────────────────────────────────────────────────┘
```

> Backend dibangun menggunakan **PHP Native 8.x tanpa framework**. Setiap endpoint adalah file `.php` tersendiri yang menerima request HTTP (GET/POST), memproses data, dan mengembalikan response dalam format JSON. Pola ini mengikuti prinsip REST API secara manual — tanpa library tambahan seperti Laravel, Slim, atau Lumen.

### Tech Stack

| Layer | Teknologi | Hosting |
|---|---|---|
| Frontend | React 18 + Vite + Tailwind CSS | Netlify |
| Backend | PHP 8.x Native (tanpa framework) | Railway |
| Database | MySQL 8.x | Railway |
| PDF Export | jsPDF + jsPDF-AutoTable | (client-side) |

---

## 🗄️ Database Design

### Entitas Utama (8 Tabel)

```
users ──────┬──── booking ────┬──── booking_rooms ──── rooms
            │                 ├──── booking_approval
            │                 ├──── booking_feedback
            │                 ├──── booking_inventory ── inventory
            │                 └──── booking_recurrence_rule
            └──── notifications
```

### Views Analytics

| View | Deskripsi |
|---|---|
| `vw_booking_report` | Laporan lengkap booking dengan join semua tabel |
| `vw_room_utilization_monthly` | Agregat pemakaian ruang per bulan (approved only) |
| `vw_my_booking_history` | Riwayat booking per user dengan status approval terakhir |
| `vw_room_popularity` | Pola penggunaan ruang per hari dan jam |

---

## 🚀 Getting Started

### Prerequisites

Pastikan sudah terinstall:

- [Node.js](https://nodejs.org/) v18+
- [npm](https://www.npmjs.com/) v9+
- [PHP](https://www.php.net/) 8.x
- [MySQL](https://www.mysql.com/) 8.x atau [XAMPP](https://www.apachefriends.org/)
- [Git](https://git-scm.com/)

### Installing

**1. Clone repository**

```bash
git clone https://github.com/ottodaniswaralol/project-kelompok-5.git
cd project-kelompok-5
```

**2. Setup Frontend**

```bash
cd client
npm install
```

Jalankan development server:

```bash
npm run dev
```

Frontend akan berjalan di `http://localhost:5173`

**3. Setup Backend**

Letakkan folder `server/` di dalam `htdocs/` (XAMPP) atau web server PHP kamu.

Buat file koneksi database di `server/config/database.php`:

```php
<?php
$host = getenv('MYSQLHOST') ?: "localhost";
$user = getenv('MYSQLUSER') ?: "root";
$pass = getenv('MYSQLPASSWORD') ?: "";
$db   = getenv('MYSQLDATABASE') ?: "room_booking";
$port = getenv('MYSQLPORT') ?: "3306";

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    http_response_code(500);
    echo json_encode(["status" => false, "message" => "Database Connection Error"]);
    exit;
}

mysqli_set_charset($conn, "utf8mb4");
?>
```

**4. Setup Database**

Import file SQL ke MySQL:

```bash
mysql -u root -p room_booking < database/Room_Booking_Update.sql
```

Atau buka file `database/Room_Booking_Update.sql` di DBeaver/phpMyAdmin dan execute.

---

## 📁 Project Structure

```
project-kelompok-5/
├── client/                          # Frontend React
│   ├── src/
│   │   ├── App.jsx                  # Main component + routing
│   │   ├── services/
│   │   │   └── api.js               # API service layer
│   │   └── main.jsx
│   ├── public/
│   │   └── favicon.svg
│   ├── package.json
│   └── vite.config.js
│
├── server/                          # Backend PHP
│   ├── api/
│   │   ├── auth/
│   │   │   └── login.php            # POST - Login user
│   │   ├── booking/
│   │   │   ├── create.php           # POST - Buat booking biasa
│   │   │   ├── create_recurring.php # POST - Buat recurring booking
│   │   │   ├── check_availability.php # GET - Cek ketersediaan ruang
│   │   │   ├── list.php             # GET - List booking per user
│   │   │   ├── all.php              # GET - Semua booking (admin)
│   │   │   └── delete.php           # POST - Hapus/batalkan booking
│   │   ├── approval/
│   │   │   ├── list.php             # GET - List approval per role
│   │   │   ├── approve.php          # POST - Setujui booking
│   │   │   └── reject.php           # POST - Tolak booking
│   │   ├── rooms/
│   │   │   └── list.php             # GET - List semua ruangan
│   │   ├── feedback/
│   │   │   └── submit.php           # POST - Submit feedback
│   │   └── reports/
│   │       ├── analytics.php        # GET - Data analytics dashboard
│   │       └── export.php           # GET - Export CSV/PDF
│   └── config/
│       └── database.php             # Konfigurasi koneksi database
│
└── database/
    └── Room_Booking_Update.sql      # Full database schema + seed data
```

---

## 🔌 API Endpoints

| Method | Endpoint | Deskripsi | Role |
|---|---|---|---|
| POST | `/api/auth/login.php` | Login user | Public |
| GET | `/api/rooms/list.php` | List semua ruangan | Semua |
| GET | `/api/booking/check_availability.php` | Cek ketersediaan ruang | Mahasiswa, Dosen |
| POST | `/api/booking/create.php` | Buat booking biasa | Mahasiswa, Dosen |
| POST | `/api/booking/create_recurring.php` | Buat recurring booking | Mahasiswa, Dosen |
| GET | `/api/booking/list.php` | List booking per user | Mahasiswa, Dosen |
| GET | `/api/booking/all.php` | Semua booking | BAA, GA |
| POST | `/api/booking/delete.php` | Batalkan booking | Mahasiswa, Dosen |
| GET | `/api/approval/list.php` | List antrean approval | Marketing, BIMA, GA |
| POST | `/api/approval/approve.php` | Setujui booking | Marketing, BIMA, GA |
| POST | `/api/approval/reject.php` | Tolak booking | Marketing, BIMA, GA |
| POST | `/api/feedback/submit.php` | Submit feedback | Mahasiswa, Dosen |
| GET | `/api/reports/analytics.php` | Data analytics | BAA, GA |
| GET | `/api/reports/export.php` | Export CSV/PDF | BAA, GA |

---

## 🧪 Tests

### Testing Manual via Postman

**Login:**
```json
POST /api/auth/login.php
{
  "username": "Amanda@student.bakrie.ac.id",
  "password": "123456789"
}
```

**Recurring Booking:**
```json
POST /api/booking/create_recurring.php
{
  "user_id": 5,
  "room_id": 7,
  "event_name": "Rapat Rutin",
  "organization": "Kelompok 5",
  "day_of_week": 2,
  "start_date": "2026-07-01",
  "end_date": "2026-07-31",
  "start_time": "09:00",
  "end_time": "11:00",
  "frequency": "weekly",
  "interval_count": 1
}
```

**Analytics:**
```
GET /api/reports/analytics.php?month=7&year=2026
```

### Test Accounts

| Email | Password | Role |
|---|---|---|
| `Amanda@student.bakrie.ac.id` | `123456789` | Mahasiswa |
| `budi@bakrie.ac.id` | `123456789` | Dosen |
| `bima@bakrie.ac.id` | `123456789` | BIMA |
| `marketing@bakrie.ac.id` | `123456789` | Marketing |
| `ga@bakrie.ac.id` | `123456789` | GA |
| `baa@bakrie.ac.id` | `123456789` | BAA |

---

## ☁️ Deployment

### Frontend — Netlify

1. Push kode ke GitHub
2. Connect repo ke Netlify
3. Set build settings:
   - **Base directory:** `client`
   - **Build command:** `npm run build`
   - **Publish directory:** `client/dist`
4. Deploy otomatis setiap push ke branch `main`

### Backend — Railway

1. Buat project baru di Railway
2. Connect ke GitHub repo
3. Set root directory ke `server/`
4. Tambahkan MySQL service
5. Railway otomatis inject environment variables:
   - `MYSQLHOST`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLDATABASE`, `MYSQLPORT`

---

## 👥 Contributing

1. Fork repository ini
2. Buat branch baru: `git checkout -b feature/nama-fitur`
3. Commit perubahan: `git commit -m 'feat: tambah fitur X'`
4. Push ke branch: `git push origin feature/nama-fitur`
5. Buat Pull Request

### Commit Convention

```
feat: tambah fitur baru
fix: perbaiki bug
docs: update dokumentasi
style: perubahan styling
refactor: refactor kode
```

---

## 📝 Release History

- **v2.0** *(Juli 2026)*
  - Tambah fitur Recurring Booking (atomic transaction)
  - Tambah Dashboard Analitik BAA/GA (bar chart, export CSV/PDF)
  - Tambah pola penggunaan ruangan (Top 10)
  - Role-based routing otomatis setelah login
  - Halaman pilihan dashboard untuk role GA

- **v1.0** *(Januari 2026)*
  - Autentikasi & RBAC (5 role)
  - Form booking dengan cek ketersediaan ruang
  - Multi-level approval workflow (Marketing → BIMA → GA)
  - Status pengajuan real-time
  - Sistem feedback (rating 1-5)

---

## 👨‍💻 Authors

**Kelompok 5 — Universitas Bakrie**  
Program Studi Teknik Informatika — Rekayasa Perangkat Lunak — 2025/2026

| Nama | NIM |
|---|---|
| Amanda Junita Maha Dewi | 1232001047 |
| Najwa Naela Fawwaz | 1232001010 |
| Yohanes Stevandrew | 1232001036 |
| Fadillah Putra Gunawan | 1232001049 |
| Otto Daniswara | 1232001040 |
| Nofita Munir | 1222001019 |

---

## 📄 License

Distributed under the MIT License. See `LICENSE` for more information.

---

<p align="center">
  Made with ❤️ by Kelompok 5 — Universitas Bakrie 2026
</p>
