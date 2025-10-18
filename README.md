# Laravel OCR Document Management System

Aplikasi web berbasis Laravel yang berfungsi sebagai antarmuka untuk mengunggah, mengelola, dan mencari dokumen (gambar dan PDF) yang teksnya telah diekstraksi menggunakan layanan OCR eksternal berbasis Python.

## Fitur Utama

* **Upload Dokumen**: Memungkinkan pengguna mengunggah file gambar (JPEG, PNG, JPG) atau PDF.
* **Integrasi Layanan OCR**: Berkomunikasi dengan layanan OCR eksternal (dibuat dengan Python/Flask/EasyOCR) untuk mengekstraksi teks dari dokumen yang diunggah.
* **Penyimpanan Hasil OCR**: Menyimpan nama file asli, path file tersimpan, teks yang diekstraksi, data kata (termasuk koordinat), dan path thumbnail (untuk PDF) ke dalam database.
* **Pencarian Teks**: Menyediakan fungsionalitas pencarian untuk menemukan dokumen berdasarkan konten teks yang diekstraksi.
* **Viewer Dokumen**: Menampilkan viewer terintegrasi untuk file PDF (menggunakan PDF.js) dan gambar, dengan fitur pencarian teks internal dan highlight.
* **Thumbnail PDF**: Menampilkan thumbnail yang dihasilkan oleh layanan OCR untuk file PDF di hasil pencarian.
* **Antarmuka Pengguna Modern**: Dibangun dengan Tailwind CSS dan Vite untuk tampilan yang bersih dan responsif.

## Teknologi yang Digunakan

* **Backend**:
    * PHP 8.2+
    * Laravel Framework 12.x
    * Intervention Image (potensi untuk manipulasi gambar di sisi Laravel)
    * Database (default SQLite, bisa dikonfigurasi)
* **Frontend**:
    * Vite
    * Tailwind CSS
    * PDF.js (untuk viewer PDF)
* **Layanan OCR Eksternal (contoh dari `ocr-system-only`)**:
    * Python 3
    * Flask
    * EasyOCR
    * pdf2image (+ Poppler)
    * OpenCV (cv2)
    * Pillow

## Prasyarat Instalasi

1.  **PHP >= 8.2**
2.  **Composer**
3.  **Node.js & NPM** (untuk frontend build)
4.  **Database** (SQLite default, bisa MySQL, PostgreSQL, dll)
5.  **Layanan OCR Python** berjalan dan dapat diakses (URL dikonfigurasi di `.env`). Layanan ini memiliki prasyaratnya sendiri (Python, EasyOCR, Poppler, dll).

## Instalasi

1.  Clone repositori:
    ```bash
    git clone [https://github.com/username/nama-repo.git](https://github.com/username/nama-repo.git)
    cd nama-repo
    ```

2.  Install dependensi PHP:
    ```bash
    composer install
    ```

3.  Install dependensi Node.js:
    ```bash
    npm install
    ```

4.  Salin file `.env.example` menjadi `.env`:
    ```bash
    cp .env.example .env
    ```

5.  Generate kunci aplikasi Laravel:
    ```bash
    php artisan key:generate
    ```

6.  Konfigurasi file `.env`:
    * Atur koneksi database (`DB_CONNECTION`, `DB_DATABASE`, dll.). Defaultnya adalah SQLite, pastikan file `database/database.sqlite` ada (jika tidak ada, buat file kosong: `touch database/database.sqlite`).
    * Atur URL layanan OCR:
        ```dotenv
        OCR_SERVICE_URL=http://<alamat_ip_atau_domain_layanan_ocr>:<port>
        ```
        Contoh: `OCR_SERVICE_URL=http://127.0.0.1:5000` jika berjalan di mesin yang sama.

7.  Jalankan migrasi database:
    ```bash
    php artisan migrate
    ```

8.  Buat symbolic link untuk storage publik:
    ```bash
    php artisan storage:link
    ```

9.  Build aset frontend:
    ```bash
    npm run build
    ```

10. (Opsional) Jalankan server development Laravel:
    ```bash
    php artisan serve
    ```
    Dan jalankan Vite development server (jika ingin hot-reloading):
    ```bash
    npm run dev
    ```

## Susunan Project (Utama)
. ├── app/ # Kode inti Laravel (Models, Controllers, Providers) │ ├── Http/ │ │ └── Controllers/ │ │ └── OcrController.php # Logika utama aplikasi │ └── Models/ │ └── OcrDocument.php # Model Eloquent untuk tabel ocr_documents ├── bootstrap/ # Skrip bootstrap aplikasi ├── config/ # File konfigurasi │ ├── database.php │ └── services.php # Termasuk URL layanan OCR ├── database/ # Migrasi, Seeder, Factories │ └── migrations/ │ └── ..._create_ocr_documents_table.php # Skema database ├── public/ # Root dokumen web │ ├── storage/ # Symbolic link ke storage/app/public │ └── index.php ├── resources/ # Aset frontend dan view │ ├── css/ │ ├── js/ │ └── views/ │ └── ocr/ # Views terkait OCR (upload, search, viewer) ├── routes/ # Definisi route │ └── web.php ├── storage/ # File cache, log, dan file yang diunggah │ ├── app/ │ │ └── public/ # File yang dapat diakses publik │ │ └── documents/ # Tempat file asli disimpan │ └── framework/ │ └── logs/ ├── tests/ # Unit & Feature tests ├── vendor/ # Dependensi Composer ├── .env # Konfigurasi environment (JANGAN DI-COMMIT) ├── .env.example # Contoh file environment ├── artisan # CLI Laravel ├── composer.json # Dependensi PHP ├── package.json # Dependensi Node.js └── vite.config.js # Konfigurasi Vite

## Contoh Penggunaan

1.  **Pastikan Layanan OCR Python berjalan.**
2.  **Jalankan Aplikasi Laravel:**
    * Jika menggunakan server development: `php artisan serve`
    * Akses aplikasi melalui browser (misal: `http://127.0.0.1:8000`).
3.  **Upload Dokumen:**
    * Buka halaman `/ocr`.
    * Pilih file gambar atau PDF.
    * Klik tombol "Extract Text". Aplikasi akan mengirim file ke layanan OCR, menyimpan hasilnya, dan menampilkan pesan sukses. Jika berhasil, Anda akan diarahkan ke halaman viewer.
4.  **Cari Dokumen:**
    * Buka halaman `/search`.
    * Masukkan kata kunci di kolom pencarian.
    * Klik tombol "Search". Hasil yang cocok akan ditampilkan.
5.  **Lihat Dokumen:**
    * Klik pada hasil pencarian untuk membuka halaman viewer PDF atau gambar (`/documents/{id}` atau `/images/{id}`).
    * Di halaman viewer, Anda dapat melihat dokumen asli dan teks yang diekstraksi secara berdampingan. Gunakan kolom pencarian di sisi teks untuk menyorot kata kunci.

## Kontribusi

Kontribusi sangat kami harapkan! Silakan fork repositori ini dan buat *pull request* untuk setiap perbaikan atau penambahan fitur.

1.  Fork repositori ini.
2.  Buat *branch* fitur Anda (`git checkout -b fitur/FiturBaru`).
3.  Commit perubahan Anda (`git commit -m 'Menambahkan FiturBaru'`).
4.  Push ke *branch* Anda (`git push origin fitur/FiturBaru`).
5.  Buka *Pull Request*.

## Lisensi

Proyek ini dilisensikan di bawah Lisensi MIT.

---

**MIT License**

Copyright (c) 2025 Jonathan Axl Wibowo

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
