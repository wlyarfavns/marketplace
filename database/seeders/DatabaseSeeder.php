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
                'description' => 'Sub-brand legendaris Michael Jordan yang mendefinisikan kultur sneaker basket dunia lewat seri Retro dan Court.',
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
                'description' => 'Kenyamanan legendaris asal Boston dengan siluet retro-runner 550, Fresh Foam 1080, dan trail runner 610v1.',
                'show_on_homepage' => true,
                'homepage_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Puma',
                'slug' => 'puma',
                'dp_percentage' => 25.00,
                'description' => 'Desain klasik retro motorsport dan gym training lewat rilisan Palermo, Fuse Trainer, dan Leadcat Slides.',
                'show_on_homepage' => true,
                'homepage_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Asics',
                'slug' => 'asics',
                'dp_percentage' => 30.00,
                'description' => 'Performa lari dan Japanese techwear dengan teknologi bantalan GEL andalan serta lini outdoor trail Sonoma.',
                'show_on_homepage' => true,
                'homepage_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Converse',
                'slug' => 'converse',
                'dp_percentage' => 25.00,
                'description' => 'Sepatu kanvas ikonik Chuck Taylor All Star 70s dan One Star Pro Skate yang timeless dari generasi ke generasi.',
                'show_on_homepage' => true,
                'homepage_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Vans',
                'slug' => 'vans',
                'dp_percentage' => 25.00,
                'description' => 'Kultur skate legendaris asal California lewat siluet Old Skool, Classic Slip-On, dan lini petualang UltraRange.',
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
            ['name' => 'Sneakers & Lifestyle', 'slug' => 'sneakers-lifestyle', 'type' => 'unisex', 'description' => 'Koleksi sneakers kasual dan street style untuk gaya harian modern.'],
            ['name' => 'Running & Performance', 'slug' => 'running-performance', 'type' => 'unisex', 'description' => 'Sepatu lari berteknologi bantalan responsif untuk aktivitas tinggi.'],
            ['name' => 'Basketball & Court', 'slug' => 'basketball-court', 'type' => 'pria', 'description' => 'Koleksi sepatu basket ikonik dan court performance berdesain kokoh.'],
            ['name' => 'Classic Heritage Retro', 'slug' => 'classic-heritage', 'type' => 'unisex', 'description' => 'Siluet vintage timeless yang menjadi legenda mode sepanjang masa.'],
            ['name' => 'Slip-On & Skate', 'slug' => 'slip-on-skate', 'type' => 'unisex', 'description' => 'Model kasual praktis dan sepatu skate bersol karet vulkanisir kuat.'],
            ['name' => 'Sandals & Slides', 'slug' => 'sandals-slides', 'type' => 'unisex', 'description' => 'Sandal slide santai berbobot ringan untuk kenyamanan maksimal.'],
            ['name' => 'Training & Gym', 'slug' => 'training-gym', 'type' => 'unisex', 'description' => 'Sepatu fitness, cross-training, dan latihan gym dengan stabilitas pijakan kuat.'],
            ['name' => 'Outdoor & Trail', 'slug' => 'outdoor-trail', 'type' => 'unisex', 'description' => 'Sepatu kegiatan luar ruang dengan traksi sol tahan banting di segala medan.'],
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
        AdminHandle::create(['code' => 'ADM-01', 'name' => 'Fauzan (Sneaker Authenticator & Ops)', 'is_active' => true]);
        AdminHandle::create(['code' => 'ADM-02', 'name' => 'Nadia (Finance & DP Verifier)', 'is_active' => true]);

        // 8. Data Keunggulan (Features)
        $features = [
            ['title' => '100% Original Authentic Guaranteed', 'description' => 'Jaminan keaslian 100% uang kembali untuk seluruh koleksi sepatu branded resmi di PasarKita.', 'sort_order' => 1],
            ['title' => 'Skema Pre-Order DP Terpercaya', 'description' => 'Pesan sneaker rilisan terbatas cukup dengan Down Payment (DP) mulai 25%, pelunasan saat sepatu siap kirim.', 'sort_order' => 2],
            ['title' => 'Tier Member & Diskon Grosir Reseller', 'description' => 'Dapatkan penawaran harga terbaik untuk reseller dan komunitas sneakerhead dengan aturan diskon otomatis.', 'sort_order' => 3],
            ['title' => 'Double-Box Packing & Fast Delivery', 'description' => 'Setiap pasang sepatu dikemas menggunakan kardus double-box tebal dan bubble wrap untuk menjaga keutuhan box original.', 'sort_order' => 4],
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

        // 10. Data Banners Promo (Menggunakan File SVG Lokal Cepat & Ringan)
        Banner::create([
            'title' => 'Official Drop: Branded Sneakers Collection',
            'description' => 'Koleksi rilisan terbaru dari Nike, Jordan, Adidas, hingga New Balance dengan jaminan 100% original bergaransi PasarKita.',
            'image_path' => 'images/banners/banner-1.svg',
            'url' => '/katalog',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        Banner::create([
            'title' => 'Pre-Order Rilisan Langka & Edisi Terbatas',
            'description' => 'Amankan siluet sneaker langka dan size favoritmu dengan skema DP mulai 25% khusus Member & Mitra PasarKita.',
            'image_path' => 'images/banners/banner-2.svg',
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

            // Foto Produk (Lokal SVG Ringan)
            if (! empty($cfg['images'])) {
                foreach ($cfg['images'] as $idx => $img) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $img['url'],
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

        // 12. BUAT KATALOG LENGKAP: SETIAP BRAND DAN SETIAP KATEGORI MEMILIKI PRODUK

        // ─────────── 1. NIKE ───────────
        // Nike 1: Dunk Low Retro Panda (Sneakers & Lifestyle, Classic Heritage)
        $createShoeProduct([
            'brand_id' => $createdBrands['nike']->id,
            'sku' => 'NKE-DNK-001',
            'name' => 'Nike Dunk Low Retro "Panda" White Black',
            'slug' => 'nike-dunk-low-retro-panda',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'visible_to_member' => true,
            'visible_to_distributor' => true,
            'weight_grams' => 950,
            'product_note' => '100% Brand New In Box (BNIB) Original Guarantee. Dilengkapi tag resmi & double-box packing aman.',
            'description' => 'Siluet sneaker paling populer dan versatile di dunia. Menggunakan material leather berkualitas tinggi dengan perpaduan warna kontras hitam dan putih yang ikonik, cocok dipadukan dengan berbagai outfit kasual dan streetwear harian.',
            'min_price' => 1549000,
            'max_price' => 1649000,
            'model_price_increment' => 100000,
            'category_ids' => [$createdCategories['sneakers-lifestyle']->id, $createdCategories['classic-heritage']->id],
            'images' => [['url' => 'images/shoes/nike-dunk-panda.svg']],
            'colors' => [
                ['name' => 'White & Black (Panda)', 'hex' => '#000000'],
                ['name' => 'Triple White', 'hex' => '#FFFFFF'],
                ['name' => 'Monochrome Stealth Black', 'hex' => '#111827'],
            ],
            'models' => [
                ['name' => 'Standard OG Box', 'description' => 'Box original resmi standar Nike BNIB'],
                ['name' => 'Collector Edition', 'description' => 'Box original BNIB + Acrylic Sneaker Case tebal'],
            ],
            'sizes' => $fullShoeSizes,
            'bonus' => [
                'name' => 'Premium Sneaker Cleaner & Crep Wipe',
                'description' => 'Gratis 1 kit pembersih sneaker premium setiap pembelian min 2 pasang sepatu.',
                'min_qty' => 2,
            ],
        ]);

        // Nike 2: Metcon 9 Training & Gym (Training & Gym, Running & Performance)
        $createShoeProduct([
            'brand_id' => $createdBrands['nike']->id,
            'sku' => 'NKE-MTC-002',
            'name' => 'Nike Metcon 9 "Black White" Training & Gym',
            'slug' => 'nike-metcon-9-training-gym',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'visible_to_member' => true,
            'visible_to_distributor' => true,
            'weight_grams' => 980,
            'product_note' => 'Sepatu standar emas latihan beban dan fitness gym dengan pelat Hyperlift kokoh.',
            'description' => 'Dirancang untuk mengangkat beban berat, sesi CrossFit, dan latihan intensif. Dilengkapi pelat Hyperlift lebih besar dan pembungkus tali karet yang memberikan kestabilan maksimal saat melakukan squat dan deadlift.',
            'min_price' => 2099000,
            'max_price' => 2199000,
            'category_ids' => [$createdCategories['training-gym']->id, $createdCategories['running-performance']->id],
            'images' => [['url' => 'images/shoes/nike-metcon-training.svg']],
            'colors' => [
                ['name' => 'Black & White Anthracite', 'hex' => '#111827'],
                ['name' => 'Triple White Metallic', 'hex' => '#FFFFFF'],
            ],
            'models' => [
                ['name' => 'Standard Training Box', 'description' => 'Kemasan ritel resmi BNIB'],
            ],
            'sizes' => $fullShoeSizes,
        ]);

        // Nike 3: Air Zoom Pegasus 40 (Running & Performance, Training & Gym)
        $createShoeProduct([
            'brand_id' => $createdBrands['nike']->id,
            'sku' => 'NKE-PEG-003',
            'name' => 'Nike Air Zoom Pegasus 40 "Monochrome Runner"',
            'slug' => 'nike-air-zoom-pegasus-40-runner',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'category_ids' => [$createdCategories['running-performance']->id, $createdCategories['training-gym']->id, $createdCategories['sneakers-lifestyle']->id],
            'images' => [['url' => 'images/shoes/nike-pegasus-runner.svg']],
            'min_price' => 1849000,
            'max_price' => 1949000,
            'description' => 'Pionir sepatu lari legendaris dengan unit ganda Zoom Air dan busa React responsif.',
            'product_note' => 'Kenyamanan lari jarak jauh dengan upper engineered mesh yang breathable.',
            'colors' => [
                ['name' => 'Pure Platinum Black', 'hex' => '#111827'],
                ['name' => 'Triple White', 'hex' => '#FFFFFF'],
            ],
            'models' => [['name' => 'Standard Nike Running Box', 'description' => 'Box orisinal Nike Running']],
            'sizes' => $fullShoeSizes,
        ]);

        // Nike 4: Pegasus Trail 4 GORE-TEX (Outdoor & Trail)
        $createShoeProduct([
            'brand_id' => $createdBrands['nike']->id,
            'sku' => 'NKE-TRL-004',
            'name' => 'Nike Pegasus Trail 4 GORE-TEX Waterproof',
            'slug' => 'nike-pegasus-trail-4-gore-tex',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'category_ids' => [$createdCategories['outdoor-trail']->id, $createdCategories['running-performance']->id],
            'images' => [['url' => 'images/shoes/nike-pegasus-trail.svg']],
            'min_price' => 2499000,
            'max_price' => 2599000,
            'description' => 'Sepatu trail running tahan air berteknologi membran GORE-TEX dengan traksi sol multiguna tahan medan berbatu.',
            'product_note' => 'Tahan air hujan dan lumpur dengan outsole berdaya cengkeram tinggi.',
            'colors' => [
                ['name' => 'Stealth Black Charcoal', 'hex' => '#111827'],
            ],
            'models' => [['name' => 'Standard Trail Box', 'description' => 'Box resmi Nike Trail']],
            'sizes' => $fullShoeSizes,
        ]);

        // Nike 5: Calm Slide (Sandals & Slides)
        $createShoeProduct([
            'brand_id' => $createdBrands['nike']->id,
            'sku' => 'NKE-SLD-005',
            'name' => 'Nike Calm Slide "Triple Black" Recovery',
            'slug' => 'nike-calm-slide-triple-black',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'category_ids' => [$createdCategories['sandals-slides']->id, $createdCategories['sneakers-lifestyle']->id],
            'images' => [['url' => 'images/shoes/nike-calm-slide.svg']],
            'min_price' => 749000,
            'max_price' => 749000,
            'description' => 'Sandal slide monolitik berbahan busa responsif kedap air dengan pijakan kaki bertekstur anti-selip.',
            'product_note' => 'Busa lembut premium untuk pemulihan kaki setelah berolahraga.',
            'colors' => [
                ['name' => 'Triple Black', 'hex' => '#000000'],
                ['name' => 'Pure Sail White', 'hex' => '#F4F1EA'],
            ],
            'models' => [['name' => 'Standard Slide Box', 'description' => 'Box slide resmi Nike']],
            'sizes' => ['EU 39 (US 6.5)', 'EU 40 (US 7)', 'EU 41 (US 8)', 'EU 42 (US 8.5)', 'EU 43 (US 9.5)', 'EU 44 (US 10)'],
        ]);

        // ─────────── 2. AIR JORDAN ───────────
        // Jordan 1: Retro High OG Chicago (Basketball & Court, Classic Heritage, Pre-Order)
        $createShoeProduct([
            'brand_id' => $createdBrands['air-jordan']->id,
            'sku' => 'JDN-AJ1-002',
            'name' => 'Air Jordan 1 Retro High OG "Chicago Lost & Found"',
            'slug' => 'air-jordan-1-retro-high-og-chicago',
            'product_type' => 'po',
            'visible_to_umum' => false, // Khusus Member & Distributor
            'visible_to_member' => true,
            'visible_to_distributor' => true,
            'weight_grams' => 1100,
            'product_note' => 'Estimasi pengiriman 10-14 hari kerja setelah batch ditutup. Pembayaran dapat menggunakan DP 35%. Jaminan 100% Original BNIB.',
            'description' => 'Edisi legendaris Air Jordan 1 High OG Chicago Lost & Found terinspirasi dari siluet orisinal 1985 dengan aksen vintage cracked leather dan detail box retro. Dilengkapi bantalan udara Nike Air terenkapsulasi.',
            'min_price' => 2899000,
            'max_price' => 3099000,
            'model_price_increment' => 200000,
            'category_ids' => [$createdCategories['basketball-court']->id, $createdCategories['classic-heritage']->id, $createdCategories['sneakers-lifestyle']->id],
            'images' => [['url' => 'images/shoes/jordan-1-chicago.svg']],
            'colors' => [
                ['name' => 'Chicago Vintage Red & Black', 'hex' => '#B91C1C'],
                ['name' => 'Shadow Stealth Grey', 'hex' => '#4B5563'],
                ['name' => 'Pure Monochrome Black', 'hex' => '#111827'],
            ],
            'models' => [
                ['name' => 'Standard High Cut OG', 'description' => 'Sepatu BNIB lengkap tali bawaan OG'],
                ['name' => 'Collector Vault Edition', 'description' => 'Termasuk extra replacement laces 3 warna & shoe tree kayu'],
            ],
            'sizes' => ['EU 40 (US 7)', 'EU 41 (US 8)', 'EU 42 (US 8.5)', 'EU 43 (US 9.5)', 'EU 44 (US 10)', 'EU 45 (US 11)'],
            'price_rule' => [
                'type' => 'cut',
                'amount' => 75000,
                'description' => 'Potongan otomatis Rp 75.000 per pasang khusus order reseller/distributor PasarKita',
            ],
        ]);

        // Jordan 2: Air Jordan 4 Retro Military Black (Basketball & Court, Pre-Order)
        $createShoeProduct([
            'brand_id' => $createdBrands['air-jordan']->id,
            'sku' => 'JDN-AJ4-009',
            'name' => 'Air Jordan 4 Retro "Military Black" (Batch PO)',
            'slug' => 'air-jordan-4-retro-military-black-po',
            'product_type' => 'po',
            'visible_to_umum' => false,
            'visible_to_member' => true,
            'visible_to_distributor' => true,
            'weight_grams' => 1250,
            'product_note' => 'Rilisan paling dicari dengan blocking warna legendaris Military Blue yang digantikan aksen hitam premium.',
            'description' => 'Air Jordan 4 Military Black mengadopsi struktur color blocking klasik dengan material kulit putih berbutir halus, suede abu-abu di bagian mudguard, dan sayap tali penyangga khas.',
            'min_price' => 3499000,
            'max_price' => 3699000,
            'model_price_increment' => 200000,
            'category_ids' => [$createdCategories['basketball-court']->id, $createdCategories['sneakers-lifestyle']->id],
            'images' => [['url' => 'images/shoes/jordan-4-military.svg']],
            'colors' => [
                ['name' => 'White & Neutral Grey Black', 'hex' => '#000000'],
                ['name' => 'Pure Stealth Black', 'hex' => '#111827'],
            ],
            'models' => [
                ['name' => 'Standard Flight Box', 'description' => 'Box retro bercorak marmer Jordan 4 BNIB'],
                ['name' => 'OG Vault Flight Case', 'description' => 'Disertai hard case pelindung sneaker & tag Jumpman akrilik'],
            ],
            'sizes' => ['EU 40 (US 7)', 'EU 41 (US 8)', 'EU 42 (US 8.5)', 'EU 43 (US 9.5)', 'EU 44 (US 10)', 'EU 45 (US 11)'],
        ]);

        // Jordan 3: Jordan Luka 2 Court Performance (Basketball & Court, Training & Gym)
        $createShoeProduct([
            'brand_id' => $createdBrands['air-jordan']->id,
            'sku' => 'JDN-LUK-010',
            'name' => 'Jordan Luka 2 "Black White" Court Performance',
            'slug' => 'jordan-luka-2-court-performance',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'category_ids' => [$createdCategories['basketball-court']->id, $createdCategories['training-gym']->id],
            'images' => [['url' => 'images/shoes/jordan-luka-court.svg']],
            'min_price' => 1999000,
            'max_price' => 1999000,
            'description' => 'Didesain untuk kelincahan step-back Luka Dončić dengan sangkar pembungkus 360 derajat untuk kontrol court maksimal.',
            'product_note' => 'Bantalan busa Formula 23 yang padat dan responsif di lapangan.',
            'colors' => [['name' => 'Monochrome Court Black', 'hex' => '#111827']],
            'models' => [['name' => 'Standard Court Box', 'description' => 'Box resmi Jordan Basketball']],
            'sizes' => $fullShoeSizes,
        ]);

        // Jordan 4: Jordan Sophia Slide (Sandals & Slides)
        $createShoeProduct([
            'brand_id' => $createdBrands['air-jordan']->id,
            'sku' => 'JDN-SLD-011',
            'name' => 'Jordan Sophia Luxury Platform Slide',
            'slug' => 'jordan-sophia-luxury-platform-slide',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'category_ids' => [$createdCategories['sandals-slides']->id, $createdCategories['sneakers-lifestyle']->id],
            'images' => [['url' => 'images/shoes/jordan-sophia-slide.svg']],
            'min_price' => 899000,
            'max_price' => 899000,
            'description' => 'Sandal slide mewah beraksen tali silang empuk dan sol tebal dengan teknologi Air terenkapsulasi.',
            'product_note' => 'Platform empuk berlapis material premium tahan lama.',
            'colors' => [['name' => 'Triple Black Velvet', 'hex' => '#111827']],
            'models' => [['name' => 'Jordan Slide Box', 'description' => 'Box slide resmi']],
            'sizes' => ['EU 38 (US 5.5)', 'EU 39 (US 6.5)', 'EU 40 (US 7)', 'EU 41 (US 8)', 'EU 42 (US 8.5)'],
        ]);

        // ─────────── 3. ADIDAS ───────────
        // Adidas 1: Samba OG (Sneakers & Lifestyle, Classic Heritage)
        $createShoeProduct([
            'brand_id' => $createdBrands['adidas']->id,
            'sku' => 'ADS-SMB-003',
            'name' => 'Adidas Samba OG "Cloud White & Core Black"',
            'slug' => 'adidas-samba-og-cloud-white-core-black',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'category_ids' => [$createdCategories['sneakers-lifestyle']->id, $createdCategories['classic-heritage']->id],
            'images' => [['url' => 'images/shoes/adidas-samba-white.svg']],
            'min_price' => 1649000,
            'max_price' => 1749000,
            'model_price_increment' => 100000,
            'description' => 'Adidas Samba OG lahir di lapangan sepak bola dalam ruangan dan kini menjadi ikon street-style dunia yang tak terbendung.',
            'product_note' => 'Siluet terrace retro terlaris dengan sol gum cokelat klasik dan upper leather premium lembut.',
            'colors' => [
                ['name' => 'Cloud White & Core Black', 'hex' => '#000000'],
                ['name' => 'Core Black & White', 'hex' => '#111827'],
            ],
            'models' => [
                ['name' => 'Standard Classic Box', 'description' => 'Box original biru klasik Adidas Originals BNIB'],
                ['name' => 'Heritage Special Edition', 'description' => 'Termasuk dust bag kanvas & gantungan kunci akrilik'],
            ],
            'sizes' => $fullShoeSizes,
        ]);

        // Adidas 2: Ultraboost Light (Running & Performance, Training & Gym)
        $createShoeProduct([
            'brand_id' => $createdBrands['adidas']->id,
            'sku' => 'ADS-UB-012',
            'name' => 'Adidas Ultraboost Light "Core Black" Runner',
            'slug' => 'adidas-ultraboost-light-runner',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'category_ids' => [$createdCategories['running-performance']->id, $createdCategories['training-gym']->id],
            'images' => [['url' => 'images/shoes/adidas-ultraboost.svg']],
            'min_price' => 2899000,
            'max_price' => 2899000,
            'description' => 'Bantalan BOOST 30% lebih ringan dengan pengembalian energi kinetik legendaris di setiap langkah kaki.',
            'product_note' => 'Sol luar Continental Rubber untuk cengkeraman maksimal di aspal basah dan kering.',
            'colors' => [['name' => 'Core Black Triple', 'hex' => '#111827']],
            'models' => [['name' => 'Performance Boost Box', 'description' => 'Box resmi Adidas Running']],
            'sizes' => $fullShoeSizes,
        ]);

        // Adidas 3: Adilette 22 Slides (Sandals & Slides)
        $createShoeProduct([
            'brand_id' => $createdBrands['adidas']->id,
            'sku' => 'ADS-ADI-013',
            'name' => 'Adidas Adilette 22 Futuristic Comfort Slides',
            'slug' => 'adidas-adilette-22-comfort-slides',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'category_ids' => [$createdCategories['sandals-slides']->id, $createdCategories['sneakers-lifestyle']->id],
            'images' => [['url' => 'images/shoes/adidas-adilette-slide.svg']],
            'min_price' => 699000,
            'max_price' => 699000,
            'description' => 'Terinspirasi oleh ekspedisi luar angkasa dan topografi 3D martian, sandal slide ini terbuat dari bahan bio-based sugarcane EVA.',
            'product_note' => 'Ringan, futuristik, dan sangat empuk untuk santai harian.',
            'colors' => [
                ['name' => 'Stardust White Charcoal', 'hex' => '#E5E7EB'],
                ['name' => 'Carbon Black', 'hex' => '#111827'],
            ],
            'models' => [['name' => 'Adilette 22 Box', 'description' => 'Box resmi']],
            'sizes' => ['EU 39 (US 6.5)', 'EU 40 (US 7)', 'EU 41 (US 8)', 'EU 42 (US 8.5)', 'EU 43 (US 9.5)', 'EU 44 (US 10)'],
        ]);

        // Adidas 4: Terrex Free Hiker 2 (Outdoor & Trail)
        $createShoeProduct([
            'brand_id' => $createdBrands['adidas']->id,
            'sku' => 'ADS-TRX-014',
            'name' => 'Adidas Terrex Free Hiker 2 GORE-TEX Trail',
            'slug' => 'adidas-terrex-free-hiker-2-trail',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'category_ids' => [$createdCategories['outdoor-trail']->id, $createdCategories['running-performance']->id],
            'images' => [['url' => 'images/shoes/adidas-terrex-trail.svg']],
            'min_price' => 3199000,
            'max_price' => 3199000,
            'description' => 'Perpaduan kenyamanan bantalan BOOST lari dengan ketangguhan sepatu gunung waterproof GORE-TEX.',
            'product_note' => 'Karet Continental kuat untuk menjelajah rute alam dan medan berbatu.',
            'colors' => [['name' => 'Stealth Trail Grey', 'hex' => '#374151']],
            'models' => [['name' => 'Terrex Outdoor Box', 'description' => 'Box resmi Terrex']],
            'sizes' => $fullShoeSizes,
        ]);

        // Adidas 5: Busenitz Pro Skate (Slip-On & Skate)
        $createShoeProduct([
            'brand_id' => $createdBrands['adidas']->id,
            'sku' => 'ADS-BSZ-015',
            'name' => 'Adidas Busenitz Pro Suede Skate Edition',
            'slug' => 'adidas-busenitz-pro-skate-edition',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'category_ids' => [$createdCategories['slip-on-skate']->id, $createdCategories['sneakers-lifestyle']->id],
            'images' => [['url' => 'images/shoes/adidas-busenitz-skate.svg']],
            'min_price' => 1399000,
            'max_price' => 1399000,
            'description' => 'Sepatu skateboarding pro signature Dennis Busenitz dengan kerah Geofit anatomis dan sol cupsole kokoh.',
            'product_note' => 'Upper suede tahan gesekan papan skate dengan cengkeraman maksimal.',
            'colors' => [['name' => 'Core Black & Gum Gold', 'hex' => '#111827']],
            'models' => [['name' => 'Skateboarding Box', 'description' => 'Box resmi Adidas Skate']],
            'sizes' => $fullShoeSizes,
        ]);

        // ─────────── 4. NEW BALANCE ───────────
        // NB 1: 550 White Sea Salt (Sneakers & Lifestyle, Basketball & Court, Classic Heritage)
        $createShoeProduct([
            'brand_id' => $createdBrands['new-balance']->id,
            'sku' => 'NB-550-004',
            'name' => 'New Balance 550 "White & Sea Salt Black" Classic',
            'slug' => 'new-balance-550-white-sea-salt-black',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'category_ids' => [$createdCategories['sneakers-lifestyle']->id, $createdCategories['basketball-court']->id, $createdCategories['classic-heritage']->id],
            'images' => [['url' => 'images/shoes/nb-550-seasalt.svg']],
            'min_price' => 1699000,
            'max_price' => 1799000,
            'model_price_increment' => 100000,
            'description' => 'Siluet legendaris dari New Balance yang menjadi favorit pecinta mode kasual retro basketball 1989.',
            'product_note' => 'Siluet retro basketball vintage 1989. Menggunakan full leather upper premium dengan bantalan nyaman.',
            'colors' => [
                ['name' => 'White & Core Black', 'hex' => '#000000'],
                ['name' => 'Sea Salt & Monochrome Grey', 'hex' => '#6B7280'],
            ],
            'models' => [
                ['name' => 'Standard Retail Box', 'description' => 'Sepatu original BNIB dengan lace bawaan'],
                ['name' => 'Premium Care Bundle', 'description' => 'Termasuk extra vintage laces & spray pelindung noda'],
            ],
            'sizes' => $fullShoeSizes,
        ]);

        // NB 2: Fresh Foam 1080v13 (Running & Performance, Training & Gym)
        $createShoeProduct([
            'brand_id' => $createdBrands['new-balance']->id,
            'sku' => 'NB-108-016',
            'name' => 'New Balance Fresh Foam X 1080v13 Runner',
            'slug' => 'new-balance-fresh-foam-1080v13-runner',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'category_ids' => [$createdCategories['running-performance']->id, $createdCategories['training-gym']->id],
            'images' => [['url' => 'images/shoes/nb-1080-running.svg']],
            'min_price' => 2499000,
            'max_price' => 2499000,
            'description' => 'Puncak kenyamanan lari harian dengan bantalan Fresh Foam X termutakhir yang empuk dan ringan.',
            'product_note' => 'Cocok untuk lari jarak jauh marathon maupun pemulihan harian.',
            'colors' => [['name' => 'Black & Pure White', 'hex' => '#111827']],
            'models' => [['name' => 'Fresh Foam Performance Box', 'description' => 'Box resmi']],
            'sizes' => $fullShoeSizes,
        ]);

        // NB 3: 610v1 Trail Runner (Outdoor & Trail)
        $createShoeProduct([
            'brand_id' => $createdBrands['new-balance']->id,
            'sku' => 'NB-610-017',
            'name' => 'New Balance 610v1 All-Terrain Trail',
            'slug' => 'new-balance-610v1-trail-runner',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'category_ids' => [$createdCategories['outdoor-trail']->id, $createdCategories['running-performance']->id],
            'images' => [['url' => 'images/shoes/nb-610-trail.svg']],
            'min_price' => 1899000,
            'max_price' => 1899000,
            'description' => 'Estetika trail runner era 2000-an dengan bantalan C-CAP tahan benturan dan sol bergrip agresif.',
            'product_note' => 'Siluet gorpcore populer yang tahan medan bebatuan dan jalanan perkotaan.',
            'colors' => [['name' => 'Charcoal Shadow Grey', 'hex' => '#4B5563']],
            'models' => [['name' => 'All-Terrain Box', 'description' => 'Box resmi']],
            'sizes' => $fullShoeSizes,
        ]);

        // ─────────── 5. PUMA ───────────
        // Puma 1: Palermo Classic (Sneakers & Lifestyle, Classic Heritage)
        $createShoeProduct([
            'brand_id' => $createdBrands['puma']->id,
            'sku' => 'PMA-PLM-005',
            'name' => 'Puma Palermo Classic "Black & Gum White"',
            'slug' => 'puma-palermo-classic-black-gum-white',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'category_ids' => [$createdCategories['sneakers-lifestyle']->id, $createdCategories['classic-heritage']->id],
            'images' => [['url' => 'images/shoes/puma-palermo-black.svg']],
            'min_price' => 1299000,
            'max_price' => 1399000,
            'model_price_increment' => 100000,
            'description' => 'Puma Palermo kembali hadir dari arsip legendaris era 80-an dengan konstruksi sol luar gum karet dan upper suede premium.',
            'product_note' => 'Sepatu teras 80-an dengan label emas Palermo khas di bagian samping luar.',
            'colors' => [
                ['name' => 'Core Black & White', 'hex' => '#000000'],
                ['name' => 'Puma White & Black', 'hex' => '#FFFFFF'],
            ],
            'models' => [
                ['name' => 'Standard Retail Box', 'description' => 'Box hijau klasik Puma BNIB'],
                ['name' => 'Terrace Fan Edition', 'description' => 'Disertai gantungan kunci vintage Puma retro'],
            ],
            'sizes' => $fullShoeSizes,
        ]);

        // Puma 2: Fuse 2.0 Gym Trainer (Training & Gym, Running & Performance)
        $createShoeProduct([
            'brand_id' => $createdBrands['puma']->id,
            'sku' => 'PMA-FUS-018',
            'name' => 'Puma Fuse 2.0 Strength Gym Trainer',
            'slug' => 'puma-fuse-2-gym-trainer',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'category_ids' => [$createdCategories['training-gym']->id, $createdCategories['running-performance']->id],
            'images' => [['url' => 'images/shoes/puma-fuse-trainer.svg']],
            'min_price' => 1599000,
            'max_price' => 1599000,
            'description' => 'Diciptakan bersama atlet cross-training dengan sol karet PUMAGRIP traksi tinggi dan stabilitas tumit solid.',
            'product_note' => 'Dirancang untuk mengangkat beban berat dan latihan HIIT intens.',
            'colors' => [['name' => 'Black Stealth Silver', 'hex' => '#111827']],
            'models' => [['name' => 'Fuse Training Box', 'description' => 'Box resmi Puma Training']],
            'sizes' => $fullShoeSizes,
        ]);

        // Puma 3: Leadcat 2.0 Slides (Sandals & Slides)
        $createShoeProduct([
            'brand_id' => $createdBrands['puma']->id,
            'sku' => 'PMA-LDC-019',
            'name' => 'Puma Leadcat 2.0 Comfort Slide',
            'slug' => 'puma-leadcat-2-comfort-slide',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'category_ids' => [$createdCategories['sandals-slides']->id, $createdCategories['sneakers-lifestyle']->id],
            'images' => [['url' => 'images/shoes/puma-leadcat-slide.svg']],
            'min_price' => 499000,
            'max_price' => 499000,
            'description' => 'Sandal slide ikonis dengan strap kulit sintetis empuk berlogo Puma No. 1 timbul dan sol EVA cetak ergonomis.',
            'product_note' => 'Sandal santai paling laris untuk pemakaian harian santai.',
            'colors' => [['name' => 'Puma White & Black', 'hex' => '#FFFFFF']],
            'models' => [['name' => 'Leadcat Slide Box', 'description' => 'Box resmi']],
            'sizes' => ['EU 39 (US 6.5)', 'EU 40 (US 7)', 'EU 41 (US 8)', 'EU 42 (US 8.5)', 'EU 43 (US 9.5)', 'EU 44 (US 10)'],
        ]);

        // ─────────── 6. ASICS ───────────
        // Asics 1: GEL-Kayano 14 (Running & Performance, Sneakers & Lifestyle, Training & Gym)
        $createShoeProduct([
            'brand_id' => $createdBrands['asics']->id,
            'sku' => 'ASC-KYN-006',
            'name' => 'Asics GEL-Kayano 14 "Metallic Silver Black"',
            'slug' => 'asics-gel-kayano-14-metallic-silver-black',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'category_ids' => [$createdCategories['running-performance']->id, $createdCategories['sneakers-lifestyle']->id, $createdCategories['training-gym']->id],
            'images' => [['url' => 'images/shoes/asics-gel-kayano.svg']],
            'min_price' => 2299000,
            'max_price' => 2449000,
            'model_price_increment' => 150000,
            'description' => 'Mewakili estetika lari retro akhir 2000-an, GEL-Kayano 14 memperbarui performa pendahulunya dengan teknologi bantalan GEL ikonis.',
            'product_note' => 'Sepatu lari techwear era akhir 2000-an dengan teknologi bantalan GEL ikonis.',
            'colors' => [
                ['name' => 'Metallic Silver & Black', 'hex' => '#111827'],
                ['name' => 'Pure White & Smoke Grey', 'hex' => '#9CA3AF'],
            ],
            'models' => [
                ['name' => 'Performance Run Box', 'description' => 'Box original resmi Asics GEL BNIB'],
                ['name' => 'Techwear Collector Edition', 'description' => 'Termasuk insole ortholite cadangan & botol semprotan shoe care'],
            ],
            'sizes' => $fullShoeSizes,
        ]);

        // Asics 2: GEL-Sonoma 7 Trail (Outdoor & Trail, Running & Performance)
        $createShoeProduct([
            'brand_id' => $createdBrands['asics']->id,
            'sku' => 'ASC-SNM-020',
            'name' => 'Asics GEL-Sonoma 7 All-Terrain Trail',
            'slug' => 'asics-gel-sonoma-7-trail',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'category_ids' => [$createdCategories['outdoor-trail']->id, $createdCategories['running-performance']->id, $createdCategories['training-gym']->id],
            'images' => [['url' => 'images/shoes/asics-sonoma-trail.svg']],
            'min_price' => 1699000,
            'max_price' => 1699000,
            'description' => 'Dirancang untuk kegiatan luar ruangan dengan bantalan AMPLIFOAM dan unit GEL peredam getaran pada medan tanah.',
            'product_note' => 'Pola tapak sol khusus trail untuk cengkeraman kokoh di tanjakan dan turunan.',
            'colors' => [['name' => 'Graphite Grey & Black', 'hex' => '#374151']],
            'models' => [['name' => 'Trail Box', 'description' => 'Box resmi']],
            'sizes' => $fullShoeSizes,
        ]);

        // ─────────── 7. CONVERSE ───────────
        // Converse 1: Chuck 70 Vintage High (Classic Heritage, Sneakers & Lifestyle, Basketball & Court)
        $createShoeProduct([
            'brand_id' => $createdBrands['converse']->id,
            'sku' => 'CNV-C70-007',
            'name' => 'Converse Chuck 70 Vintage High "Black & Egret"',
            'slug' => 'converse-chuck-70-vintage-high-black-egret',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'category_ids' => [$createdCategories['classic-heritage']->id, $createdCategories['sneakers-lifestyle']->id, $createdCategories['basketball-court']->id],
            'images' => [['url' => 'images/shoes/converse-chuck-70.svg']],
            'min_price' => 999000,
            'max_price' => 1099000,
            'model_price_increment' => 100000,
            'description' => 'Chuck 70 dibuat berdasarkan rancangan asli tahun 1970-an dengan perhatian luar biasa pada detail kanvas tebal 12oz.',
            'product_note' => 'Kanvas tebal 12oz dengan midsole egret mengilap dan bantalan insole OrthoLite super empuk.',
            'colors' => [
                ['name' => 'Black Canvas & Egret', 'hex' => '#000000'],
                ['name' => 'Optic White Canvas', 'hex' => '#FFFFFF'],
            ],
            'models' => [
                ['name' => 'Classic High 70s Box', 'description' => 'Box merah heritage Converse BNIB'],
                ['name' => 'Vintage Premium Edition', 'description' => 'Termasuk extra tali flat katun 100% warna cream'],
            ],
            'sizes' => $fullShoeSizes,
        ]);

        // Converse 2: One Star Pro Suede Skate (Slip-On & Skate, Sneakers & Lifestyle)
        $createShoeProduct([
            'brand_id' => $createdBrands['converse']->id,
            'sku' => 'CNV-OST-021',
            'name' => 'Converse One Star Pro Suede Skate Edition',
            'slug' => 'converse-one-star-pro-skate',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'category_ids' => [$createdCategories['slip-on-skate']->id, $createdCategories['sneakers-lifestyle']->id, $createdCategories['classic-heritage']->id],
            'images' => [['url' => 'images/shoes/converse-onestar-skate.svg']],
            'min_price' => 1199000,
            'max_price' => 1199000,
            'description' => 'Siluet One Star ikonik berbahan suede berlapis karet penyerap benturan dengan insole CX foam.',
            'product_note' => 'Cengkeraman papan skate optimal dengan sol karet CONS traction.',
            'colors' => [['name' => 'Black Suede White Star', 'hex' => '#111827']],
            'models' => [['name' => 'CONS Skate Box', 'description' => 'Box resmi']],
            'sizes' => $fullShoeSizes,
        ]);

        // ─────────── 8. VANS ───────────
        // Vans 1: Old Skool Classic (Slip-On & Skate, Sneakers & Lifestyle)
        $createShoeProduct([
            'brand_id' => $createdBrands['vans']->id,
            'sku' => 'VNS-OSK-008',
            'name' => 'Vans Old Skool Classic "Black & White Canvas"',
            'slug' => 'vans-old-skool-classic-black-white',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'category_ids' => [$createdCategories['slip-on-skate']->id, $createdCategories['sneakers-lifestyle']->id],
            'images' => [['url' => 'images/shoes/vans-oldskool-black.svg']],
            'min_price' => 899000,
            'max_price' => 999000,
            'model_price_increment' => 100000,
            'description' => 'Sepatu pertama Vans yang memperkenalkan sidestripe ikonis dengan upper kanvas kokoh dan suede tahan abrasi.',
            'product_note' => 'Sepatu skate ikonis dengan garis jazz sidestripe legendaris dan sol waffle karet vulkanisir.',
            'colors' => [
                ['name' => 'Core Black & White Sidestripe', 'hex' => '#000000'],
                ['name' => 'Triple White Canvas', 'hex' => '#FFFFFF'],
            ],
            'models' => [
                ['name' => 'Standard Waffle Box', 'description' => 'Box merah klasik Vans BNIB'],
                ['name' => 'Pro Skate Cushion Edition', 'description' => 'Dilengkapi insole PopCush anti benturan ekstra'],
            ],
            'sizes' => $fullShoeSizes,
        ]);

        // Vans 2: Classic Slip-On Checkerboard (Slip-On & Skate, Classic Heritage)
        $createShoeProduct([
            'brand_id' => $createdBrands['vans']->id,
            'sku' => 'VNS-SLP-022',
            'name' => 'Vans Classic Slip-On "Black White Checkerboard"',
            'slug' => 'vans-classic-slip-on-checkerboard',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'category_ids' => [$createdCategories['slip-on-skate']->id, $createdCategories['classic-heritage']->id, $createdCategories['sneakers-lifestyle']->id],
            'images' => [['url' => 'images/shoes/vans-slipon-checker.svg']],
            'min_price' => 849000,
            'max_price' => 849000,
            'description' => 'Motif papan catur hitam-putih paling ikonik di dunia tanpa tali, praktis dipakai sehari-hari.',
            'product_note' => 'Karet elastis samping yang nyaman dan sol wafel khas Vans.',
            'colors' => [['name' => 'Checkerboard Black & White', 'hex' => '#000000']],
            'models' => [['name' => 'Classic Slip-On Box', 'description' => 'Box resmi']],
            'sizes' => $fullShoeSizes,
        ]);

        // Vans 3: UltraRange EXO MTE Trail (Outdoor & Trail, Running & Performance, Training & Gym)
        $createShoeProduct([
            'brand_id' => $createdBrands['vans']->id,
            'sku' => 'VNS-ULT-023',
            'name' => 'Vans UltraRange EXO MTE All-Weather Trail',
            'slug' => 'vans-ultrarange-exo-mte-trail',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'category_ids' => [$createdCategories['outdoor-trail']->id, $createdCategories['running-performance']->id, $createdCategories['training-gym']->id],
            'images' => [['url' => 'images/shoes/vans-ultrarange-trail.svg']],
            'min_price' => 1799000,
            'max_price' => 1799000,
            'description' => 'Didesain untuk menghadapi berbagai cuaca dengan bantalan UltraCush Lite dan sol luar berprofil agresif.',
            'product_note' => 'Sangat ringan dengan perlindungan cuaca tak terduga.',
            'colors' => [['name' => 'Covert Black Charcoal', 'hex' => '#111827']],
            'models' => [['name' => 'UltraRange Adventure Box', 'description' => 'Box resmi']],
            'sizes' => $fullShoeSizes,
        ]);

        // ─────────── 9. BUNDLE GROSIR DISTRIBUTOR ───────────
        $pGrosir = Product::create([
            'brand_id' => $createdBrands['adidas']->id,
            'sku' => 'ADS-SMB-024',
            'name' => 'Paket Seri Grosir Adidas Samba OG (Bundle 5 Pasang Size Run)',
            'slug' => 'paket-seri-grosir-adidas-samba-og',
            'product_type' => 'ready_stock',
            'visible_to_umum' => false,
            'visible_to_member' => false,
            'visible_to_distributor' => true, // HANYA DISTRIBUTOR
            'weight_grams' => 4500,
            'product_note' => 'Khusus Mitra Distributor & Reseller berlisensi PasarKita. Minimal pembelian 1 paket seri (isi 5 pasang lengkap size run EU 40-44).',
            'description' => 'Paket grosir partai resmi Adidas Samba OG paling dicari di pasar sneaker. Sol gum klasik dengan kulit asli halus dan suede T-toe khas, perpaduan Cloud White dan aksen Core Black.',
            'min_price' => 8250000,
            'max_price' => 8250000,
            'status' => 'active',
        ]);
        $pGrosir->categories()->attach([$createdCategories['sneakers-lifestyle']->id, $createdCategories['classic-heritage']->id]);

        ProductImage::create([
            'product_id' => $pGrosir->id,
            'image_path' => 'images/shoes/adidas-samba-white.svg',
            'sort_order' => 1,
            'is_primary' => true,
        ]);

        ProductColor::create(['product_id' => $pGrosir->id, 'color_name' => 'Cloud White & Core Black', 'hex_code' => '#000000', 'sort_order' => 1]);
        $mGrosir_1 = ProductModel::create(['product_id' => $pGrosir->id, 'model_name' => 'Paket Seri 5 Pasang Size Run', 'description' => 'Paket bundling distributor lengkap size run 40-44', 'sort_order' => 1]);
        $sGrosir_seri = ProductSize::create(['product_id' => $pGrosir->id, 'size_label' => 'Bundle Seri EU 40-44 (5 Pasang)', 'is_available' => true, 'sort_order' => 1]);
        ProductVariantPrice::create(['product_id' => $pGrosir->id, 'product_model_id' => $mGrosir_1->id, 'product_size_id' => $sGrosir_seri->id, 'price' => 8250000, 'stock' => 20]);
    }
}
