# DOKUMEN REFACTORING ARSITEKTUR & BACKEND MARKETPLACE FOOTWORK
**Versi:** 1.0  
**Tanggal:** 6 November 2026  
**Standar Arsitektur:** Clean Code, SOLID Principles, DRY (Don't Repeat Yourself), Transaction Atomicity  

---

## 1. PENDEKATAN REFACTORING

Refactoring dilakukan secara terarah pada titik-titik kritis performa, integritas data, dan duplikasi kode tanpa mengubah antarmuka visual (UI/UX) maupun *behavior* yang diharapkan oleh pengguna akhir:

1. **Prinsip Single Responsibility:** Memindahkan seluruh kalkulasi diskon dan aturan validasi kupon dari controller ke domain service khusus (`CouponService`).
2. **Kueri Agregasi Bersyarat (Conditional Aggregation):** Mengganti rangkaian kueri `count()` dan `sum()` individual menjadi kueri agregat tunggal berbasis ANSI SQL `CASE WHEN`.
3. **Bulk Aggregation & Grouping:** Mengeliminasi loop query N+1 dengan `groupBy('seller_id')` dan mapping menggunakan collection key (`keyBy`).
4. **Perlindungan ACID & Locking:** Membungkus operasi multi-tabel dalam `DB::transaction()` serta menggunakan `lockForUpdate()` saat mengurangi kuota kupon.
5. **Koreksi Skema Model:** Memperbaiki referensi kolom dan nilai enum yang tidak sinkron antara migration dan Eloquent Model.

---

## 2. DETAIL PERUBAHAN IMPLEMENTASI

### 2.1. Pembuatan `App\Services\CouponService`
Logika kupon yang sebelumnya tercecer di `FrontController` dan `CheckoutController` dikonsolidasikan ke dalam service baru: `app/Services/CouponService.php`.

#### Metode Utama:
- `validate(Coupon $coupon, float $subtotal, int $totalQuantity = 1, ?User $user = null): array`  
  Memvalidasi status aktif, rentang tanggal (`starts_at`, `expires_at`), ketersediaan kuota (`quota_total`), pembatasan audiens (`member_only`, `non_member_only`), batas minimal belanja (`min_purchase`), dan kuantitas item minimal (`min_quantity`).
- `calculateDiscount(Coupon $coupon, float $subtotal): float`  
  Menghitung diskon persentase (dengan batas `max_discount`) atau diskon nominal tetap (*fixed*), memastikan nominal diskon tidak melebihi subtotal.
- `check(string $code, float $subtotal, int $totalQuantity = 1, ?User $user = null): array`  
  Metode *facade* yang mencari kupon berdasarkan kode, memvalidasi aturan, dan mengembalikan payload JSON siap pakai bagi frontend.

---

### 2.2. Refactoring Controller
- **`FrontController`:** Menggunakan `CouponService` pada `checkCoupon`, menghapus kueri dead code `$relatedProducts`.
- **`CheckoutController`:** Memvalidasi kupon secara menyeluruh, memindahkan decrement kuota ke dalam `DB::transaction()` dengan `lockForUpdate()`.
- **`AdminDashboardController`:** Mengonsolidasikan 11 kueri terpisah menjadi 1 kueri agregat SQL.
- **`AdminSellerController`:** Mengeliminasi N+1 pada `salesReport` dan `exportSalesReport` dengan grouped query.
- **`AdminOrderController`:** Membungkus `verifyPayment` dalam `DB::transaction()`, optimasi eager load pada `exportExcel`.
- **`AccountController`:** Mengonsolidasikan kueri statistik akun dalam 1 kueri agregasi.
- **`CartController`:** Memanfaatkan eager load `images` dan accessor `primary_image`.

---

### 2.3. Perbaikan Model `Product`
- `scopePreOrder`: Memperbaiki pencarian status `'po'`.
- `updatePriceRange`: Memperbaiki kolom pencarian menjadi `'price'`.
