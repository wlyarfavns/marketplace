# DOKUMEN PENGUJIAN & VERIFIKASI BACKEND MARKETPLACE FOOTWORK
**Versi:** 1.0  
**Tanggal:** 6 November 2026  
**Cakupan Pengujian:** Unit Testing, Feature Testing, Transaction Verification & Regression Testing  
**Tooling:** PHPUnit 11.5, Laravel Testing Framework  

---

## 1. STRUKTUR & INVENTARIS TEST SUITE

Test suite FOOTWORK terdiri dari 6 berkas pengujian terstruktur:

| No | Berkas Test | Kategori | Jumlah Test | Fokus Pengujian |
|---|---|---|:---:|---|
| 1 | `tests/Unit/CouponServiceTest.php` | Unit Test | 5 | Kalkulasi diskon persentase, pembatasan max discount, diskon fixed, validasi min belanja & qty, pembatasan member/non-member, filter `scopePreOrder`, dan sinkronisasi `updatePriceRange`. |
| 2 | `tests/Feature/BackendOptimizationTest.php` | Feature Test | 5 | Kueri agregat tunggal Admin Dashboard, Laporan & Ekspor Seller, Kueri statistik Dashboard Akun Pelanggan, Atomisitas transaksi verifikasi pembayaran, dan Eager load keranjang belanja. |
| 3 | `tests/Feature/PasarKitaTest.php` | Feature / E2E | 18 | Akses 16 modul admin, AJAX check seller & coupon, alur checkout lengkap, upload bukti bayar, verifikasi bayar admin, faktur PDF, dan ekspor excel. |
| 4 | `tests/Feature/AdminSpaTest.php` | Feature Test | 3 | Navigasi SPA back-office, persistensi layout sidebar/navbar, dan respons parsial konten SPA. |
| 5 | `tests/Feature/FrontSpaTest.php` | Feature Test | 6 | Transisi SPA storefront, isolasi rute admin dari SPA storefront, filter katalog tanpa reload, drawer keranjang, dan fallback navigasi browser. |
| 6 | `tests/Feature/OtpRegistrationTest.php` | Feature Test | 5 | Autentikasi pelanggan berbasis OTP WhatsApp, validasi nomor HP, multi-domain login (PasarKita & FOOTWORK). |
| 7 | `tests/Unit/ExampleTest.php` & `Feature/ExampleTest.php` | Smoke Test | 2 | Pemeriksaan dasar kesiapan lingkungan pengujian. |

---

## 2. HASIL EKSEKUSI PENGUJIAN OTOMATIS (PHPUNIT)

```text
---------------------------------------------------------------------------------
Tests:    44 passed (340 assertions)
Duration: 5.66s
Status:   100% GREEN (ALL PASS)
---------------------------------------------------------------------------------
```
