# Agent Changelog - Optimasi & Filter Laporan Absensi

File ini mencatat riwayat perubahan, logika kode baru, serta alur pengerjaan fitur filter laporan absensi di dalam proyek **Absensi QR berbasis CodeIgniter 4**.

---

## 1. Ringkasan Perubahan
* **Filter Tambahan:** Menambahkan pilihan filter **Mingguan (Date Range)**, **Bulanan (Dropdown Bulan & Tahun)**, dan **Semester (Semester Ganjil/Genap + Tahun Ajaran)**.
* **Perhitungan Persentase:** Menampilkan persentase absensi (Hadir, Sakit, Izin, Alpa) per individu (di tabel) dan rata-rata keseluruhan (di ringkasan bawah).
* **Optimasi Query Database:** Mengubah pengambilan data absensi dari looping query harian menjadi **single query per-range tanggal**, mengurangi beban query database dari ~130 query (untuk semester) menjadi **1 query saja**.
* **Perbaikan Tampilan (UI/UX):** Modernisasi form filter dengan tab Bootstrap 4 serta layout table laporan yang scannable, soft colored badges, zebra striping, dan otomatis Landscape saat dicetak ke PDF.

---

## 2. Struktur Database (SQL Query)
> [!NOTE]
> **TIDAK ADA PERUBAHAN STRUKTUR DATABASE (NO SCHEMA CHANGES REQUIRED)**
> Fitur filter mingguan, bulanan, dan semester dirancang secara dinamis menggunakan kalkulasi range tanggal di sisi server (PHP) berdasarkan input user.
> Oleh karena itu, Anda **tidak perlu menjalankan query SQL baru** di phpMyAdmin. Struktur database bawaan (`tb_presensi_siswa`, `tb_presensi_guru`, dll.) sudah sepenuhnya memadai.

---

## 3. Detail File yang Diubah

### A. Controllers

#### 1. GenerateLaporan.php (Admin)
* **Logika Baru:**
  * Membaca parameter `filter_type` (`bulanan`, `mingguan`, `semester`).
  * Untuk `mingguan`: mengambil parameter `start_date` dan `end_date`.
  * Untuk `semester`: memecah `tahun_ajaran` (misal `2024/2025`) dan mencocokkan Semester Ganjil (1 Juli - 31 Des) atau Semester Genap (1 Jan - 30 Juni).
  * Untuk `bulanan`: menyatukan input dropdown `bulanSiswa` / `bulanGuru` dan `tahunSiswa` / `tahunGuru`.
  * **Optimasi Query:** Melakukan query tunggal ke tabel absensi menggunakan clause `WHERE tanggal BETWEEN startDate AND endDate` (daripada looping database query per-hari). Hasil absensi di-index di PHP untuk dipetakan ke data siswa/guru.

#### 2. Reports.php (Guru/Wali Kelas)
* **Logika Baru:** Mengimplementasikan kalkulasi tanggal dinamis dan optimasi single-query yang identik dengan controller Admin agar Wali Kelas mendapat performa dan fitur filter yang setara.

---

### B. Views (UI & Laporan)

#### 3. generate-laporan.php (Admin Dashboard Laporan)
* **Perubahan UI:**
  * Mengganti form input sederhana menjadi tabbed layout Bootstrap 4 (Bulanan, Mingguan, Semester).
  * Menambahkan Datepicker untuk Mingguan, Dropdown Bulan/Tahun untuk Bulanan, dan Dropdown Semester/Tahun Ajaran untuk Semester.
  * Penambahan hidden input `filter_type` yang diupdate secara otomatis lewat event handler tab click.

#### 4. reports.php (Guru Dashboard Laporan)
* **Perubahan UI:** Menerapkan layout tabbed filter yang sama dengan Admin agar konsisten dan user-friendly.

#### 5. laporan.php (Master CSS Template Laporan)
* **Perubahan UI:**
  * Menggunakan font `Inter` modern dengan rendering tajam.
  * Membuat custom class `.report-table`, `.student-row`, dan `.student-name-td` untuk mengganti table HTML konvensional.
  * Menambahkan style badge status absensi warna pastel lembut: `.status-h` (Hijau), `.status-s` (Biru), `.status-i` (Oranye), `.status-a` (Merah).
  * Menambahkan CSS Media Print `@page { size: A4 landscape; }` agar print dialog browser otomatis tersetting Landscape.
  * Menambahkan `-webkit-print-color-adjust: exact` agar warna latar badge status tercetak di PDF.

#### 6. laporan-siswa.php (Template Laporan Siswa)
* **Logika Baru:**
  * Menggabungkan kolom header (Rowspan/Colspan) menjadi rapi dan sejajar.
  * Menghitung total hari aktif (`$activeDays`) untuk mengabaikan hari di masa depan.
  * Menghitung persentase kehadiran per siswa (Hadir, Sakit, Izin, Alpa) dan menampilkannya di kolom baru "Persentase".
  * Menghitung rata-rata kehadiran seluruh kelas dan menampilkannya di box ringkasan (Summary Card) di bagian bawah.

#### 7. laporan-guru.php (Template Laporan Guru)
* **Logika Baru:** Menerapkan desain, kolom persentase individu, ringkasan rata-rata kehadiran guru, dan formatting yang sama persis dengan laporan siswa.
