# DOKUMEN AUDIT ARSITEKTUR BACKEND MARKETPLACE FOOTWORK
**Versi:** 1.0  
**Tanggal:** 6 November 2026  
**Peran Auditor:** Senior Laravel Software Architect, Database Optimization Specialist, & Code Refactoring Engineer  
**Teknologi:** Laravel 11.x, PHP 8.2+, SQLite / MySQL, Alpine.js, Tailwind CSS  

---

## 1. RINGKASAN EKSEKUTIF AUDIT

Audit mendalam terhadap implementasi backend FOOTWORK mencakup analisis menyeluruh terhadap Controllers, Models, Services, Query Execution, Database Transactions, dan Integrasi Frontend-Backend.

Tujuan utama audit adalah mengidentifikasi masalah arsitektural berupa:
1. **Latent Bugs / Schema Inconsistencies:** Kesalahan definisi query/kolom yang berpotensi memicu query gagal atau data kosong.
2. **Kueri N+1 dan Kueri Berulang:** Eksekusi puluhan hingga ratusan kueri terpisah pada halaman laporan dan analitik.
3. **Duplikasi Business Logic:** Logika kalkulasi kupon dan validasi diskon yang terfragmentasi di beberapa controller tanpa satu sumber kebenaran (*single source of truth*).
4. **Keamanan Transaksi & Integritas Data (ACID):** Mutasi data multi-tabel tanpa `DB::transaction` dan decrement kuota tanpa *row-level locking*.
5. **Dead Queries & Unused Eager Loads:** Pemanggilan database yang datanya tidak pernah dirender ke tampilan atau diekspor ke file.

---

## 2. MATRIKS KLASIFIKASI TEMUAN AUDIT

| No | Komponen / Lokasi | Kategori | Tingkat Keparahan | Dampak pada Sistem | Status |
|---|---|---|---|---|---|
| 1 | `Product::updatePriceRange()` (`app/Models/Product.php`) | Schema Inconsistency | **CRITICAL** | Memanggil kolom `price_umum` yang tidak ada pada tabel `product_variant_prices` (kolom sebenarnya: `price`), menyebabkan `QueryException: Unknown column`. | **TERATASI** |
| 2 | `Product::scopePreOrder()` (`app/Models/Product.php`) | Logical Bug | **HIGH** | Memfilter `product_type = 'pre_order'` padahal nilai ENUM migrasi adalah `'po'`. Kueri selalu mengembalikan 0 produk PO. | **TERATASI** |
| 3 | Logika Kupon & Diskon (`FrontController` & `CheckoutController`) | Logic Duplication & Security Gap | **HIGH** | `CheckoutController` menduplikasi rumus kalkulasi diskon tetapi mengabaikan validasi tanggal, kuota, target audiens, dan batas pembelian. Kuota didekremen di luar transaksi. | **TERATASI** |
| 4 | Laporan Performa Mitra (`AdminSellerController@salesReport` & `exportSalesReport`) | Database / N+1 Problem | **HIGH** | Loop per mitra mengeksekusi 3-4 kueri terpisah per seller. Pada 50 mitra, terjadi lebih dari 150-200 kueri SQL individual. | **TERATASI** |
| 5 | Metrik Dashboard Admin (`AdminDashboardController@index`) | Query Redundancy | **MEDIUM** | Mengeksekusi 11 kueri agregat terpisah (`count`, `sum`) ke tabel `orders` secara sekuensial. | **TERATASI** |
| 6 | Verifikasi Pembayaran (`AdminOrderController@verifyPayment`) | ACID / Data Consistency | **HIGH** | Pembaruan tabel `payment_confirmations` dan tabel `orders` dilakukan terpisah tanpa perlindungan `DB::transaction()`. | **TERATASI** |
| 7 | Dashboard Akun Pelanggan (`AccountController@dashboard`) | Query Redundancy | **MEDIUM** | 4 kueri terpisah untuk menghitung pesanan aktif, selesai, total, dan total belanja pelanggan. | **TERATASI** |
| 8 | Detail Produk Katalog (`FrontController@productDetail`) | Dead Query | **LOW** | Menjalankan kueri `$relatedProducts` dari database yang tidak pernah dirender pada Blade template `front/product-detail.blade.php`. | **TERATASI** |
| 9 | Tambah ke Keranjang (`CartController@add`) | Redundant Query | **LOW** | Memanggil `$product->images()->first()` dua kali berturut-turut tanpa memanfaatkan eager loading atau accessor model. | **TERATASI** |
| 10 | Ekspor Pesanan Excel (`AdminOrderController@exportExcel`) | Memory Inefficiency | **LOW** | Melakukan eager load relasi `items` untuk ratusan baris pesanan padahal kolom detail item tidak dicetak pada lembar Excel. | **TERATASI** |
