# 👟 PasarKita — Toko Sepatu Branded & Sneakers Original

Platform e-commerce dan marketplace kurasi sepatu branded & sneakers original berbasis **Laravel 11 & PHP 8.5**, dirancang dengan estetika modern **Editorial Minimalist (Dominan Putih & Hitam)**. Mendukung penjualan **Ready Stock** dan **Pre-Order (PO) dengan skema Down Payment (DP)** terpercaya, serta sistem kemitraan tier member dan distributor/reseller.

---

## 🌟 Fitur Utama & Keunggulan Sistem

### 1. Sistem 5 Role Pengguna Terintegrasi
- **Pengunjung (Guest / Non-Member)**: 
  - Menjelajah Beranda, Banner Promo, dan 4 Pilar Keaslian Sepatu.
  - Berbelanja produk Ready Stock melalui fitur **Checkout Cepat (Guest Checkout)** tanpa harus mendaftar terlebih dahulu.
  - Terkunci dari katalog Pre-Order (PO).
- **Member (Komunitas Sneakerhead)**:
  - Akses eksklusif ke katalog **Pre-Order (PO)** rilisan terbatas dan kupon promo khusus member.
  - Halaman **Akun Saya**: Statistik Belanja (Total Pesanan, Aktif, Selesai, Total Belanja), Buku Alamat Default, Preferensi ID Seller, Ekspedisi Favorit.
- **Distributor (Tier B2B / Reseller Sepatu)**:
  - Seluruh akses Member + Paket Seri Grosir B2B Size Run (EU 40–44).
  - Menerapkan **Rule Harga Otomatis (Potongan Grosir per pasang)** dan **Bonus Barang (FREE Sneaker Cleaner Kit)** untuk pembelian partai.
- **Mitra Penjual (Sellers)**:
  - Memiliki kode unik **ID Seller** yang terikat pada Brand tertentu.
  - Pembeli memasukkan ID Seller saat checkout, omzet dan komisi tercatat otomatis secara real-time.
- **Administrator (Super Admin)**:
  - Akses penuh (CRUD) ke seluruh **16 Modul Back-Office** pengelolaan toko sepatu.

### 2. Panel Admin (16 Modul Back-Office Lengkap)
1. **Dashboard Analitik & Statistik Down Payment (DP)**
2. **Kelola Pesanan & Cetak Faktur PDF Resmi (`FKT-YYYYMMDD-XXXX`)**
3. **Laporan Penjualan & Performa Mitra (Closing Real-Time + Export Excel)**
4. **Manajemen Produk Kompleks (Matriks Model/Box × Ukuran EU 38–45, Warna Hex, Bonus)**
5. **Manajemen Brand (Persentase DP%, Urutan Homepage, Logo)**
6. **Manajemen Kategori (Sneakers & Lifestyle, Running, Basketball, Retro, Skate, Slides, Gym, Trail)**
7. **Mitra Seller (Registrasi ID Seller per Brand)**
8. **Kelola Pengguna (Ubah Role & Customer Tier: Umum, Member, Distributor)**
9. **Manajemen Kupon Pemasaran (Persentase/Nominal, Kuota, Target Audiens)**
10. **Banner Promosi Slider (Rasio Landscape 21:7)**
11. **Pilar Keunggulan Toko (100% Original BNIB, Double-Box Shipping, dsb)**
12. **Customer Service & Penentuan Tepat 1 Floating WhatsApp Utama**
13. **Rekening Bank Transfer Resmi (BCA, Mandiri, BSI)**
14. **Media Sosial (Instagram, TikTok, YouTube)**
15. **Admin Handle Order**
16. **Tautan Langsung ke Front-Store Toko**

---

## 👟 Katalog Brand & Ukuran
- **8 Brand Resmi**: Nike, Air Jordan, Adidas, New Balance, Puma, Asics, Converse, Vans.
- **Size Run Standar**: EU 38, EU 39, EU 40, EU 41, EU 42, EU 43, EU 44, EU 45.
- **Metode Pembayaran**: Transfer Bank Penuh (100%) & Down Payment (DP) 25%–35%.

---

## 🔑 Akun Demo Siap Pakai

| Peran / Role | Email | Password | Hak Akses |
|--------------|-------|----------|-----------|
| **Super Admin** | `admin@pasarkita.com` | `password` | Akses penuh 16 modul di `/admin/dashboard` |
| **Member** | `member@pasarkita.com` | `password` | Akses Pre-Order (PO), Akun Saya, Kupon Member |
| **Distributor** | `distributor@pasarkita.com` | `password` | Akses Produk B2B Grosir + Diskon Otomatis |
| **Customer Umum** | `umum@pasarkita.com` | `password` | Belanja Ready Stock & Guest Checkout |

---

## 🚀 Panduan Menjalankan Aplikasi

1. **Jalankan Web Server Laravel**:
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses di: `http://localhost:8000`

2. **Akses Panel Admin**:
   Buka: `http://localhost:8000/admin/login`
   Gunakan akun: `admin@pasarkita.com` / `password`
