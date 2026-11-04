# IFS24034 - PABWE 2026 Praktikum 7 (Studi Kasus: Stok Barang - Laravel 13 & Livewire)

Aplikasi Manajemen Stok Barang modern dengan arsitektur **Clean Architecture & Feature-First Approach** sesuai standar industri, dibangun menggunakan **Laravel 13**, **Livewire 3**, basis data **MySQL**, upload/export spreadsheet via **PhpOffice/PhpSpreadsheet**, integrasi kecerdasan buatan (**OpenAI AI Content Generation**), penyesuaian kuantitas realtime (+/-) dengan sinkronisasi status otomatis, rate limiting (1000 per detik), proteksi kepemilikan middleware, force HTTPS, dan **100% Test Coverage**.

---

## 🚀 Fitur Utama

1. **Clean Architecture & Feature-First Approach**:
   - Modul diorganisir per feature/domain (`App\Features\Auth`, `App\Features\Stock`, `App\Features\Admin`, `App\Features\Ai`, `App\Features\Profile`).
   - Menerapkan Dependency Inversion Principle (DIP) dengan repository interface dan client contracts.
   - Pemisahan lapisan yang jelas antara Actions, Repositories, Services, DTOs, Policies, dan UI (Livewire).

2. **Domain Model Stok Barang**:
   - Atribut lengkap: `name`, `sku`, `description`, `quantity`, `unit`, `price`, `min_stock`, `status`, `cover_image_path`.
   - **Sinkronisasi Status Otomatis**:
     - `available` (Tersedia) jika `quantity > min_stock`
     - `low_stock` (Stok Menipis) jika `1 <= quantity <= min_stock`
     - `out_of_stock` (Habis) jika `quantity <= 0`
   - Kontrol penyesuaian kuantitas cepat (+ / -) pada daftar stok dan halaman detail stok.
   - Pengunggahan foto cover produk dengan preview visual.

3. **Autentikasi & Autorisasi Role-Based**:
   - **Login Fleksibel**: Pengguna dapat masuk menggunakan **Username** atau **Email** (`id="login-username"`, `id="login-password"`, `id="login-submit"`).
   - **Role `admin`**:
     - Mengakses halaman Manajemen Pengguna (`/admin/users`).
     - Melihat daftar seluruh pengguna beserta username, email, dan jumlah stok barang mereka.
     - Melihat daftar stok pengguna lain dalam mode **Read-Only** (`/admin/users/{user}/stocks`) serta dapat mengekspornya.
     - Admin **tidak dapat** memodifikasi/menghapus stok barang pengguna lain.
   - **Role `member`**:
     - User yang mendaftar melalui form registrasi otomatis memiliki role `member`.
     - Hanya dapat mengelola (CRUD), menambah/mengurangi jumlah, mengunggah cover, mengekspor/mengimpor, serta generate AI untuk inventaris miliknya sendiri (`/stocks`, `/stocks/{stock}`).
   - **Middleware & Policy**:
     - `EnsureRole`: Membatasi rute khusus admin.
     - `EnsureStockOwnership`: Memastikan member tidak dapat mengakses atau memodifikasi stok milik orang lain.
     - `StockPolicy`: Memvalidasi otorisasi di tingkat model.

4. **Spreadsheet Excel Import & Export**:
   - Menggunakan library: **`phpoffice/phpspreadsheet`** (bebas dari celah keamanan).
   - **Export**: Menghasilkan file `.xlsx` dengan styling profesional (header berwarna, border, text wrap, auto size).
   - **Import**: Mengunggah file `.xlsx`, `.xls`, `.csv` untuk bulk insert stok barang secara aman dengan validasi format data, nama, satuan, dan penentuan status otomatis.

5. **AI Stock Content Generator (OpenAI)**:
   - Dikonfigurasi secara dinamis melalui `.env`:
     - `OPENAI_ENDPOINT` (mendukung custom proxy atau model OpenAI kompatibel)
     - `OPENAI_KEY`
     - `OPENAI_MODEL`
   - Kemampuan generate multiple item stok barang secara otomatis berdasarkan prompt pengguna (misal: "Buatkan daftar inventaris kafe dan kedai kopi").
   - Output diformat dan divalidasi ke dalam DTO `GeneratedStockItem`.
   - Pengguna dapat melihat preview hasil generate AI, memilih item yang diinginkan, dan menyimpannya langsung ke basis data.

6. **Rate Limiting & Keamanan**:
   - Skrip pembatas akses: **1000 request per detik** via `RateLimiter::for('1000-per-second', ...)`.
   - **Force HTTPS**: Dikonfigurasi melalui `FORCE_HTTPS=true` di `.env` atau otomatis aktif pada environment `production`.
   - Bebas dari kerentanan keamanan (`composer audit` clean 0 advisories).

7. **100% Test Coverage**:
   - Seluruh classes, methods, dan baris kode pada direktori `app/` memiliki **100.00% coverage** (23/23 classes, 118/118 methods, 735/735 lines).
   - Menggunakan mock/stub aman (`createStub()`) untuk menghindari PHPUnit deprecation notice.

---

## 📁 Struktur Direktori Feature-First

```
app/
├── Features/
│   ├── Admin/
│   │   ├── Livewire/
│   │   │   ├── AdminUserStockList.php
│   │   │   └── UserList.php
│   │   └── Services/
│   │       └── AdminUserService.php
│   ├── Ai/
│   │   ├── Contracts/
│   │   │   └── AiClientInterface.php
│   │   ├── DTO/
│   │   │   └── GeneratedStockItem.php
│   │   └── Services/
│   │       └── OpenAiStockGeneratorService.php
│   ├── Auth/
│   │   ├── Actions/
│   │   │   ├── LoginUserAction.php
│   │   │   └── RegisterUserAction.php
│   │   └── Livewire/
│   │       ├── Login.php
│   │       └── Register.php
│   ├── Profile/
│   │   ├── Livewire/
│   │   │   └── Profile.php
│   │   └── Services/
│   │       └── ProfileService.php
│   └── Stock/
│       ├── Contracts/
│       │   └── StockRepositoryInterface.php
│       ├── Enums/
│       │   └── StockStatus.php
│       ├── Livewire/
│       │   └── StockDetail.php
│       │   └── StockList.php
│       ├── Models/
│       │   └── Stock.php
│       ├── Policies/
│       │   └── StockPolicy.php
│       ├── Repositories/
│       │   └── StockRepository.php
│       └── Services/
│           ├── StockExcelService.php
│           └── StockService.php
├── Http/
│   └── Middleware/
│       ├── EnsureRole.php
│       └── EnsureStockOwnership.php
├── Models/
│   └── User.php
└── Providers/
    └── AppServiceProvider.php
```

---

## ⚙️ Persyaratan Sistem & Instalasi

- PHP >= 8.2 (Disarankan PHP 8.4 atau 8.5)
- Composer 2.x
- Node.js & NPM
- MySQL 8.x atau SQLite

### Langkah Menjalankan Aplikasi:

1. **Clone dan Pasang Dependensi:**
   ```bash
   composer install
   npm install
   npm run build
   ```

2. **Konfigurasi Environment:**
   Salin `.env.example` menjadi `.env` dan atur kredensial database serta OpenAI key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   php artisan storage:link
   ```

3. **Migrasi dan Seeding Basis Data:**
   ```bash
   php artisan migrate:fresh --seed
   ```

   Akun bawaan seeder:
   - **Admin**: `admin` / `password` (Email: `admin@stockapp.local`)
   - **Member**: `member` / `password` (Email: `member@stockapp.local`)

4. **Jalankan Aplikasi:**
   ```bash
   php artisan serve
   ```

---

## 🧪 Menjalankan Automated Test (PHPUnit)

Jalankan seluruh rangkaian pengujian dengan perintah:
```bash
php vendor/phpunit/phpunit/phpunit
```

Untuk melihat laporan coverage:
```bash
php vendor/phpunit/phpunit/phpunit --coverage-text
```
Semua 108 test akan lulus dengan 100% coverage pada seluruh lapisan arsitektur.
