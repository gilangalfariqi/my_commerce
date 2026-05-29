<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Permissions
        $permissions = [
            'manage-products',
            'manage-orders',
            'manage-users',
            'manage-payments',
            'manage-shipping',
            'manage-seo',
            'manage-settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 2. Seed Roles and Assign Permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $customerRole = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        $superAdminRole->syncPermissions(Permission::all());
        $adminRole->syncPermissions([
            'manage-products',
            'manage-orders',
            'manage-payments',
            'manage-shipping',
            'manage-seo',
        ]);

        // 3. Seed Users
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@mycommerce.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'phone' => '+6281234567890',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->assignRole($superAdminRole);

        $adminUser = User::firstOrCreate(
            ['email' => 'staff@mycommerce.com'],
            [
                'name' => 'Store Manager',
                'password' => Hash::make('password'),
                'phone' => '+6281234567891',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $adminUser->assignRole($adminRole);

        $customerUser = User::firstOrCreate(
            ['email' => 'customer@mycommerce.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password'),
                'phone' => '+6281234567892',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $customerUser->assignRole($customerRole);

        // 4. Seed Couriers
        $couriers = [
            ['code' => 'jne', 'name' => 'Jalur Nugraha Ekakurir (JNE)', 'is_active' => true],
            ['code' => 'pos', 'name' => 'POS Indonesia', 'is_active' => true],
            ['code' => 'tiki', 'name' => 'Citra Van Titipan Kilat (TIKI)', 'is_active' => true],
        ];

        foreach ($couriers as $courier) {
            DB::table('couriers')->updateOrInsert(
                ['code' => $courier['code']],
                ['name' => $courier['name'], 'is_active' => $courier['is_active'], 'created_at' => now(), 'updated_at' => now()]
            );
        }

        // 5. Seed Settings
        $settings = [
            [
                'key' => 'store_name',
                'value' => 'MotoPart Hub',
                'description' => 'The public name of the e-commerce storefront.',
            ],
            [
                'key' => 'store_email',
                'value' => 'support@motoparthub.com',
                'description' => 'Primary store contact email.',
            ],
            [
                'key' => 'store_phone',
                'value' => '+6281234567890',
                'description' => 'Primary customer service phone number.',
            ],
            [
                'key' => 'store_address',
                'value' => 'Kebayoran Lama No. 88, South Jakarta, Indonesia',
                'description' => 'Physical store or warehouse address.',
            ],
            [
                'key' => 'rajaongkir_origin_city_id',
                'value' => '153', // Jakarta Selatan
                'description' => 'Origin city ID from RajaOngkir for shipping calculations.',
            ],
            [
                'key' => 'rajaongkir_origin_province_id',
                'value' => '6', // DKI Jakarta
                'description' => 'Origin province ID from RajaOngkir for shipping calculations.',
            ],
            [
                'key' => 'default_shipping_weight',
                'value' => '1000', // 1 kg
                'description' => 'Default weight used if product weight is zero (in grams).',
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'description' => $setting['description'], 'created_at' => now(), 'updated_at' => now()]
            );
        }

        // 6. Clean existing records to avoid duplicates
        DB::table('flash_sale_items')->delete();
        DB::table('flash_sales')->delete();
        DB::table('coupons')->delete();
        DB::table('banners')->delete();
        DB::table('product_variants')->delete();
        DB::table('product_images')->delete();
        DB::table('category_product')->delete();
        DB::table('products')->delete();
        DB::table('categories')->delete();

        // 7. Seed Banners for MotoPart Hub
        $bannersData = [
            [
                'title' => 'Suku Cadang Asli & Aftermarket Terbaik',
                'subtitle' => 'Temukan suku cadang performa tinggi untuk motor Honda, Yamaha, Kawasaki, dan Suzuki Anda.',
                'image_path' => 'https://images.unsplash.com/photo-1558981806-ec527fa84c39?q=80&w=1200',
                'click_url' => '/products',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Maksimalkan Performa Mesin Anda',
                'subtitle' => 'Piston, busi iridium, oli premium, dan knalpot racing berkualitas internasional.',
                'image_path' => 'https://images.unsplash.com/photo-1609630875171-b1321377ee65?q=80&w=1200',
                'click_url' => '/products?category=mesin',
                'sort_order' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($bannersData as $b) {
            \App\Models\Banner::create($b);
        }

        // 8. Seed Categories
        $categoriesData = [
            'mesin' => [
                'name' => 'Mesin',
                'slug' => 'mesin',
                'description' => 'Piston, block cylinder, klep, gasket, dan komponen dalam mesin lainnya.',
                'image' => null,
                'is_active' => true,
                'sort_order' => 1,
            ],
            'pengapian' => [
                'name' => 'Pengapian',
                'slug' => 'pengapian',
                'description' => 'Busi, CDI, Koil, Aki, Spul, dan kelistrikan motor.',
                'image' => null,
                'is_active' => true,
                'sort_order' => 2,
            ],
            'kaki-kaki' => [
                'name' => 'Kaki-kaki',
                'slug' => 'kaki-kaki',
                'description' => 'Shockbreaker, swing arm, bearing, shock depan, dan kemudi.',
                'image' => null,
                'is_active' => true,
                'sort_order' => 3,
            ],
            'pengereman' => [
                'name' => 'Pengereman',
                'slug' => 'pengereman',
                'description' => 'Kampas rem, piringan cakram, kaliper, master rem, dan minyak rem.',
                'image' => null,
                'is_active' => true,
                'sort_order' => 4,
            ],
            'bodi' => [
                'name' => 'Bodi',
                'slug' => 'bodi',
                'description' => 'Fairing, cover bodi, spakbor, kaca spion, dan jok motor.',
                'image' => null,
                'is_active' => true,
                'sort_order' => 5,
            ],
            'aksesoris' => [
                'name' => 'Aksesoris',
                'slug' => 'aksesoris',
                'description' => 'Handgrip, jalu stang, bracket, phone holder, stiker pelindung.',
                'image' => null,
                'is_active' => true,
                'sort_order' => 6,
            ],
            'ban' => [
                'name' => 'Ban & Velg',
                'slug' => 'ban',
                'description' => 'Ban luar, ban dalam, velg racing, jari-jari.',
                'image' => null,
                'is_active' => true,
                'sort_order' => 7,
            ],
            'oli' => [
                'name' => 'Oli & Cairan',
                'slug' => 'oli',
                'description' => 'Oli mesin premium, oli shock, minyak rem, cairan radiator.',
                'image' => null,
                'is_active' => true,
                'sort_order' => 8,
            ],
            'knalpot' => [
                'name' => 'Knalpot',
                'slug' => 'knalpot',
                'description' => 'Knalpot racing full system, slip-on, silencer, db killer.',
                'image' => null,
                'is_active' => true,
                'sort_order' => 9,
            ],
            'transmisi' => [
                'name' => 'Transmisi',
                'slug' => 'transmisi',
                'description' => 'Rantai, gir set, roller CVT, v-belt, kampas ganda.',
                'image' => null,
                'is_active' => true,
                'sort_order' => 10,
            ],
        ];

        $createdCategories = [];
        foreach ($categoriesData as $key => $c) {
            $createdCategories[$key] = \App\Models\Category::create($c);
        }

        // 9. Seed Products & Compatibility
        $productsData = [
            [
                'category' => 'bodi',
                'product' => [
                    'name' => 'Yamaha R15 Fairing Kit (V3 Carbon Look)',
                    'slug' => 'yamaha-r15-fairing-kit-v3-carbon-look',
                    'sku' => 'BDY-R15-01',
                    'short_description' => 'Body fairing kit sporty bermotif carbon look khusus untuk Yamaha R15 V3.',
                    'description' => '<p>Dibuat dari bahan ABS tebal berkualitas tinggi dengan finishing cat premium motif karbon. Desain sangat presisi dan pnp (plug and play) tanpa perlu ubahan pada rangka motor.</p><p>Cocok untuk membuat tampilan Yamaha R15 Anda menjadi lebih racy dan sporty layaknya motor balap sirkuit.</p>',
                    'price' => 1500000,
                    'compare_at_price' => 3000000,
                    'cost_price' => 700000,
                    'weight' => 5000,
                    'stock' => 10,
                    'is_active' => true,
                    'is_featured' => true,
                    'metadata' => [
                        'compatibility' => [
                            ['brand' => 'Yamaha', 'model' => 'R15', 'year' => '2019'],
                            ['brand' => 'Yamaha', 'model' => 'R15', 'year' => '2020'],
                            ['brand' => 'Yamaha', 'model' => 'R15', 'year' => '2021'],
                            ['brand' => 'Yamaha', 'model' => 'R15', 'year' => '2022'],
                            ['brand' => 'Yamaha', 'model' => 'R15', 'year' => '2023'],
                        ]
                    ]
                ],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?q=80&w=600', 'is_primary' => true],
                ],
                'variants' => [],
            ],
            [
                'category' => 'pengereman',
                'product' => [
                    'name' => 'Brembo Ceramic Red Series Brake Pads (Front)',
                    'slug' => 'brembo-ceramic-red-series-brake-pads-front',
                    'sku' => 'BRK-BREM-02',
                    'short_description' => 'Kampas rem Brembo Red Series keramik premium untuk performa pengereman maksimal.',
                    'description' => '<p>Kampas rem Brembo Ceramic Red Series menawarkan koefisien gesek yang stabil pada suhu tinggi, meminimalisir pudar rem (brake fade), serta ramah terhadap piringan cakram. Sangat direkomendasikan untuk motor sport harian maupun penggunaan sirkuit.</p>',
                    'price' => 350000,
                    'compare_at_price' => 450000,
                    'cost_price' => 180000,
                    'weight' => 200,
                    'stock' => 50,
                    'is_active' => true,
                    'is_featured' => true,
                    'metadata' => [
                        'compatibility' => [
                            ['brand' => 'Honda', 'model' => 'CBR250RR', 'year' => '2020'],
                            ['brand' => 'Honda', 'model' => 'CBR250RR', 'year' => '2021'],
                            ['brand' => 'Honda', 'model' => 'CBR250RR', 'year' => '2022'],
                            ['brand' => 'Honda', 'model' => 'CBR250RR', 'year' => '2023'],
                            ['brand' => 'Yamaha', 'model' => 'R25', 'year' => '2020'],
                            ['brand' => 'Yamaha', 'model' => 'R25', 'year' => '2021'],
                            ['brand' => 'Yamaha', 'model' => 'R25', 'year' => '2022'],
                            ['brand' => 'Yamaha', 'model' => 'R25', 'year' => '2023'],
                            ['brand' => 'Kawasaki', 'model' => 'Ninja 250', 'year' => '2019'],
                            ['brand' => 'Kawasaki', 'model' => 'Ninja 250', 'year' => '2020'],
                            ['brand' => 'Kawasaki', 'model' => 'Ninja 250', 'year' => '2021'],
                            ['brand' => 'Kawasaki', 'model' => 'Ninja 250', 'year' => '2022'],
                        ]
                    ]
                ],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1485965120184-e220f721d03e?q=80&w=600', 'is_primary' => true],
                ],
                'variants' => [],
            ],
            [
                'category' => 'ban',
                'product' => [
                    'name' => 'Michelin Pilot Street Radial Tire 140/70-17',
                    'slug' => 'michelin-pilot-street-radial-tire-140-70-17',
                    'sku' => 'TIR-MICH-03',
                    'short_description' => 'Ban radial Michelin Pilot Street memberikan daya cengkeram luar biasa di jalan basah dan kering.',
                    'description' => '<p>Teknologi konstruksi radial Michelin menawarkan kenyamanan berkendara yang superior serta umur ban yang lebih panjang. Pola alur ban yang optimal memastikan pembuangan air yang cepat guna menghindari aquaplaning.</p>',
                    'price' => 720000,
                    'compare_at_price' => 1440000,
                    'cost_price' => 400000,
                    'weight' => 4500,
                    'stock' => 20,
                    'is_active' => true,
                    'is_featured' => true,
                    'metadata' => [
                        'compatibility' => [
                            ['brand' => 'Honda', 'model' => 'CBR250RR', 'year' => '2020'],
                            ['brand' => 'Honda', 'model' => 'CBR250RR', 'year' => '2021'],
                            ['brand' => 'Honda', 'model' => 'CBR250RR', 'year' => '2022'],
                            ['brand' => 'Honda', 'model' => 'CBR250RR', 'year' => '2023'],
                            ['brand' => 'Yamaha', 'model' => 'R25', 'year' => '2020'],
                            ['brand' => 'Yamaha', 'model' => 'R25', 'year' => '2021'],
                            ['brand' => 'Yamaha', 'model' => 'R25', 'year' => '2022'],
                            ['brand' => 'Yamaha', 'model' => 'R25', 'year' => '2023'],
                            ['brand' => 'Kawasaki', 'model' => 'Ninja 250', 'year' => '2019'],
                            ['brand' => 'Kawasaki', 'model' => 'Ninja 250', 'year' => '2020'],
                            ['brand' => 'Kawasaki', 'model' => 'Ninja 250', 'year' => '2021'],
                        ]
                    ]
                ],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1580273916550-e323be2ae537?q=80&w=600', 'is_primary' => true],
                ],
                'variants' => [],
            ],
            [
                'category' => 'kaki-kaki',
                'product' => [
                    'name' => 'RCB VD Series Suspension (305mm Premium)',
                    'slug' => 'rcb-vd-series-suspension-305mm-premium',
                    'sku' => 'SUS-RCB-04',
                    'short_description' => 'Dual shockbreaker RCB VD Series premium dengan kompresi dan rebound yang dapat diatur.',
                    'description' => '<p>Dirancang khusus untuk kenyamanan dan kestabilan berkendara maksimal. Dibuat dengan piston alloy kokoh dan tabung gas nitrogen eksternal. Anda dapat dengan mudah mengatur kekerasan per (preload), rebound damping, dan kompresi sesuai beban berkendara Anda.</p>',
                    'price' => 1950000,
                    'compare_at_price' => 2800000,
                    'cost_price' => 1100000,
                    'weight' => 4800,
                    'stock' => 15,
                    'is_active' => true,
                    'is_featured' => true,
                    'metadata' => [
                        'compatibility' => [
                            ['brand' => 'Yamaha', 'model' => 'NMAX', 'year' => '2020'],
                            ['brand' => 'Yamaha', 'model' => 'NMAX', 'year' => '2021'],
                            ['brand' => 'Yamaha', 'model' => 'NMAX', 'year' => '2022'],
                            ['brand' => 'Yamaha', 'model' => 'NMAX', 'year' => '2023'],
                            ['brand' => 'Yamaha', 'model' => 'Aerox', 'year' => '2021'],
                            ['brand' => 'Yamaha', 'model' => 'Aerox', 'year' => '2022'],
                            ['brand' => 'Yamaha', 'model' => 'Aerox', 'year' => '2023'],
                        ]
                    ]
                ],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1558981806-ec527fa84c39?q=80&w=600', 'is_primary' => true],
                ],
                'variants' => [],
            ],
            [
                'category' => 'pengapian',
                'product' => [
                    'name' => 'NGK Iridium IX Spark Plug CPR9EAIX-9',
                    'slug' => 'ngk-iridium-ix-spark-plug-cpr9eaix-9',
                    'sku' => 'IGN-NGK-05',
                    'short_description' => 'Busi Iridium berperforma tinggi dari NGK untuk pembakaran mesin lebih sempurna.',
                    'description' => '<p>Ujung elektroda bahan iridium halus 0.6mm memastikan daya tahan tinggi dan percikan api yang stabil secara konsisten. Mempercepat respon akselerasi, menghemat bahan bakar, serta membuat starter mesin menjadi lebih mudah di pagi hari.</p>',
                    'price' => 110000,
                    'compare_at_price' => 150000,
                    'cost_price' => 50000,
                    'weight' => 50,
                    'stock' => 120,
                    'is_active' => true,
                    'is_featured' => false,
                    'metadata' => [
                        'compatibility' => [
                            ['brand' => 'Honda', 'model' => 'Beat', 'year' => '2019'],
                            ['brand' => 'Honda', 'model' => 'Beat', 'year' => '2020'],
                            ['brand' => 'Honda', 'model' => 'Beat', 'year' => '2021'],
                            ['brand' => 'Honda', 'model' => 'Beat', 'year' => '2022'],
                            ['brand' => 'Honda', 'model' => 'Beat', 'year' => '2023'],
                            ['brand' => 'Yamaha', 'model' => 'NMAX', 'year' => '2020'],
                            ['brand' => 'Yamaha', 'model' => 'NMAX', 'year' => '2021'],
                            ['brand' => 'Yamaha', 'model' => 'NMAX', 'year' => '2022'],
                            ['brand' => 'Yamaha', 'model' => 'NMAX', 'year' => '2023'],
                            ['brand' => 'Yamaha', 'model' => 'Aerox', 'year' => '2021'],
                            ['brand' => 'Yamaha', 'model' => 'Aerox', 'year' => '2022'],
                        ]
                    ]
                ],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1619642751034-765dfdf7c58e?q=80&w=600', 'is_primary' => true],
                ],
                'variants' => [],
            ],
            [
                'category' => 'oli',
                'product' => [
                    'name' => 'Motul 7100 4T 10W-40 Synthetic (1L)',
                    'slug' => 'motul-7100-4t-10w-40-synthetic-1l',
                    'sku' => 'LUB-MOTL-06',
                    'short_description' => 'Oli motor full synthetic berteknologi ester 100% untuk perlindungan mesin sport optimal.',
                    'description' => '<p>Didesain khusus untuk mesin 4-tak berkinerja tinggi. Mengurangi gesekan dalam mesin secara signifikan, meningkatkan tenaga, serta memberikan ketahanan panas yang luar biasa bahkan dalam kondisi macet parah sekalipun.</p>',
                    'price' => 230000,
                    'compare_at_price' => 260000,
                    'cost_price' => 140000,
                    'weight' => 1000,
                    'stock' => 80,
                    'is_active' => true,
                    'is_featured' => false,
                    'metadata' => [
                        'compatibility' => [
                            ['brand' => 'Honda', 'model' => 'CBR250RR', 'year' => '2020'],
                            ['brand' => 'Honda', 'model' => 'CBR250RR', 'year' => '2021'],
                            ['brand' => 'Honda', 'model' => 'CBR250RR', 'year' => '2022'],
                            ['brand' => 'Honda', 'model' => 'CBR250RR', 'year' => '2023'],
                            ['brand' => 'Yamaha', 'model' => 'R25', 'year' => '2020'],
                            ['brand' => 'Yamaha', 'model' => 'R25', 'year' => '2021'],
                            ['brand' => 'Yamaha', 'model' => 'R25', 'year' => '2022'],
                            ['brand' => 'Kawasaki', 'model' => 'Ninja 250', 'year' => '2019'],
                            ['brand' => 'Kawasaki', 'model' => 'Ninja 250', 'year' => '2020'],
                        ]
                    ]
                ],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1622445262465-2481c4574875?q=80&w=600', 'is_primary' => true],
                ],
                'variants' => [],
            ],
            [
                'category' => 'mesin',
                'product' => [
                    'name' => 'FIM Piston Kit Dome (57.3mm Pin 14)',
                    'slug' => 'fim-piston-kit-dome-57-3mm-pin-14',
                    'sku' => 'ENG-FIM-07',
                    'short_description' => 'Paket piston kit kompresi tinggi FIM diameter 57.3mm untuk bore up harian.',
                    'description' => '<p>Paket lengkap terdiri dari piston dome kompresi tinggi, ring piston, pen piston, dan clip. Sangat kuat menahan temperatur tinggi serta gesekan piston pada rpm tinggi. Menambah performa torsi motor Anda secara instan.</p>',
                    'price' => 450000,
                    'compare_at_price' => 550000,
                    'cost_price' => 220000,
                    'weight' => 350,
                    'stock' => 15,
                    'is_active' => true,
                    'is_featured' => false,
                    'metadata' => [
                        'compatibility' => [
                            ['brand' => 'Yamaha', 'model' => 'NMAX', 'year' => '2020'],
                            ['brand' => 'Yamaha', 'model' => 'NMAX', 'year' => '2021'],
                            ['brand' => 'Yamaha', 'model' => 'NMAX', 'year' => '2022'],
                            ['brand' => 'Yamaha', 'model' => 'Aerox', 'year' => '2021'],
                            ['brand' => 'Yamaha', 'model' => 'Aerox', 'year' => '2022'],
                        ]
                    ]
                ],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1532634922-8fe0b757fb13?q=80&w=600', 'is_primary' => true],
                ],
                'variants' => [],
            ],
            [
                'category' => 'knalpot',
                'product' => [
                    'name' => 'Akrapovic Slip-On Carbon Exhaust Muffler',
                    'slug' => 'akrapovic-slip-on-carbon-exhaust-muffler',
                    'sku' => 'EXH-AKRA-08',
                    'short_description' => 'Knalpot slip-on berbahan real carbon fiber dari Akrapovic untuk suara gahar berkelas.',
                    'description' => '<p>Menghasilkan raungan suara ngebass adem bulat di rpm rendah dan gahar berteriak di rpm tinggi. Terbuat dari serat karbon asli berkualitas tinggi yang efektif menahan panas secara optimal. Meningkatkan output torsi dan horsepower motor Anda secara signifikan.</p>',
                    'price' => 6800000,
                    'compare_at_price' => 7500000,
                    'cost_price' => 4000000,
                    'weight' => 2500,
                    'stock' => 5,
                    'is_active' => true,
                    'is_featured' => true,
                    'metadata' => [
                        'compatibility' => [
                            ['brand' => 'Honda', 'model' => 'CBR250RR', 'year' => '2020'],
                            ['brand' => 'Honda', 'model' => 'CBR250RR', 'year' => '2021'],
                            ['brand' => 'Honda', 'model' => 'CBR250RR', 'year' => '2022'],
                            ['brand' => 'Honda', 'model' => 'CBR250RR', 'year' => '2023'],
                            ['brand' => 'Yamaha', 'model' => 'R25', 'year' => '2020'],
                            ['brand' => 'Yamaha', 'model' => 'R25', 'year' => '2021'],
                            ['brand' => 'Yamaha', 'model' => 'R25', 'year' => '2022'],
                            ['brand' => 'Kawasaki', 'model' => 'Ninja 250', 'year' => '2019'],
                            ['brand' => 'Kawasaki', 'model' => 'Ninja 250', 'year' => '2020'],
                            ['brand' => 'Kawasaki', 'model' => 'Ninja 250', 'year' => '2021'],
                        ]
                    ]
                ],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1609630875171-b1321377ee65?q=80&w=600', 'is_primary' => true],
                ],
                'variants' => [],
            ],
            [
                'category' => 'transmisi',
                'product' => [
                    'name' => 'SSS Gold Chain & Sprocket Gear Set 428',
                    'slug' => 'sss-gold-chain-sprocket-gear-set-428',
                    'sku' => 'TRN-SSS-09',
                    'short_description' => 'Paket gir depan, belakang, dan rantai warna gold merk SSS ukuran 428.',
                    'description' => '<p>Dibuat dari baja berkualitas tinggi dengan proses hardening modern untuk daya tahan ekstra panjang. Rantai warna emas (gold) memberikan kesan mewah dan kokoh pada kaki-kaki motor Anda.</p>',
                    'price' => 550000,
                    'compare_at_price' => 650000,
                    'cost_price' => 300000,
                    'weight' => 2200,
                    'stock' => 30,
                    'is_active' => true,
                    'is_featured' => false,
                    'metadata' => [
                        'compatibility' => [
                            ['brand' => 'Yamaha', 'model' => 'R15', 'year' => '2019'],
                            ['brand' => 'Yamaha', 'model' => 'R15', 'year' => '2020'],
                            ['brand' => 'Yamaha', 'model' => 'R15', 'year' => '2021'],
                            ['brand' => 'Yamaha', 'model' => 'R15', 'year' => '2022'],
                        ]
                    ]
                ],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1616422285623-13ff0162193c?q=80&w=600', 'is_primary' => true],
                ],
                'variants' => [],
            ],
            [
                'category' => 'aksesoris',
                'product' => [
                    'name' => 'WR3 CNC Universal Handlebar Grip',
                    'slug' => 'wr3-cnc-universal-handlebar-grip',
                    'sku' => 'ACC-WR3-10',
                    'short_description' => 'Handgrip stang motor berbahan karet lembut anti-slip dipadukan dengan aksen alumunium CNC.',
                    'description' => '<p>Grip stang nyaman digenggam berlama-lama, meminimalisir getaran dan anti-slip saat kondisi hujan. Ukuran universal, cocok dipasangkan ke semua jenis stang motor.</p>',
                    'price' => 250000,
                    'compare_at_price' => 300000,
                    'cost_price' => 120000,
                    'weight' => 250,
                    'stock' => 45,
                    'is_active' => true,
                    'is_featured' => false,
                    'metadata' => [
                        'compatibility' => [
                            ['brand' => 'Honda', 'model' => 'Beat', 'year' => '2020'],
                            ['brand' => 'Honda', 'model' => 'CBR250RR', 'year' => '2021'],
                            ['brand' => 'Yamaha', 'model' => 'NMAX', 'year' => '2021'],
                            ['brand' => 'Yamaha', 'model' => 'R25', 'year' => '2021'],
                        ]
                    ]
                ],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1449426468159-d96dbf08f19f?q=80&w=600', 'is_primary' => true],
                ],
                'variants' => [],
            ]
        ];

        foreach ($productsData as $data) {
            $p = \App\Models\Product::create(array_merge($data['product'], [
                'metadata' => $data['product']['metadata']
            ]));
            
            // Attach categories
            if (isset($createdCategories[$data['category']])) {
                $p->categories()->attach($createdCategories[$data['category']]->id);
            }

            // Insert Images
            foreach ($data['images'] as $img) {
                \App\Models\ProductImage::create([
                    'product_id' => $p->id,
                    'image_path' => $img['url'],
                    'is_primary' => $img['is_primary'],
                    'sort_order' => 1,
                ]);
            }

            // Insert Variants if exists
            foreach ($data['variants'] as $v) {
                \App\Models\ProductVariant::create(array_merge($v, ['product_id' => $p->id]));
            }
        }

        // 10. Seed Active Flash Sale
        $flashSale = \App\Models\FlashSale::create([
            'name' => 'Flash Sale Gila Hari Ini',
            'start_time' => now()->subHours(2),
            'end_time' => now()->addHours(22),
            'is_active' => true,
        ]);

        // Find products for flash sale
        $fairing = \App\Models\Product::where('slug', 'yamaha-r15-fairing-kit-v3-carbon-look')->first();
        $brembo = \App\Models\Product::where('slug', 'brembo-ceramic-red-series-brake-pads-front')->first();
        $tire = \App\Models\Product::where('slug', 'michelin-pilot-street-radial-tire-140-70-17')->first();
        $shock = \App\Models\Product::where('slug', 'rcb-vd-series-suspension-305mm-premium')->first();

        if ($fairing) {
            \App\Models\FlashSaleItem::create([
                'flash_sale_id' => $flashSale->id,
                'product_id' => $fairing->id,
                'discounted_price' => 750000,
                'stock_limit' => 5,
                'stock_sold' => 2,
            ]);
        }

        if ($brembo) {
            \App\Models\FlashSaleItem::create([
                'flash_sale_id' => $flashSale->id,
                'product_id' => $brembo->id,
                'discounted_price' => 280000,
                'stock_limit' => 20,
                'stock_sold' => 6,
            ]);
        }

        if ($tire) {
            \App\Models\FlashSaleItem::create([
                'flash_sale_id' => $flashSale->id,
                'product_id' => $tire->id,
                'discounted_price' => 360000,
                'stock_limit' => 10,
                'stock_sold' => 5,
            ]);
        }

        if ($shock) {
            \App\Models\FlashSaleItem::create([
                'flash_sale_id' => $flashSale->id,
                'product_id' => $shock->id,
                'discounted_price' => 1365000,
                'stock_limit' => 8,
                'stock_sold' => 3,
            ]);
        }

        // 11. Seed Coupons
        $couponsData = [
            [
                'code' => 'GASPOL10',
                'type' => 'percentage',
                'value' => 10,
                'min_spend' => 200000,
                'max_discount' => 100000,
                'start_time' => now()->subDay(),
                'end_time' => now()->addDays(30),
                'is_active' => true,
            ],
            [
                'code' => 'MOTO50K',
                'type' => 'fixed',
                'value' => 50000,
                'min_spend' => 500000,
                'max_discount' => 50000,
                'start_time' => now()->subDay(),
                'end_time' => now()->addDays(30),
                'is_active' => true,
            ],
        ];

        foreach ($couponsData as $cp) {
            \App\Models\Coupon::create($cp);
        }
    }
}
