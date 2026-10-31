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
                'description' => 'Pionir inovasi sepatu olahraga & sneakers lifestyle dunia dengan lini ikonik Dunk, Air Max, dan Court.',
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
                'description' => 'Ikon streetwear dan terrace fashion asal Jerman dengan siluet retro klasik Samba, Gazelle, dan Spezial.',
                'show_on_homepage' => true,
                'homepage_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'New Balance',
                'slug' => 'new-balance',
                'dp_percentage' => 30.00,
                'description' => 'Kenyamanan legendaris asal Boston dengan siluet retro-runner 550, 990v, dan 2002R berkualitas tinggi.',
                'show_on_homepage' => true,
                'homepage_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Puma',
                'slug' => 'puma',
                'dp_percentage' => 25.00,
                'description' => 'Desain klasik retro motorsport dan street-classic lewat rilisan Palermo, Suede, dan Speedcat.',
                'show_on_homepage' => true,
                'homepage_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Asics',
                'slug' => 'asics',
                'dp_percentage' => 30.00,
                'description' => 'Performa lari dan tren Japanese techwear dengan teknologi bantalan GEL andalan yang nyaman seharian.',
                'show_on_homepage' => true,
                'homepage_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Converse',
                'slug' => 'converse',
                'dp_percentage' => 25.00,
                'description' => 'Sepatu kanvas ikonik Chuck Taylor All Star 70s dan One Star yang timeless dari generasi ke generasi.',
                'show_on_homepage' => true,
                'homepage_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Vans',
                'slug' => 'vans',
                'dp_percentage' => 25.00,
                'description' => 'Kultur skate legendaris asal California lewat siluet Old Skool, Sk8-Hi, dan Authentic bersol waffle ikonik.',
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

        // 10. Data Banners Promo
        Banner::create([
            'title' => 'Official Drop: Branded Sneakers Collection',
            'description' => 'Koleksi rilisan terbaru dari Nike, Jordan, Adidas, hingga New Balance dengan jaminan 100% original bergaransi PasarKita.',
            'image_path' => 'https://images.unsplash.com/photo-1552346154-21d32810aba3?w=1600&auto=format&fit=crop&q=80',
            'url' => '/katalog',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        Banner::create([
            'title' => 'Pre-Order Rilisan Langka & Edisi Terbatas',
            'description' => 'Amankan siluet sneaker langka dan size favoritmu dengan skema DP mulai 25% khusus Member & Mitra PasarKita.',
            'image_path' => 'https://images.unsplash.com/photo-1584735935682-2f2b69dff9d2?w=1600&auto=format&fit=crop&q=80',
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

            // Foto Produk
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
                            'stock' => $cfg['default_stock'] ?? 15,
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

        // 12. BUAT 10 PRODUK SEPATU BRANDED LENGKAP & IKONIK

        // PRODUK 1: Nike Dunk Low Retro "Panda" White Black
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
            'images' => [
                ['url' => 'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?w=800&auto=format&fit=crop&q=80'],
                ['url' => 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=800&auto=format&fit=crop&q=80'],
            ],
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
                'description' => 'Gratis 1 kit pembersih sneaker premium dan lap mikrofiber setiap pembelian min 2 pasang sepatu.',
                'min_qty' => 2,
            ],
        ]);

        // PRODUK 2: Air Jordan 1 Retro High OG "Chicago Lost & Found" (Pre-Order / Member)
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
            'images' => [
                ['url' => 'https://images.unsplash.com/photo-1552346154-21d32810aba3?w=800&auto=format&fit=crop&q=80'],
                ['url' => 'https://images.unsplash.com/photo-1584735935682-2f2b69dff9d2?w=800&auto=format&fit=crop&q=80'],
            ],
            'colors' => [
                ['name' => 'Chicago Vintage Red & Black', 'hex' => '#000000'],
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

        // PRODUK 3: Adidas Samba OG "Cloud White & Core Black"
        $createShoeProduct([
            'brand_id' => $createdBrands['adidas']->id,
            'sku' => 'ADS-SMB-003',
            'name' => 'Adidas Samba OG "Cloud White & Core Black"',
            'slug' => 'adidas-samba-og-cloud-white-core-black',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'visible_to_member' => true,
            'visible_to_distributor' => true,
            'weight_grams' => 880,
            'product_note' => 'Siluet terrace retro terlaris dengan sol gum cokelat klasik dan upper leather premium lembut.',
            'description' => 'Adidas Samba OG lahir di lapangan sepak bola dalam ruangan dan kini menjadi ikon street-style dunia yang tak terbendung. Didesain dengan material kulit halus, detail suede berbentuk T di ujung jari kaki, dan tiga garis hitam khas Adidas.',
            'min_price' => 1649000,
            'max_price' => 1749000,
            'model_price_increment' => 100000,
            'category_ids' => [$createdCategories['sneakers-lifestyle']->id, $createdCategories['classic-heritage']->id],
            'images' => [
                ['url' => 'https://images.unsplash.com/photo-1587563871167-1ee9c731aefb?w=800&auto=format&fit=crop&q=80'],
            ],
            'colors' => [
                ['name' => 'Cloud White & Core Black', 'hex' => '#000000'],
                ['name' => 'Core Black & White', 'hex' => '#111827'],
                ['name' => 'Triple White Gum', 'hex' => '#FFFFFF'],
            ],
            'models' => [
                ['name' => 'Standard Classic Box', 'description' => 'Box original biru klasik Adidas Originals BNIB'],
                ['name' => 'Heritage Special Edition', 'description' => 'Termasuk dust bag kanvas & gantungan kunci akrilik'],
            ],
            'sizes' => ['EU 38 (US 5.5)', 'EU 39 (US 6.5)', 'EU 40 (US 7)', 'EU 41 (US 8)', 'EU 42 (US 8.5)', 'EU 43 (US 9.5)', 'EU 44 (US 10)'],
        ]);

        // PRODUK 4: New Balance 550 "White & Sea Salt Black"
        $createShoeProduct([
            'brand_id' => $createdBrands['new-balance']->id,
            'sku' => 'NB-550-004',
            'name' => 'New Balance 550 "White & Sea Salt Black" Classic',
            'slug' => 'new-balance-550-white-sea-salt-black',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'visible_to_member' => true,
            'visible_to_distributor' => true,
            'weight_grams' => 980,
            'product_note' => 'Siluet retro basketball vintage 1989. Menggunakan full leather upper premium dengan bantalan nyaman.',
            'description' => 'Siluet legendaris dari New Balance yang menjadi favorit pecinta mode kasual. Perpaduan warna putih bersih dan aksen hitam pekat memberikan estetika vintage yang tak lekang oleh waktu dengan kenyamanan bantalan sepanjang hari.',
            'min_price' => 1699000,
            'max_price' => 1799000,
            'model_price_increment' => 100000,
            'category_ids' => [$createdCategories['sneakers-lifestyle']->id, $createdCategories['basketball-court']->id, $createdCategories['classic-heritage']->id],
            'images' => [
                ['url' => 'https://images.unsplash.com/photo-1539185441755-769473a23570?w=800&auto=format&fit=crop&q=80'],
            ],
            'colors' => [
                ['name' => 'White & Core Black', 'hex' => '#000000'],
                ['name' => 'Sea Salt & Monochrome Grey', 'hex' => '#6B7280'],
                ['name' => 'Triple White', 'hex' => '#FFFFFF'],
            ],
            'models' => [
                ['name' => 'Standard Retail Box', 'description' => 'Sepatu original BNIB dengan lace bawaan'],
                ['name' => 'Premium Care Bundle', 'description' => 'Termasuk extra vintage laces & spray pelindung noda'],
            ],
            'sizes' => ['EU 39 (US 6.5)', 'EU 40 (US 7)', 'EU 41 (US 8)', 'EU 42 (US 8.5)', 'EU 43 (US 9.5)', 'EU 44 (US 10)', 'EU 45 (US 11)'],
        ]);

        // PRODUK 5: Puma Palermo Classic "Black & Gum White"
        $createShoeProduct([
            'brand_id' => $createdBrands['puma']->id,
            'sku' => 'PMA-PLM-005',
            'name' => 'Puma Palermo Classic "Black & Gum White"',
            'slug' => 'puma-palermo-classic-black-gum-white',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'visible_to_member' => true,
            'visible_to_distributor' => true,
            'weight_grams' => 850,
            'product_note' => 'Sepatu teras 80-an dengan label emas Palermo khas di bagian samping luar.',
            'description' => 'Puma Palermo kembali hadir dari arsip legendaris era 80-an. Membawa kembali nuansa retro sepak bola Eropa dengan konstruksi sol luar gum karet, upper suede premium, dan formstrip putih kontras yang elegan.',
            'min_price' => 1299000,
            'max_price' => 1399000,
            'model_price_increment' => 100000,
            'category_ids' => [$createdCategories['sneakers-lifestyle']->id, $createdCategories['classic-heritage']->id],
            'images' => [
                ['url' => 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=800&auto=format&fit=crop&q=80'],
            ],
            'colors' => [
                ['name' => 'Core Black & White', 'hex' => '#000000'],
                ['name' => 'Puma White & Black', 'hex' => '#FFFFFF'],
                ['name' => 'Monochrome Charcoal', 'hex' => '#374151'],
            ],
            'models' => [
                ['name' => 'Standard Retail Box', 'description' => 'Box hijau klasik Puma BNIB'],
                ['name' => 'Terrace Fan Edition', 'description' => 'Disertai gantungan kunci vintage Puma retro'],
            ],
            'sizes' => ['EU 39 (US 6.5)', 'EU 40 (US 7)', 'EU 41 (US 8)', 'EU 42 (US 8.5)', 'EU 43 (US 9.5)', 'EU 44 (US 10)'],
        ]);

        // PRODUK 6: Asics GEL-Kayano 14 "Metallic Silver Black"
        $createShoeProduct([
            'brand_id' => $createdBrands['asics']->id,
            'sku' => 'ASC-KYN-006',
            'name' => 'Asics GEL-Kayano 14 "Metallic Silver Black"',
            'slug' => 'asics-gel-kayano-14-metallic-silver-black',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'visible_to_member' => true,
            'visible_to_distributor' => true,
            'weight_grams' => 920,
            'product_note' => 'Sepatu lari techwear era akhir 2000-an dengan teknologi bantalan GEL ikonis.',
            'description' => 'Mewakili estetika lari retro akhir 2000-an, GEL-Kayano 14 memperbarui performa pendahulunya dengan material kontemporer. Lapisan jaring berpori dengan aksen metallic silver dan black memberikan tampilan street-tech yang sangat diminati.',
            'min_price' => 2299000,
            'max_price' => 2449000,
            'model_price_increment' => 150000,
            'category_ids' => [$createdCategories['running-performance']->id, $createdCategories['sneakers-lifestyle']->id, $createdCategories['training-gym']->id],
            'images' => [
                ['url' => 'https://images.unsplash.com/photo-1579338559194-a162d19bf842?w=800&auto=format&fit=crop&q=80'],
            ],
            'colors' => [
                ['name' => 'Metallic Silver & Black', 'hex' => '#111827'],
                ['name' => 'Pure White & Smoke Grey', 'hex' => '#9CA3AF'],
                ['name' => 'Triple Black Stealth', 'hex' => '#000000'],
            ],
            'models' => [
                ['name' => 'Performance Run Box', 'description' => 'Box original resmi Asics GEL BNIB'],
                ['name' => 'Techwear Collector Edition', 'description' => 'Termasuk insole ortholite cadangan & botol semprotan shoe care'],
            ],
            'sizes' => ['EU 40 (US 7)', 'EU 41 (US 8)', 'EU 42 (US 8.5)', 'EU 43 (US 9.5)', 'EU 44 (US 10)', 'EU 45 (US 11)'],
        ]);

        // PRODUK 7: Converse Chuck 70 Vintage High "Black & Egret"
        $createShoeProduct([
            'brand_id' => $createdBrands['converse']->id,
            'sku' => 'CNV-C70-007',
            'name' => 'Converse Chuck 70 Vintage High "Black & Egret"',
            'slug' => 'converse-chuck-70-vintage-high-black-egret',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'visible_to_member' => true,
            'visible_to_distributor' => true,
            'weight_grams' => 890,
            'product_note' => 'Kanvas tebal 12oz dengan midsole egret mengilap dan bantalan insole OrthoLite super empuk.',
            'description' => 'Chuck 70 dibuat berdasarkan rancangan asli tahun 1970-an dengan perhatian luar biasa pada detail material. Jahitan bersayap pada lidah sepatu, kanvas berbobot tinggi, dan plat lisensi vintage hitam di bagian tumit menjadikannya sneaker paling timeless.',
            'min_price' => 999000,
            'max_price' => 1099000,
            'model_price_increment' => 100000,
            'category_ids' => [$createdCategories['classic-heritage']->id, $createdCategories['sneakers-lifestyle']->id],
            'images' => [
                ['url' => 'https://images.unsplash.com/photo-1607522370275-f14206abe5d3?w=800&auto=format&fit=crop&q=80'],
            ],
            'colors' => [
                ['name' => 'Black Canvas & Egret', 'hex' => '#000000'],
                ['name' => 'Optic White Canvas', 'hex' => '#FFFFFF'],
                ['name' => 'Monochrome All-Black', 'hex' => '#111827'],
            ],
            'models' => [
                ['name' => 'Classic High 70s Box', 'description' => 'Box merah heritage Converse BNIB'],
                ['name' => 'Vintage Premium Edition', 'description' => 'Termasuk extra tali flat katun 100% warna cream'],
            ],
            'sizes' => $fullShoeSizes,
        ]);

        // PRODUK 8: Vans Old Skool Classic "Black & White Canvas"
        $createShoeProduct([
            'brand_id' => $createdBrands['vans']->id,
            'sku' => 'VNS-OSK-008',
            'name' => 'Vans Old Skool Classic "Black & White Canvas"',
            'slug' => 'vans-old-skool-classic-black-white',
            'product_type' => 'ready_stock',
            'visible_to_umum' => true,
            'visible_to_member' => true,
            'visible_to_distributor' => true,
            'weight_grams' => 840,
            'product_note' => 'Sepatu skate ikonis dengan garis jazz sidestripe legendaris dan sol waffle karet vulkanisir.',
            'description' => 'Sepatu pertama Vans yang memperkenalkan sidestripe ikonis. Dibuat dengan upper kanvas kokoh dan suede tahan abrasi, kerah empuk yang menopang pergelangan kaki, serta outsole wafel khas Vans yang mencengkeram kuat.',
            'min_price' => 899000,
            'max_price' => 999000,
            'model_price_increment' => 100000,
            'category_ids' => [$createdCategories['slip-on-skate']->id, $createdCategories['sneakers-lifestyle']->id],
            'images' => [
                ['url' => 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?w=800&auto=format&fit=crop&q=80'],
            ],
            'colors' => [
                ['name' => 'Core Black & White Sidestripe', 'hex' => '#000000'],
                ['name' => 'Triple White Canvas', 'hex' => '#FFFFFF'],
                ['name' => 'Triple Black Suede', 'hex' => '#111827'],
            ],
            'models' => [
                ['name' => 'Standard Waffle Box', 'description' => 'Box merah klasik Vans BNIB'],
                ['name' => 'Pro Skate Cushion Edition', 'description' => 'Dilengkapi insole PopCush anti benturan ekstra'],
            ],
            'sizes' => $fullShoeSizes,
        ]);

        // PRODUK 9: Air Jordan 4 Retro "Military Black" (Pre-Order Member)
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
            'description' => 'Air Jordan 4 Military Black mengadopsi struktur color blocking klasik dengan material kulit putih berbutir halus, suede abu-abu di bagian mudguard, dan aksen hitam pekat pada jaring samping dan sayap tali penyangga.',
            'min_price' => 3499000,
            'max_price' => 3699000,
            'model_price_increment' => 200000,
            'category_ids' => [$createdCategories['basketball-court']->id, $createdCategories['sneakers-lifestyle']->id],
            'images' => [
                ['url' => 'https://images.unsplash.com/photo-1584735935682-2f2b69dff9d2?w=800&auto=format&fit=crop&q=80'],
            ],
            'colors' => [
                ['name' => 'White & Neutral Grey Black', 'hex' => '#000000'],
                ['name' => 'Pure Stealth Black', 'hex' => '#111827'],
            ],
            'models' => [
                ['name' => 'Standard Flight Box', 'description' => 'Box retro bercorak marmer Jordan 4 BNIB'],
                ['name' => 'OG Vault Flight Case', 'description' => 'Disertai hard case pelindung sneaker & tag Jumpman akrilik'],
            ],
            'sizes' => ['EU 40 (US 7)', 'EU 41 (US 8)', 'EU 42 (US 8.5)', 'EU 43 (US 9.5)', 'EU 44 (US 10)', 'EU 45 (US 11)'],
            'price_rule' => [
                'type' => 'cut',
                'amount' => 100000,
                'description' => 'Diskon grosir reseller/distributor Rp 100.000 per pasang',
            ],
        ]);

        // PRODUK 10: Paket Seri Grosir Adidas Samba OG (Eksklusif Distributor B2B)
        $p10 = Product::create([
            'brand_id' => $createdBrands['adidas']->id,
            'sku' => 'ADS-SMB-010',
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
        $p10->categories()->attach([$createdCategories['sneakers-lifestyle']->id, $createdCategories['classic-heritage']->id]);

        ProductImage::create([
            'product_id' => $p10->id,
            'image_path' => 'https://images.unsplash.com/photo-1587563871167-1ee9c731aefb?w=800&auto=format&fit=crop&q=80',
            'sort_order' => 1,
            'is_primary' => true,
        ]);

        ProductColor::create(['product_id' => $p10->id, 'color_name' => 'Cloud White & Core Black', 'hex_code' => '#000000', 'sort_order' => 1]);
        $m10_1 = ProductModel::create(['product_id' => $p10->id, 'model_name' => 'Paket Seri 5 Pasang Size Run', 'description' => 'Paket bundling distributor lengkap size run 40-44', 'sort_order' => 1]);
        $s10_seri = ProductSize::create(['product_id' => $p10->id, 'size_label' => 'Bundle Seri EU 40-44 (5 Pasang)', 'is_available' => true, 'sort_order' => 1]);
        ProductVariantPrice::create(['product_id' => $p10->id, 'product_model_id' => $m10_1->id, 'product_size_id' => $s10_seri->id, 'price' => 8250000, 'stock' => 20]);
    }
}
