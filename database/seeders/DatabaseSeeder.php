<?php

namespace Database\Seeders;

use App\Models\AdminHandle;
use App\Models\BankAccount;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\CustomerService;
use App\Models\Feature;
use App\Models\Product;
use App\Models\ProductBonus;
use App\Models\ProductColor;
use App\Models\ProductImage;
use App\Models\ProductModel;
use App\Models\ProductPriceRule;
use App\Models\ProductSize;
use App\Models\ProductVariantPrice;
use App\Models\Seller;
use App\Models\SocialMedia;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Users dengan berbagai Role & Tipe
        $admin = User::create([
            'name' => 'Administrator PasarKita',
            'email' => 'admin@pasarkita.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'customer_type' => 'umum',
            'phone' => '081234567890',
            'address' => 'PasarKita Sneaker Hub Lt. 3, Jl. Sudirman No. 12',
            'city' => 'Jakarta Selatan',
            'province' => 'DKI Jakarta',
            'postal_code' => '12190',
            'status' => 'active',
        ]);

        $member = User::create([
            'name' => 'Dimas Sneakerhead (Member)',
            'email' => 'member@pasarkita.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'customer_type' => 'member',
            'phone' => '081298765432',
            'address' => 'Komp. Melati Indah Blok B3 No. 14',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'postal_code' => '40123',
            'default_seller_code' => 'MITRA-001',
            'favorite_courier' => 'JNE',
            'status' => 'active',
        ]);

        $distributor = User::create([
            'name' => 'Toko Sepatu Jaya (Distributor)',
            'email' => 'distributor@pasarkita.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'customer_type' => 'distributor',
            'phone' => '081377889900',
            'address' => 'Ruko Sneaker Square Blok A No. 8',
            'city' => 'Surabaya',
            'province' => 'Jawa Timur',
            'postal_code' => '60281',
            'default_seller_code' => 'MITRA-002',
            'favorite_courier' => 'SiCepat',
            'status' => 'active',
        ]);

        $customerUmum = User::create([
            'name' => 'Budi Santoso (Customer Biasa)',
            'email' => 'umum@pasarkita.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'customer_type' => 'umum',
            'phone' => '081555667788',
            'address' => 'Jl. Cempaka Putih Timur No. 45',
            'city' => 'Jakarta Pusat',
            'province' => 'DKI Jakarta',
            'postal_code' => '10510',
            'favorite_courier' => 'J&T',
            'status' => 'active',
        ]);

        // 2. Data Master Brands (8 Brand Sepatu Branded Dunia)
        $brandsData = [
            [
                'name' => 'Nike',
                'slug' => 'nike',
                'dp_percentage' => 30.00,
                'description' => 'Pionir inovasi sepatu olahraga & sneakers lifestyle dunia dengan lini ikonik Dunk, Air Max, Pegasus, dan Metcon.',
                'show_on_homepage' => true,
                'homepage_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Air Jordan',
                'slug' => 'air-jordan',
                'dp_percentage' => 35.00,
                'description' => 'Sub-brand legendaris Michael Jordan yang mendefinisikan kultur sneaker basket dunia lewat seri Retro.',
                'show_on_homepage' => true,
                'homepage_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Adidas',
                'slug' => 'adidas',
                'dp_percentage' => 25.00,
                'description' => 'Ikon streetwear dan terrace fashion asal Jerman dengan siluet retro klasik Samba, Ultraboost, dan Adilette.',
                'show_on_homepage' => true,
                'homepage_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'New Balance',
                'slug' => 'new-balance',
                'dp_percentage' => 30.00,
                'description' => 'Kenyamanan legendaris asal Boston dengan siluet retro-runner 550, 990v5, dan Fresh Foam Hierro Trail.',
                'show_on_homepage' => true,
                'homepage_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Puma',
                'slug' => 'puma',
                'dp_percentage' => 25.00,
                'description' => 'Desain klasik retro motorsport dan gym training lewat rilisan Palermo dan Fuse 2.0.',
                'show_on_homepage' => true,
                'homepage_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Asics',
                'slug' => 'asics',
                'dp_percentage' => 30.00,
                'description' => 'Performa lari dan tren Japanese techwear dengan teknologi bantalan GEL Kayano dan Trabuco Trail.',
                'show_on_homepage' => true,
                'homepage_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Converse',
                'slug' => 'converse',
                'dp_percentage' => 25.00,
                'description' => 'Sepatu kanvas ikonik Chuck Taylor All Star 70s dan One Star Pro Skate yang timeless dari masa ke masa.',
                'show_on_homepage' => true,
                'homepage_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Vans',
                'slug' => 'vans',
                'dp_percentage' => 25.00,
                'description' => 'Kultur skate legendaris California lewat siluet Old Skool dan Classic Slip-On sol waffle ikonik.',
                'show_on_homepage' => true,
                'homepage_order' => 8,
                'is_active' => true,
            ],
        ];

        $createdBrands = [];
        foreach ($brandsData as $b) {
            $createdBrands[$b['slug']] = Brand::create($b);
        }

        // 3. Data Master Categories (8 Kategori Sepatu Branded)
        $categoriesData = [
            ['name' => 'Sneakers & Gaya Hidup', 'slug' => 'sneakers-lifestyle', 'type' => 'unisex', 'description' => 'Koleksi sneakers kasual dan street style untuk gaya harian modern.'],
            ['name' => 'Sepatu Lari & Performa', 'slug' => 'running-performance', 'type' => 'unisex', 'description' => 'Sepatu lari berteknologi bantalan responsif untuk aktivitas tinggi.'],
            ['name' => 'Sepatu Basket & Lapangan', 'slug' => 'basketball-court', 'type' => 'pria', 'description' => 'Koleksi sepatu basket ikonik dan court performance berdesain kokoh.'],
            ['name' => 'Siluet Klasik & Retro Heritage', 'slug' => 'classic-heritage', 'type' => 'unisex', 'description' => 'Siluet vintage timeless yang menjadi legenda mode sepanjang masa.'],
            ['name' => 'Slip-On & Sepatu Skate', 'slug' => 'slip-on-skate', 'type' => 'unisex', 'description' => 'Model kasual praktis dan sepatu skate bersol karet vulkanisir kuat.'],
            ['name' => 'Sandal Slide & Kenyamanan', 'slug' => 'sandals-slides', 'type' => 'unisex', 'description' => 'Sandal slide santai berbobot ringan untuk kenyamanan maksimal.'],
            ['name' => 'Latihan Fitnes & Gym', 'slug' => 'training-gym', 'type' => 'unisex', 'description' => 'Sepatu fitness, cross-training, dan latihan gym dengan stabilitas pijakan kuat.'],
            ['name' => 'Aktivitas Luar Ruang & Trail', 'slug' => 'outdoor-trail', 'type' => 'unisex', 'description' => 'Sepatu kegiatan luar ruang dengan traksi sol tahan banting di segala medan.'],
        ];

        $createdCategories = [];
        foreach ($categoriesData as $c) {
            $createdCategories[$c['slug']] = Category::create(array_merge($c, ['is_active' => true]));
        }

        // 4. Data Master Sellers (Mitra & Outlet Resmi PasarKita)
        $sellersData = [
            ['seller_code' => 'MITRA-001', 'brand_id' => $createdBrands['nike']->id, 'name' => 'PasarKita Jakarta Flagship', 'phone' => '081299112233'],
            ['seller_code' => 'MITRA-002', 'brand_id' => $createdBrands['air-jordan']->id, 'name' => 'PasarKita Hub Surabaya', 'phone' => '081388223344'],
            ['seller_code' => 'MITRA-003', 'brand_id' => $createdBrands['adidas']->id, 'name' => 'PasarKita Hub Bandung', 'phone' => '081577334455'],
            ['seller_code' => 'SELLER-SOLO', 'brand_id' => $createdBrands['new-balance']->id, 'name' => 'PasarKita Official Outlet Solo', 'phone' => '081866445566'],
        ];

        foreach ($sellersData as $s) {
            Seller::create(array_merge($s, ['is_active' => true]));
        }

        // 5. Data Bank Accounts
        $banks = [
            ['bank_name' => 'Bank Central Asia (BCA)', 'account_number' => '8820991823', 'account_holder_name' => 'PT PASARKITA DIGITAL INDONESIA', 'is_active' => true],
            ['bank_name' => 'Bank Mandiri', 'account_number' => '1370019283741', 'account_holder_name' => 'PT PASARKITA DIGITAL INDONESIA', 'is_active' => true],
            ['bank_name' => 'Bank Syariah Indonesia (BSI)', 'account_number' => '7112233445', 'account_holder_name' => 'PT PASARKITA DIGITAL INDONESIA', 'is_active' => true],
        ];
        foreach ($banks as $b) {
            BankAccount::create($b);
        }

        // 6. Data Customer Service (Termasuk Floating WA)
        CustomerService::create([
            'name' => 'CS PasarKita Sneaker Care',
            'phone' => '6281234567890',
            'is_floating_whatsapp' => true,
            'is_active' => true,
        ]);
        CustomerService::create([
            'name' => 'CS Kemitraan & Reseller Sepatu',
            'phone' => '6281298765432',
            'is_floating_whatsapp' => false,
            'is_active' => true,
        ]);

        // 7. Data Admin Handles
        AdminHandle::create(['code' => 'ADM-01', 'name' => 'Fauzan (Pemeriksa Keaslian & Operasional)', 'is_active' => true]);
        AdminHandle::create(['code' => 'ADM-02', 'name' => 'Nadia (Verifikasi Keuangan & DP)', 'is_active' => true]);

        // 8. Data Keunggulan (Features)
        $features = [
            ['title' => '100% Sepatu Asli Bergaransi Resmi', 'description' => 'Jaminan keaslian 100% uang kembali untuk seluruh koleksi sepatu branded resmi di PasarKita.', 'sort_order' => 1],
            ['title' => 'Skema Prapesan (DP) Terpercaya', 'description' => 'Pesan sneaker langka cukup dengan Down Payment (DP) mulai 25%, pelunasan saat sepatu siap dikirim.', 'sort_order' => 2],
            ['title' => 'Tingkat Member & Diskon Grosir', 'description' => 'Dapatkan penawaran harga terbaik untuk reseller dan komunitas dengan potongan harga otomatis.', 'sort_order' => 3],
            ['title' => 'Pengemasan Kotak Ganda Aman', 'description' => 'Setiap pasang sepatu dikemas menggunakan kardus tebal ganda (double-box) dan bubble wrap pelindung.', 'sort_order' => 4],
        ];
        foreach ($features as $f) {
            Feature::create(array_merge($f, ['is_active' => true]));
        }

        // 9. Data Social Media
        $socials = [
            ['platform' => 'Instagram', 'icon_class' => 'fab fa-instagram', 'brand_color' => '#000000', 'url' => 'https://instagram.com/pasarkita.official', 'sort_order' => 1],
            ['platform' => 'TikTok', 'icon_class' => 'fab fa-tiktok', 'brand_color' => '#000000', 'url' => 'https://tiktok.com/@pasarkita.id', 'sort_order' => 2],
            ['platform' => 'YouTube', 'icon_class' => 'fab fa-youtube', 'brand_color' => '#000000', 'url' => 'https://youtube.com/@pasarkita', 'sort_order' => 3],
        ];
        foreach ($socials as $s) {
            SocialMedia::create(array_merge($s, ['is_active' => true]));
        }

        // 10. Data Banners Promo (Lokal, Ringan, Dominan Putih)
        Banner::create([
            'title' => 'Rilisan Resmi: Koleksi Sepatu Branded Terbaru',
            'description' => 'Koleksi rilisan terbaru dari Nike, Jordan, Adidas, hingga New Balance dengan jaminan 100% keaslian original BNIB.',
            'image_path' => 'images/banners/banner-official-drop.jpg',
            'url' => '/katalog',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        Banner::create([
            'title' => 'Prapesan Rilisan Langka & Edisi Terbatas',
            'description' => 'Amankan siluet sneaker langka dan ukuran favorit Anda dengan skema DP mulai 25% khusus Member & Mitra PasarKita.',
            'image_path' => 'images/banners/banner-preorder-vault.jpg',
            'url' => '/katalog?type=po',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        // 11. Data Kupon
        Coupon::create([
            'code' => 'PASARKITA10',
            'discount_type' => 'percentage',
            'discount_value' => 10.00,
            'max_discount' => 100000.00,
            'target_audience' => 'all',
            'min_purchase' => 150000.00,
            'min_quantity' => 1,
            'quota_total' => 500,
            'quota_per_user' => 1,
            'restriction_type' => 'none',
            'show_on_checkout' => true,
            'starts_at' => now(),
            'expires_at' => now()->addMonths(6),
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'MEMBERKICK50',
            'discount_type' => 'fixed',
            'discount_value' => 50000.00,
            'max_discount' => null,
            'target_audience' => 'member_only',
            'min_purchase' => 1000000.00,
            'min_quantity' => 1,
            'quota_total' => 200,
            'quota_per_user' => 2,
            'restriction_type' => 'none',
            'show_on_checkout' => true,
            'starts_at' => now(),
            'expires_at' => now()->addMonths(6),
            'is_active' => true,
        ]);

        // Helper untuk mempermudah pembuatan varian sepatu lengkap
        $createShoeProduct = function (array $cfg) {
            $product = Product::create([
                'brand_id' => $cfg['brand_id'],
                'sku' => $cfg['sku'],
                'name' => $cfg['name'],
                'slug' => $cfg['slug'],
                'product_type' => $cfg['product_type'], // ready_stock atau po
                'visible_to_umum' => $cfg['visible_to_umum'] ?? true,
                'visible_to_member' => $cfg['visible_to_member'] ?? true,
                'visible_to_distributor' => $cfg['visible_to_distributor'] ?? true,
                'weight_grams' => $cfg['weight_grams'] ?? 950,
                'product_note' => $cfg['product_note'],
                'description' => $cfg['description'],
                'min_price' => $cfg['min_price'],
                'max_price' => $cfg['max_price'],
                'status' => 'active',
            ]);

            // Kategori
            if (! empty($cfg['category_ids'])) {
                $product->categories()->attach($cfg['category_ids']);
            }

            // Foto Produk (Lokal)
            if (! empty($cfg['images'])) {
                foreach ($cfg['images'] as $idx => $imgPath) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $imgPath,
                        'sort_order' => $idx + 1,
                        'is_primary' => $idx === 0,
                    ]);
                }
            }

            // Warna
            if (! empty($cfg['colors'])) {
                foreach ($cfg['colors'] as $cIdx => $c) {
                    ProductColor::create([
                        'product_id' => $product->id,
                        'color_name' => $c['name'],
                        'hex_code' => $c['hex'],
                        'sort_order' => $cIdx + 1,
                    ]);
                }
            }

            // Model / Box Editions
            $createdModels = [];
            if (! empty($cfg['models'])) {
                foreach ($cfg['models'] as $mIdx => $m) {
                    $createdModels[] = ProductModel::create([
                        'product_id' => $product->id,
                        'model_name' => $m['name'],
                        'description' => $m['description'] ?? null,
                        'sort_order' => $mIdx + 1,
                    ]);
                }
            }

            // Ukuran (Sizes EU 38-45)
            $createdSizes = [];
            if (! empty($cfg['sizes'])) {
                foreach ($cfg['sizes'] as $sIdx => $s) {
                    $createdSizes[] = ProductSize::create([
                        'product_id' => $product->id,
                        'size_label' => $s,
                        'is_available' => true,
                        'sort_order' => $sIdx + 1,
                    ]);
                }
            }

            // Matriks Harga Variant (Model x Size)
            if (! empty($createdModels) && ! empty($createdSizes)) {
                foreach ($createdModels as $mIndex => $cModel) {
                    $extraPrice = $mIndex * ($cfg['model_price_increment'] ?? 50000);
                    foreach ($createdSizes as $cSize) {
                        ProductVariantPrice::create([
                            'product_id' => $product->id,
                            'product_model_id' => $cModel->id,
                            'product_size_id' => $cSize->id,
                            'price' => $cfg['min_price'] + $extraPrice,
                            'stock' => $cfg['default_stock'] ?? 20,
                        ]);
                    }
                }
            }

            // Bonus Item
            if (! empty($cfg['bonus'])) {
                ProductBonus::create([
                    'product_id' => $product->id,
                    'free_item_name' => $cfg['bonus']['name'],
                    'free_item_description' => $cfg['bonus']['description'],
                    'min_quantity' => $cfg['bonus']['min_qty'] ?? 2,
                    'is_active' => true,
                ]);
            }

            // Price Rule (Distributor / Grosir)
            if (! empty($cfg['price_rule'])) {
                ProductPriceRule::create([
                    'product_id' => $product->id,
                    'rule_type' => $cfg['price_rule']['type'],
                    'amount' => $cfg['price_rule']['amount'],
                    'description' => $cfg['price_rule']['description'],
                    'is_active' => true,
                ]);
            }

            return $product;
        };

        // Standard Shoe Sizes Array
        $fullShoeSizes = [
            'EU 38 (US 5.5)',
            'EU 39 (US 6.5)',
            'EU 40 (US 7)',
            'EU 41 (US 8)',
            'EU 42 (US 8.5)',
            'EU 43 (US 9.5)',
            'EU 44 (US 10)',
            'EU 45 (US 11)',
        ];

        // 12. BUAT 22 PRODUK LENGKAP UNTUK SEMUA BRAND & SEMUA KATEGORI (TIDAK ADA FILTER KOSONG)

        // BRAND 1: NIKE
        // Nike 1: Dunk Low Panda
        $createShoeProduct([
            'brand_id' => $createdBrands['nike']->id,
            'sku' => 'NKE-DNK-001',
            'name' => 'Nike Dunk Low Retro "Panda" Hitam Putih',
            'slug' => 'nike-dunk-low-retro-panda',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'weight_grams' => 950,
            'product_note' => '100% Sepatu Baru Dalam Kotak Resmi (BNIB) Bergaransi Asli. Dilengkapi tag resmi & pengemasan kotak ganda tebal.',
            'description' => 'Siluet sneaker paling populer dan serbaguna di dunia. Material kulit asli berkualitas tinggi dengan perpaduan kontras hitam dan putih ikonik, sangat cocok untuk gaya kasual harian.',
            'min_price' => 1549000,
            'max_price' => 1649000,
            'model_price_increment' => 100000,
            'category_ids' => [
                $createdCategories['sneakers-lifestyle']->id,
                $createdCategories['classic-heritage']->id,
                $createdCategories['basketball-court']->id,
            ],
            'images' => ['images/shoes/nike-dunk-panda.jpg'],
            'colors' => [
                ['name' => 'Hitam Putih (Panda)', 'hex' => '#000000'],
                ['name' => 'Putih Bersih (Triple White)', 'hex' => '#FFFFFF'],
                ['name' => 'Hitam Pekat (Stealth Black)', 'hex' => '#111827'],
            ],
            'models' => [
                ['name' => 'Kotak Standar Resmi', 'description' => 'Kotak original bawaan pabrik Nike BNIB'],
                ['name' => 'Edisi Kolektor (Kotak Akrilik)', 'description' => 'Termasuk kotak pajangan akrilik tebal transparan'],
            ],
            'sizes' => $fullShoeSizes,
            'bonus' => [
                'name' => 'Paket Pembersih Sneaker & Lap Mikrofiber',
                'description' => 'Gratis 1 kit pembersih premium setiap pembelian minimal 2 pasang sepatu.',
                'min_qty' => 2,
            ],
        ]);

        // Nike 2: Pegasus 40 Running
        $createShoeProduct([
            'brand_id' => $createdBrands['nike']->id,
            'sku' => 'NKE-PEG-002',
            'name' => 'Nike Air Zoom Pegasus 40 Sepatu Lari',
            'slug' => 'nike-air-zoom-pegasus-40',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'weight_grams' => 880,
            'product_note' => 'Sepatu lari andalan dengan unit ganda bantalan Zoom Air untuk pantulan responsif dan kenyamanan jarak jauh.',
            'description' => 'Pegasus 40 dirancang untuk memberikan kenyamanan maksimal saat berlari maupun aktivitas gym harian. Bagian atas menggunakan jaring bersirkulasi udara tinggi yang sejuk di kaki.',
            'min_price' => 1899000,
            'max_price' => 1999000,
            'model_price_increment' => 100000,
            'category_ids' => [
                $createdCategories['running-performance']->id,
                $createdCategories['training-gym']->id,
                $createdCategories['sneakers-lifestyle']->id,
            ],
            'images' => ['images/shoes/nike-pegasus-running.jpg'],
            'colors' => [
                ['name' => 'Merah Sport & Hitam', 'hex' => '#DC2626'],
                ['name' => 'Putih & Abu-abu', 'hex' => '#F3F4F6'],
                ['name' => 'Hitam Reflektif', 'hex' => '#1F2937'],
            ],
            'models' => [
                ['name' => 'Kotak Standar Lari', 'description' => 'Kotak orisinal resmi Nike Running BNIB'],
                ['name' => 'Edisi Performa Plus', 'description' => 'Dilengkapi kaus kaki lari antiselip resmi'],
            ],
            'sizes' => $fullShoeSizes,
        ]);

        // Nike 3: Metcon 9 Workout & Gym
        $createShoeProduct([
            'brand_id' => $createdBrands['nike']->id,
            'sku' => 'NKE-MTC-003',
            'name' => 'Nike Metcon 9 Sepatu Fitnes & Latihan Gym',
            'slug' => 'nike-metcon-9-workout-gym',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'weight_grams' => 920,
            'product_note' => 'Pelat Hyperlift lebih lebar di bagian tumit untuk kestabilan angkat beban, squat, dan latihan kekuatan tinggi.',
            'description' => 'Standar emas untuk sepatu latihan cross-training dan fitness gym. Sol karet melengkung melindungi sepatu saat memanjat tali dan memberikan daya cengkeram optimal di lantai gym maupun luar ruangan.',
            'min_price' => 2099000,
            'max_price' => 2199000,
            'model_price_increment' => 100000,
            'category_ids' => [
                $createdCategories['training-gym']->id,
                $createdCategories['outdoor-trail']->id,
                $createdCategories['sneakers-lifestyle']->id,
            ],
            'images' => ['images/shoes/nike-metcon-gym.jpg'],
            'colors' => [
                ['name' => 'Hitam Abu Gym', 'hex' => '#374151'],
                ['name' => 'Putih Monokrom', 'hex' => '#E5E7EB'],
            ],
            'models' => [
                ['name' => 'Kotak Standar Pelatihan', 'description' => 'Kotak resmi Nike Training BNIB'],
                ['name' => 'Paket Atletik Lengkap', 'description' => 'Disertai tas serut gym antiair'],
            ],
            'sizes' => ['EU 39 (US 6.5)', 'EU 40 (US 7)', 'EU 41 (US 8)', 'EU 42 (US 8.5)', 'EU 43 (US 9.5)', 'EU 44 (US 10)', 'EU 45 (US 11)'],
        ]);

        // Nike 4: Calm Slide
        $createShoeProduct([
            'brand_id' => $createdBrands['nike']->id,
            'sku' => 'NKE-SLD-004',
            'name' => 'Nike Calm Slide Sandal Santai Monokrom',
            'slug' => 'nike-calm-slide-monochrome',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'weight_grams' => 550,
            'product_note' => 'Busa lembut berkontur antiair dengan traksi karet penuh untuk pemakaian santai dan pemulihan setelah berolahraga.',
            'description' => 'Desain minimalis mulus tanpa sambungan. Memberikan kenyamanan melangkah super empuk untuk gaya kasual santai di rumah maupun jalan-jalan akhir pekan.',
            'min_price' => 699000,
            'max_price' => 749000,
            'model_price_increment' => 50000,
            'category_ids' => [
                $createdCategories['sandals-slides']->id,
                $createdCategories['slip-on-skate']->id,
                $createdCategories['sneakers-lifestyle']->id,
            ],
            'images' => ['images/shoes/nike-zoom-court.jpg'],
            'colors' => [
                ['name' => 'Hitam Arang (Black Charcoal)', 'hex' => '#111827'],
                ['name' => 'Putih Tulang (Sail White)', 'hex' => '#F4F1EA'],
            ],
            'models' => [
                ['name' => 'Kemasan Kotak Slide', 'description' => 'Kotak resmi ramah lingkungan Nike BNIB'],
            ],
            'sizes' => ['EU 38 (US 5.5)', 'EU 39 (US 6.5)', 'EU 40 (US 7)', 'EU 41 (US 8)', 'EU 42 (US 8.5)', 'EU 43 (US 9.5)', 'EU 44 (US 10)'],
        ]);

        // BRAND 2: AIR JORDAN
        // Jordan 1: Chicago High (PO)
        $createShoeProduct([
            'brand_id' => $createdBrands['air-jordan']->id,
            'sku' => 'JDN-AJ1-005',
            'name' => 'Air Jordan 1 Retro High OG "Chicago"',
            'slug' => 'air-jordan-1-retro-high-og-chicago',
            'product_type' => 'po',
            'visible_to_umum' => false,
            'visible_to_member' => true,
            'visible_to_distributor' => true,
            'weight_grams' => 1100,
            'product_note' => 'Estimasi pengiriman 10-14 hari kerja setelah batch prapesan ditutup. Pembayaran dapat menggunakan DP 35%.',
            'description' => 'Siluet legendaris Air Jordan 1 High OG Chicago yang merevolusi dunia sneaker basket sejak 1985. Menggunakan kulit asli berbutir premium dan bantalan Nike Air terenkapsulasi.',
            'min_price' => 2899000,
            'max_price' => 3099000,
            'model_price_increment' => 200000,
            'category_ids' => [
                $createdCategories['basketball-court']->id,
                $createdCategories['classic-heritage']->id,
                $createdCategories['sneakers-lifestyle']->id,
            ],
            'images' => ['images/shoes/jordan-1-chicago.jpg'],
            'colors' => [
                ['name' => 'Chicago Merah & Hitam', 'hex' => '#000000'],
                ['name' => 'Monokrom Hitam Putih', 'hex' => '#111827'],
            ],
            'models' => [
                ['name' => 'Kotak Standar High Cut OG', 'description' => 'Sepatu BNIB lengkap dengan tali bawaan resmi'],
                ['name' => 'Edisi Kolektor Brankas (Vault)', 'description' => 'Termasuk gantungan kunci Jumpman akrilik & cetakan tali 3 warna'],
            ],
            'sizes' => ['EU 40 (US 7)', 'EU 41 (US 8)', 'EU 42 (US 8.5)', 'EU 43 (US 9.5)', 'EU 44 (US 10)', 'EU 45 (US 11)'],
            'price_rule' => [
                'type' => 'cut',
                'amount' => 75000,
                'description' => 'Potongan otomatis Rp 75.000 khusus member & mitra distributor PasarKita',
            ],
        ]);

        // Jordan 2: Jordan 4 Military (PO)
        $createShoeProduct([
            'brand_id' => $createdBrands['air-jordan']->id,
            'sku' => 'JDN-AJ4-006',
            'name' => 'Air Jordan 4 Retro "Military Black"',
            'slug' => 'air-jordan-4-retro-military-black-po',
            'product_type' => 'po',
            'visible_to_umum' => false,
            'visible_to_member' => true,
            'visible_to_distributor' => true,
            'weight_grams' => 1250,
            'product_note' => 'Siluet vintage Jumpman paling dicari dengan sayap tali penyangga dan jaring samping kokoh.',
            'description' => 'Air Jordan 4 Military Black memadukan kulit putih berbutir halus, aksen suede abu-abu di ujung sepatu, dan aksen hitam pekat pada jaring samping serta tumit.',
            'min_price' => 3499000,
            'max_price' => 3699000,
            'model_price_increment' => 200000,
            'category_ids' => [
                $createdCategories['basketball-court']->id,
                $createdCategories['sneakers-lifestyle']->id,
                $createdCategories['training-gym']->id,
            ],
            'images' => ['images/shoes/jordan-4-military.jpg'],
            'colors' => [
                ['name' => 'Putih & Hitam Militer', 'hex' => '#000000'],
            ],
            'models' => [
                ['name' => 'Kotak Retro Marmer BNIB', 'description' => 'Kotak resmi bercorak marmer retro Jordan 4'],
            ],
            'sizes' => ['EU 40 (US 7)', 'EU 41 (US 8)', 'EU 42 (US 8.5)', 'EU 43 (US 9.5)', 'EU 44 (US 10)', 'EU 45 (US 11)'],
        ]);

        // Jordan 3: Hydro Slide (Ready Stock)
        $createShoeProduct([
            'brand_id' => $createdBrands['air-jordan']->id,
            'sku' => 'JDN-SLD-007',
            'name' => 'Jordan Hydro 8 Sandal Slide Pemulihan',
            'slug' => 'jordan-hydro-8-slide-recovery',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'weight_grams' => 600,
            'product_note' => 'Bantalan Solarsoft empuk di bawah telapak kaki untuk meredakan ketegangan setelah bermain basket.',
            'description' => 'Sandal slide premium Jordan dengan tali perekat yang dapat disesuaikan. Menampilkan logo Jumpman timbul elegan untuk gaya santai.',
            'min_price' => 799000,
            'max_price' => 849000,
            'model_price_increment' => 50000,
            'category_ids' => [
                $createdCategories['sandals-slides']->id,
                $createdCategories['slip-on-skate']->id,
                $createdCategories['sneakers-lifestyle']->id,
            ],
            'images' => ['images/shoes/jordan-slides.jpg'],
            'colors' => [
                ['name' => 'Hitam Merah Jumpman', 'hex' => '#000000'],
                ['name' => 'Putih Bersih', 'hex' => '#FFFFFF'],
            ],
            'models' => [
                ['name' => 'Kotak Slide Jordan Resmi', 'description' => 'Kemasan asli pabrik Jordan BNIB'],
            ],
            'sizes' => ['EU 39 (US 6.5)', 'EU 40 (US 7)', 'EU 41 (US 8)', 'EU 42 (US 8.5)', 'EU 43 (US 9.5)', 'EU 44 (US 10)'],
        ]);

        // BRAND 3: ADIDAS
        // Adidas 1: Samba OG
        $createShoeProduct([
            'brand_id' => $createdBrands['adidas']->id,
            'sku' => 'ADS-SMB-008',
            'name' => 'Adidas Samba OG "Cloud White & Core Black"',
            'slug' => 'adidas-samba-og-cloud-white-core-black',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'weight_grams' => 880,
            'product_note' => 'Siluet terrace retro terlaris dengan sol gum cokelat klasik dan kulit asli lembut.',
            'description' => 'Adidas Samba OG lahir di lapangan futsal dan bertransformasi menjadi ikon fashion kasual dunia. Sol karet gum klasik berpadu dengan suede T-toe di bagian ujung kaki.',
            'min_price' => 1649000,
            'max_price' => 1749000,
            'model_price_increment' => 100000,
            'category_ids' => [
                $createdCategories['sneakers-lifestyle']->id,
                $createdCategories['classic-heritage']->id,
                $createdCategories['slip-on-skate']->id,
            ],
            'images' => ['images/shoes/adidas-samba.jpg'],
            'colors' => [
                ['name' => 'Putih & Tiga Garis Hitam', 'hex' => '#000000'],
                ['name' => 'Hitam & Garis Putih', 'hex' => '#111827'],
            ],
            'models' => [
                ['name' => 'Kotak Biru Klasik Adidas Originals', 'description' => 'Kotak orisinal resmi standar pabrik BNIB'],
                ['name' => 'Edisi Spesial Warisan', 'description' => 'Disertai kantung sepatu kanvas tebal'],
            ],
            'sizes' => $fullShoeSizes,
        ]);

        // Adidas 2: Ultraboost
        $createShoeProduct([
            'brand_id' => $createdBrands['adidas']->id,
            'sku' => 'ADS-UB-009',
            'name' => 'Adidas Ultraboost Light Sepatu Lari Ringan',
            'slug' => 'adidas-ultraboost-light-running',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'weight_grams' => 820,
            'product_note' => 'Busa bantalan Light BOOST 30% lebih ringan dengan pengembalian energi kinetik tanpa henti.',
            'description' => 'Sepatu lari performa tinggi yang sangat nyaman untuk lari jarak jauh maupun latihan intensitas tinggi di pusat kebugaran.',
            'min_price' => 2499000,
            'max_price' => 2599000,
            'model_price_increment' => 100000,
            'category_ids' => [
                $createdCategories['running-performance']->id,
                $createdCategories['training-gym']->id,
                $createdCategories['outdoor-trail']->id,
            ],
            'images' => ['images/shoes/adidas-ultraboost.jpg'],
            'colors' => [
                ['name' => 'Putih Inti (Core White)', 'hex' => '#FFFFFF'],
                ['name' => 'Hitam Karbon', 'hex' => '#1F2937'],
            ],
            'models' => [
                ['name' => 'Kotak Performa BOOST', 'description' => 'Kotak resmi Adidas Running BNIB'],
            ],
            'sizes' => $fullShoeSizes,
        ]);

        // Adidas 3: Adilette 22
        $createShoeProduct([
            'brand_id' => $createdBrands['adidas']->id,
            'sku' => 'ADS-ADL-010',
            'name' => 'Adidas Adilette 22 Sandal Selop Futuristik',
            'slug' => 'adidas-adilette-22-slides',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'weight_grams' => 540,
            'product_note' => 'Terinspirasi dari peta topografi 3D penjelajahan planet Mars dengan bahan berbasis tebu alami.',
            'description' => 'Sandal slide dengan garis desain futuristik bergelombang. Sangat nyaman, tahan air, dan memberikan tampilan modern yang mencuri perhatian.',
            'min_price' => 749000,
            'max_price' => 799000,
            'model_price_increment' => 50000,
            'category_ids' => [
                $createdCategories['sandals-slides']->id,
                $createdCategories['slip-on-skate']->id,
                $createdCategories['sneakers-lifestyle']->id,
            ],
            'images' => ['images/shoes/adidas-slides.jpg'],
            'colors' => [
                ['name' => 'Abu Marmer Topografi', 'hex' => '#9CA3AF'],
                ['name' => 'Hitam Karbon', 'hex' => '#111827'],
            ],
            'models' => [
                ['name' => 'Kotak Sandal Asli', 'description' => 'Kemasan resmi Adidas Originals BNIB'],
            ],
            'sizes' => ['EU 38 (US 5.5)', 'EU 39 (US 6.5)', 'EU 40 (US 7)', 'EU 41 (US 8)', 'EU 42 (US 8.5)', 'EU 43 (US 9.5)', 'EU 44 (US 10)'],
        ]);

        // Adidas 4: B2B Grosir
        $pB2B = Product::create([
            'brand_id' => $createdBrands['adidas']->id,
            'sku' => 'ADS-B2B-011',
            'name' => 'Paket Grosir Seri Adidas Samba OG (5 Pasang Ukuran Lengkap)',
            'slug' => 'paket-grosir-seri-adidas-samba-og',
            'product_type' => 'ready_stock',
            'visible_to_umum' => false,
            'visible_to_member' => false,
            'visible_to_distributor' => true,
            'weight_grams' => 4500,
            'product_note' => 'Khusus mitra reseller terdaftar PasarKita. 1 paket berisi 5 pasang lengkap ukuran EU 40-44.',
            'description' => 'Paket pembelian partai reseller untuk siluet Adidas Samba OG terlaris dengan jaminan keaslian 100% dan faktur resmi.',
            'min_price' => 8250000,
            'max_price' => 8250000,
            'status' => 'active',
        ]);
        $pB2B->categories()->attach([$createdCategories['sneakers-lifestyle']->id, $createdCategories['classic-heritage']->id]);
        ProductImage::create(['product_id' => $pB2B->id, 'image_path' => 'images/shoes/adidas-samba.jpg', 'sort_order' => 1, 'is_primary' => true]);
        ProductColor::create(['product_id' => $pB2B->id, 'color_name' => 'Putih Awan & Hitam Inti', 'hex_code' => '#000000', 'sort_order' => 1]);
        $mB2B = ProductModel::create(['product_id' => $pB2B->id, 'model_name' => 'Paket Grosir Seri 5 Pasang', 'description' => '1 paket isi 5 pasang lengkap size run EU 40-44', 'sort_order' => 1]);
        $sB2B = ProductSize::create(['product_id' => $pB2B->id, 'size_label' => 'Paket Seri EU 40-44 (5 Pasang)', 'is_available' => true, 'sort_order' => 1]);
        ProductVariantPrice::create(['product_id' => $pB2B->id, 'product_model_id' => $mB2B->id, 'product_size_id' => $sB2B->id, 'price' => 8250000, 'stock' => 15]);

        // BRAND 4: NEW BALANCE
        // NB 1: 550
        $createShoeProduct([
            'brand_id' => $createdBrands['new-balance']->id,
            'sku' => 'NB-550-012',
            'name' => 'New Balance 550 Retro Basket Vintage',
            'slug' => 'new-balance-550-retro-vintage',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'weight_grams' => 980,
            'product_note' => 'Rancangan ulang dari sepatu basket era 1989 dengan konstruksi kulit premium yang kokoh.',
            'description' => 'Siluet New Balance paling digemari pecinta fesyen kasual. Kulit putih bersih dengan aksen monokrom memberikan nuansa vintage klasik.',
            'min_price' => 1699000,
            'max_price' => 1799000,
            'model_price_increment' => 100000,
            'category_ids' => [
                $createdCategories['sneakers-lifestyle']->id,
                $createdCategories['basketball-court']->id,
                $createdCategories['classic-heritage']->id,
            ],
            'images' => ['images/shoes/nb-550.jpg'],
            'colors' => [
                ['name' => 'Putih & Hitam Inti', 'hex' => '#000000'],
                ['name' => 'Garam Laut Monokrom', 'hex' => '#6B7280'],
            ],
            'models' => [
                ['name' => 'Kotak Eceran Standar', 'description' => 'Kemasan asli New Balance BNIB'],
                ['name' => 'Paket Perawatan Premium', 'description' => 'Termasuk semprotan pelindung kotoran dan tali cadangan'],
            ],
            'sizes' => $fullShoeSizes,
        ]);

        // NB 2: 990v5
        $createShoeProduct([
            'brand_id' => $createdBrands['new-balance']->id,
            'sku' => 'NB-990-013',
            'name' => 'New Balance 990v5 Warisan Legendaris',
            'slug' => 'new-balance-990v5-heritage',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'weight_grams' => 1020,
            'product_note' => 'Teknologi bantalan ENCAP yang memadukan busa empuk dan tepi poliuretan tahan lama.',
            'description' => 'Ikon gaya dad-shoe abadi asal Amerika. Memberikan kenyamanan melangkah kelas atas untuk jalan kaki, olahraga santai, dan penggunaan seharian penuh.',
            'min_price' => 3199000,
            'max_price' => 3299000,
            'model_price_increment' => 100000,
            'category_ids' => [
                $createdCategories['classic-heritage']->id,
                $createdCategories['running-performance']->id,
                $createdCategories['training-gym']->id,
            ],
            'images' => ['images/shoes/nb-990.jpg'],
            'colors' => [
                ['name' => 'Abu-abu Khas New Balance', 'hex' => '#9CA3AF'],
                ['name' => 'Hitam Monokrom', 'hex' => '#111827'],
            ],
            'models' => [
                ['name' => 'Kotak Warisan Orisinal', 'description' => 'Kotak resmi seri 990 BNIB'],
            ],
            'sizes' => ['EU 40 (US 7)', 'EU 41 (US 8)', 'EU 42 (US 8.5)', 'EU 43 (US 9.5)', 'EU 44 (US 10)', 'EU 45 (US 11)'],
        ]);

        // NB 3: Fresh Foam Hierro Trail
        $createShoeProduct([
            'brand_id' => $createdBrands['new-balance']->id,
            'sku' => 'NB-HRO-014',
            'name' => 'New Balance Fresh Foam Hierro Sepatu Luar Ruang & Trail',
            'slug' => 'new-balance-fresh-foam-hierro-trail',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'weight_grams' => 960,
            'product_note' => 'Sol luar Vibram Megagrip bergerigi untuk daya cengkeram tangguh di bebatuan dan tanah terjal.',
            'description' => 'Dirancang untuk para penjelajah alam bebas dan pelari lintas alam. Dilengkapi bantalan Fresh Foam X ekstra empuk dan lapisan pelindung jari kaki Toe Protect.',
            'min_price' => 2199000,
            'max_price' => 2299000,
            'model_price_increment' => 100000,
            'category_ids' => [
                $createdCategories['outdoor-trail']->id,
                $createdCategories['running-performance']->id,
                $createdCategories['training-gym']->id,
            ],
            'images' => ['images/shoes/nb-trail.jpg'],
            'colors' => [
                ['name' => 'Hitam Gunung & Zaitun', 'hex' => '#1F2937'],
            ],
            'models' => [
                ['name' => 'Kotak Ekspedisi Trail', 'description' => 'Kotak resmi New Balance Outdoor BNIB'],
            ],
            'sizes' => ['EU 40 (US 7)', 'EU 41 (US 8)', 'EU 42 (US 8.5)', 'EU 43 (US 9.5)', 'EU 44 (US 10)', 'EU 45 (US 11)'],
        ]);

        // BRAND 5: PUMA
        // Puma 1: Palermo
        $createShoeProduct([
            'brand_id' => $createdBrands['puma']->id,
            'sku' => 'PMA-PLM-015',
            'name' => 'Puma Palermo Klasik Suede Sol Karet',
            'slug' => 'puma-palermo-classic-terrace',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'weight_grams' => 850,
            'product_note' => 'Ikon sepak bola teras Eropa era 80-an dengan label emas Palermo khas di bagian samping.',
            'description' => 'Puma Palermo kembali hadir dari arsip sejarah. Konstruksi sol karet gum lentur berpadu dengan suede halus dan garis formstrip putih kontras yang elegan.',
            'min_price' => 1299000,
            'max_price' => 1399000,
            'model_price_increment' => 100000,
            'category_ids' => [
                $createdCategories['sneakers-lifestyle']->id,
                $createdCategories['classic-heritage']->id,
                $createdCategories['slip-on-skate']->id,
            ],
            'images' => ['images/shoes/puma-palermo.jpg'],
            'colors' => [
                ['name' => 'Hitam Inti & Putih', 'hex' => '#000000'],
                ['name' => 'Putih & Hitam', 'hex' => '#FFFFFF'],
            ],
            'models' => [
                ['name' => 'Kotak Hijau Retro Puma', 'description' => 'Kemasan orisinal resmi Puma BNIB'],
            ],
            'sizes' => $fullShoeSizes,
        ]);

        // Puma 2: Fuse 2.0 Gym
        $createShoeProduct([
            'brand_id' => $createdBrands['puma']->id,
            'sku' => 'PMA-FUS-016',
            'name' => 'Puma Fuse 2.0 Sepatu Latihan Kekuatan & Gym',
            'slug' => 'puma-fuse-strength-gym',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'weight_grams' => 900,
            'product_note' => 'Sol datar tanpa sudut kemiringan (zero drop) untuk stabilitas maksimal saat deadlift dan angkat beban berat.',
            'description' => 'Dirancang bersama atlet ketahanan fisik internasional. Karet sol PUMAGRIP memberikan cengkeraman kokoh di semua permukaan lantai latihan.',
            'min_price' => 1599000,
            'max_price' => 1699000,
            'model_price_increment' => 100000,
            'category_ids' => [
                $createdCategories['training-gym']->id,
                $createdCategories['outdoor-trail']->id,
                $createdCategories['running-performance']->id,
            ],
            'images' => ['images/shoes/puma-gym.jpg'],
            'colors' => [
                ['name' => 'Hitam Siluman (Stealth Black)', 'hex' => '#111827'],
            ],
            'models' => [
                ['name' => 'Kotak Pelatihan Gym', 'description' => 'Kotak orisinal resmi Puma Training BNIB'],
            ],
            'sizes' => ['EU 40 (US 7)', 'EU 41 (US 8)', 'EU 42 (US 8.5)', 'EU 43 (US 9.5)', 'EU 44 (US 10)', 'EU 45 (US 11)'],
        ]);

        // BRAND 6: ASICS
        // Asics 1: GEL-Kayano 14
        $createShoeProduct([
            'brand_id' => $createdBrands['asics']->id,
            'sku' => 'ASC-KYN-017',
            'name' => 'Asics GEL-Kayano 14 Perak Metalik & Hitam',
            'slug' => 'asics-gel-kayano-14-metallic-silver',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'weight_grams' => 920,
            'product_note' => 'Teknologi bantalan GEL ikonis pada tumit dan telapak depan untuk peredaman benturan optimal.',
            'description' => 'Mewakili tren lari retro akhir tahun 2000-an. Aksen jaring perak metalik mengilap berpadu garis hitam pekat memberikan tampilan techwear futuristik yang sangat populer.',
            'min_price' => 2299000,
            'max_price' => 2449000,
            'model_price_increment' => 150000,
            'category_ids' => [
                $createdCategories['running-performance']->id,
                $createdCategories['training-gym']->id,
                $createdCategories['sneakers-lifestyle']->id,
            ],
            'images' => ['images/shoes/asics-kayano.jpg'],
            'colors' => [
                ['name' => 'Perak Metalik & Hitam', 'hex' => '#111827'],
                ['name' => 'Putih Murni & Abu Asap', 'hex' => '#9CA3AF'],
            ],
            'models' => [
                ['name' => 'Kotak Performa Lari GEL', 'description' => 'Kotak orisinal resmi Asics Sportstyle BNIB'],
            ],
            'sizes' => $fullShoeSizes,
        ]);

        // Asics 2: Trabuco Trail
        $createShoeProduct([
            'brand_id' => $createdBrands['asics']->id,
            'sku' => 'ASC-TRB-018',
            'name' => 'Asics GEL-Trabuco Terra Sepatu Lari Alam Bebas & Trail',
            'slug' => 'asics-gel-trabuco-terra-trail',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'weight_grams' => 940,
            'product_note' => 'Pola tapak sol multi-arah untuk kemampuan menanjak dan menuruni medan tanah licin dengan stabil.',
            'description' => 'Sepatu andalan untuk olahraga lintas alam, pendakian ringan, dan lari trail. Busa bantalan FLYTEFOAM berbobot ringan menyerap setiap guncangan tanah berbatu.',
            'min_price' => 1799000,
            'max_price' => 1899000,
            'model_price_increment' => 100000,
            'category_ids' => [
                $createdCategories['outdoor-trail']->id,
                $createdCategories['running-performance']->id,
                $createdCategories['training-gym']->id,
            ],
            'images' => ['images/shoes/asics-trail.jpg'],
            'colors' => [
                ['name' => 'Hitam Hutan & Jeruk', 'hex' => '#1F2937'],
            ],
            'models' => [
                ['name' => 'Kotak Trail Asics Resmi', 'description' => 'Kotak orisinal resmi Asics Trail BNIB'],
            ],
            'sizes' => ['EU 40 (US 7)', 'EU 41 (US 8)', 'EU 42 (US 8.5)', 'EU 43 (US 9.5)', 'EU 44 (US 10)', 'EU 45 (US 11)'],
        ]);

        // BRAND 7: CONVERSE
        // Converse 1: Chuck 70
        $createShoeProduct([
            'brand_id' => $createdBrands['converse']->id,
            'sku' => 'CNV-C70-019',
            'name' => 'Converse Chuck 70 Vintage High Kanvas Hitam',
            'slug' => 'converse-chuck-70-vintage-high-black',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'weight_grams' => 890,
            'product_note' => 'Kanvas katun 12oz tebal dengan sol tengah mengilap dan bantalan insole OrthoLite super empuk.',
            'description' => 'Rancangan klasik abadi era 1970-an. Jahitan sayap pada lidah sepatu, plat lisensi vintage di tumit, dan sol karet kokoh menjadikannya sneaker wajib semua kalangan.',
            'min_price' => 999000,
            'max_price' => 1099000,
            'model_price_increment' => 100000,
            'category_ids' => [
                $createdCategories['classic-heritage']->id,
                $createdCategories['sneakers-lifestyle']->id,
                $createdCategories['basketball-court']->id,
            ],
            'images' => ['images/shoes/converse-chuck70.jpg'],
            'colors' => [
                ['name' => 'Kanvas Hitam & Sol Krem', 'hex' => '#000000'],
                ['name' => 'Putih Optik', 'hex' => '#FFFFFF'],
            ],
            'models' => [
                ['name' => 'Kotak Merah Warisan 70s', 'description' => 'Kotak merah resmi Converse 70s BNIB'],
            ],
            'sizes' => $fullShoeSizes,
        ]);

        // Converse 2: One Star Pro Skate
        $createShoeProduct([
            'brand_id' => $createdBrands['converse']->id,
            'sku' => 'CNV-1ST-020',
            'name' => 'Converse One Star Pro Suede Sepatu Skate',
            'slug' => 'converse-one-star-pro-suede-skate',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'weight_grams' => 870,
            'product_note' => 'Insole busa CX peredam benturan papan skate dengan bahan suede tahan gesekan tinggi.',
            'description' => 'Siluet bintang tunggal ikonis era 90-an yang disempurnakan untuk kultur skateboard. Sol luar karet CONS memberikan traksi papan terbaik.',
            'min_price' => 1199000,
            'max_price' => 1299000,
            'model_price_increment' => 100000,
            'category_ids' => [
                $createdCategories['slip-on-skate']->id,
                $createdCategories['sneakers-lifestyle']->id,
                $createdCategories['classic-heritage']->id,
            ],
            'images' => ['images/shoes/converse-onestar.jpg'],
            'colors' => [
                ['name' => 'Suede Hitam Bintang Putih', 'hex' => '#000000'],
            ],
            'models' => [
                ['name' => 'Kotak Skate Resmi CONS', 'description' => 'Kotak orisinal resmi Converse CONS BNIB'],
            ],
            'sizes' => $fullShoeSizes,
        ]);

        // BRAND 8: VANS
        // Vans 1: Old Skool
        $createShoeProduct([
            'brand_id' => $createdBrands['vans']->id,
            'sku' => 'VNS-OSK-021',
            'name' => 'Vans Old Skool Klasik Kanvas & Suede Hitam Putih',
            'slug' => 'vans-old-skool-classic-black-white',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'weight_grams' => 840,
            'product_note' => 'Garis jazz sidestripe legendaris dengan sol wafel karet vulkanisir yang kokoh dan tahan lama.',
            'description' => 'Sepatu pertama Vans yang memperkenalkan garis samping ikonik. Terbuat dari perpaduan suede tahan abrasi dan kanvas tebal, sangat nyaman dipakai bermain skate maupun gaya kasual.',
            'min_price' => 899000,
            'max_price' => 999000,
            'model_price_increment' => 100000,
            'category_ids' => [
                $createdCategories['slip-on-skate']->id,
                $createdCategories['sneakers-lifestyle']->id,
                $createdCategories['training-gym']->id,
            ],
            'images' => ['images/shoes/vans-oldskool.jpg'],
            'colors' => [
                ['name' => 'Hitam & Garis Putih', 'hex' => '#000000'],
                ['name' => 'Putih Kanvas Penuh', 'hex' => '#FFFFFF'],
            ],
            'models' => [
                ['name' => 'Kotak Merah Klasik Vans', 'description' => 'Kotak orisinal resmi Vans BNIB'],
                ['name' => 'Edisi Skate Pro Bantalan Busa', 'description' => 'Dilengkapi insole PopCush ekstra empuk'],
            ],
            'sizes' => $fullShoeSizes,
        ]);

        // Vans 2: Classic Slip-On
        $createShoeProduct([
            'brand_id' => $createdBrands['vans']->id,
            'sku' => 'VNS-SLP-022',
            'name' => 'Vans Classic Slip-On Papan Catur (Checkerboard)',
            'slug' => 'vans-classic-slip-on-checkerboard',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'weight_grams' => 780,
            'product_note' => 'Model tanpa tali praktis langsung pakai dengan kerah berbusa lembut dan sol wafel karet khas.',
            'description' => 'Motif kotak-kotak papan catur hitam putih paling legendaris dari California. Praktis dipakai dan menjadi simbol gaya santai skate di seluruh dunia.',
            'min_price' => 849000,
            'max_price' => 949000,
            'model_price_increment' => 100000,
            'category_ids' => [
                $createdCategories['slip-on-skate']->id,
                $createdCategories['sandals-slides']->id,
                $createdCategories['sneakers-lifestyle']->id,
            ],
            'images' => ['images/shoes/vans-slipon.jpg'],
            'colors' => [
                ['name' => 'Motif Papan Catur Hitam Putih', 'hex' => '#000000'],
                ['name' => 'Hitam Polos', 'hex' => '#111827'],
            ],
            'models' => [
                ['name' => 'Kotak Standar Vans Asli', 'description' => 'Kemasan resmi Vans BNIB'],
            ],
            'sizes' => $fullShoeSizes,
        ]);
    }
}
