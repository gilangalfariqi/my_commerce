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

        // Super Admin gets all permissions (handled via Gate::before in AuthServiceProvider, but seeding it explicitly is good)
        $superAdminRole->syncPermissions(Permission::all());

        // Admin gets product, order, payment, shipping, and SEO permissions
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
                'value' => 'MyCommerce Enterprise',
                'description' => 'The public name of the e-commerce storefront.',
            ],
            [
                'key' => 'store_email',
                'value' => 'contact@mycommerce.com',
                'description' => 'Primary store contact email.',
            ],
            [
                'key' => 'store_phone',
                'value' => '+6281234567890',
                'description' => 'Primary customer service phone number.',
            ],
            [
                'key' => 'store_address',
                'value' => 'Sudirman Street No. 45, Central Jakarta, Indonesia',
                'description' => 'Physical store or warehouse address.',
            ],
            [
                'key' => 'rajaongkir_origin_city_id',
                'value' => '152', // Jakarta Pusat
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

        // 6. Clean existing store records to avoid duplicates
        DB::table('flash_sale_items')->delete();
        DB::table('flash_sales')->delete();
        DB::table('coupons')->delete();
        DB::table('banners')->delete();
        DB::table('product_variants')->delete();
        DB::table('product_images')->delete();
        DB::table('category_product')->delete();
        DB::table('products')->delete();
        DB::table('categories')->delete();

        // 7. Seed Banners
        $bannersData = [
            [
                'title' => 'Premium Minimalist Living',
                'subtitle' => 'Handcrafted boutique furniture designed for contemporary spaces.',
                'image_path' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?q=80&w=1200',
                'click_url' => '/products?category=furniture',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Refined Workspaces',
                'subtitle' => 'Sleek, minimalist desktop tools and mechanical essentials.',
                'image_path' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?q=80&w=1200',
                'click_url' => '/products?category=tech',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Signature Apparel',
                'subtitle' => 'Curated wardrobe collection crafted from sustainable materials.',
                'image_path' => 'https://images.unsplash.com/photo-1479064555552-3ef4979f8908?q=80&w=1200',
                'click_url' => '/products?category=apparel',
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($bannersData as $b) {
            \App\Models\Banner::create($b);
        }

        // 8. Seed Categories
        $categoriesData = [
            'furniture' => [
                'name' => 'Furniture',
                'slug' => 'furniture',
                'description' => 'Elegantly crafted tables, chairs, and loungers.',
                'image' => 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?q=80&w=300',
                'is_active' => true,
                'sort_order' => 1,
            ],
            'tech' => [
                'name' => 'Technology',
                'slug' => 'tech',
                'description' => 'Sleek tools and gadgets for modern setups.',
                'image' => 'https://images.unsplash.com/photo-1468495244123-6c6c332eeece?q=80&w=300',
                'is_active' => true,
                'sort_order' => 2,
            ],
            'apparel' => [
                'name' => 'Apparel',
                'slug' => 'apparel',
                'description' => 'Timeless cuts, luxury fabrics, everyday comfort.',
                'image' => 'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?q=80&w=300',
                'is_active' => true,
                'sort_order' => 3,
            ],
            'home-decor' => [
                'name' => 'Home Decor',
                'slug' => 'home-decor',
                'description' => 'Artisanal ceramics, vases, and scented candles.',
                'image' => 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?q=80&w=300',
                'is_active' => true,
                'sort_order' => 4,
            ],
            'accessories' => [
                'name' => 'Accessories',
                'slug' => 'accessories',
                'description' => 'Bags, leather watches, and minimal jewelry.',
                'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?q=80&w=300',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        $createdCategories = [];
        foreach ($categoriesData as $key => $c) {
            $createdCategories[$key] = \App\Models\Category::create($c);
        }

        // 9. Seed Products & Variants
        $productsData = [
            [
                'category' => 'furniture',
                'product' => [
                    'name' => 'Minimalist Oak Study Desk',
                    'slug' => 'minimalist-oak-study-desk',
                    'sku' => 'FRN-DESK-01',
                    'short_description' => 'Solid white oak study desk with clean lines and integrated drawer.',
                    'description' => '<p>Crafted entirely from responsibly sourced sustainable white oak, this study desk is a testament to minimalist design. Features a spacious smooth desktop surface, soft-close hidden drawers, and a solid timber framework designed to last generations.</p><p>Perfect for remote work stations, creative studios, or home libraries.</p>',
                    'price' => 2499000,
                    'compare_at_price' => 2999000,
                    'cost_price' => 1200000,
                    'weight' => 25000,
                    'stock' => 15,
                    'is_active' => true,
                    'is_featured' => true,
                ],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1518455027359-f3f8164ba6bd?q=80&w=600', 'is_primary' => true],
                    ['url' => 'https://images.unsplash.com/photo-1486946255434-2466348c2166?q=80&w=600', 'is_primary' => false],
                ],
                'variants' => [
                    ['name' => 'Oakwood 120cm', 'sku' => 'FRN-DESK-01-120', 'price' => 2499000, 'stock' => 10, 'weight' => 25000],
                    ['name' => 'Dark Walnut 140cm', 'sku' => 'FRN-DESK-01-140', 'price' => 2899000, 'stock' => 5, 'weight' => 30000],
                ],
            ],
            [
                'category' => 'furniture',
                'product' => [
                    'name' => 'Luxury Velvet Lounge Chair',
                    'slug' => 'luxury-velvet-lounge-chair',
                    'sku' => 'FRN-CHAIR-02',
                    'short_description' => 'Ultra-comfortable lounge chair in premium plush velvet upholstery.',
                    'description' => '<p>Sink into the deep cushioning of our custom-made Velvet Lounge Chair. Upholstered in premium commercial-grade plush velvet, with a steel matte gold base frame. Perfectly balances comfort and refined class.</p>',
                    'price' => 4500000,
                    'compare_at_price' => null,
                    'cost_price' => 2100000,
                    'weight' => 18000,
                    'stock' => 8,
                    'is_active' => true,
                    'is_featured' => true,
                ],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1567538096630-e0c55bd6374c?q=80&w=600', 'is_primary' => true],
                ],
                'variants' => [
                    ['name' => 'Emerald Green', 'sku' => 'FRN-CHAIR-02-GRN', 'price' => 4500000, 'stock' => 4, 'weight' => 18000],
                    ['name' => 'Royal Blue', 'sku' => 'FRN-CHAIR-02-BLU', 'price' => 4500000, 'stock' => 4, 'weight' => 18000],
                ],
            ],
            [
                'category' => 'tech',
                'product' => [
                    'name' => 'Sleek Mechanical Keyboard',
                    'slug' => 'sleek-mechanical-keyboard',
                    'sku' => 'TCH-KEYB-01',
                    'short_description' => '75% layout custom wireless mechanical keyboard with high-grade aluminum case.',
                    'description' => '<p>Experience tactile typing perfection. Featuring dynamic Bluetooth 5.1 / 2.4Ghz / USB-C wired connection options, hot-swappable switches, and factory-lubed stabilizers. Encased in a beautiful CNC anodized aluminum shell.</p>',
                    'price' => 1200000,
                    'compare_at_price' => 1500000,
                    'cost_price' => 600000,
                    'weight' => 1200,
                    'stock' => 30,
                    'is_active' => true,
                    'is_featured' => true,
                ],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?q=80&w=600', 'is_primary' => true],
                ],
                'variants' => [
                    ['name' => 'Linear Red Switches', 'sku' => 'TCH-KEYB-01-RED', 'price' => 1200000, 'stock' => 15, 'weight' => 1200],
                    ['name' => 'Tactile Brown Switches', 'sku' => 'TCH-KEYB-01-BRW', 'price' => 1200000, 'stock' => 15, 'weight' => 1200],
                ],
            ],
            [
                'category' => 'tech',
                'product' => [
                    'name' => 'Aluminum Laptop Stand',
                    'slug' => 'aluminum-laptop-stand',
                    'sku' => 'TCH-STAND-02',
                    'short_description' => 'Ergonomic brushed aluminum cooling stand for laptops up to 16 inches.',
                    'description' => '<p>Sleek, ergonomic, and robust. This aluminum laptop stand raises your screen to eye level to improve posture and reduce neck strain. Silicone pads protect your device, and open back vents maximize cooling airflow.</p>',
                    'price' => 450000,
                    'compare_at_price' => null,
                    'cost_price' => 200000,
                    'weight' => 800,
                    'stock' => 50,
                    'is_active' => true,
                    'is_featured' => false,
                ],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?q=80&w=600', 'is_primary' => true],
                ],
                'variants' => [],
            ],
            [
                'category' => 'apparel',
                'product' => [
                    'name' => 'Minimalist Linen Overshirt',
                    'slug' => 'minimalist-linen-overshirt',
                    'sku' => 'APR-SHRT-01',
                    'short_description' => 'Breathable linen overshirt with a relaxed classic fit.',
                    'description' => '<p>Woven from pure organic French flax linen. Preshrunk and garment-washed for a soft hand-feel from day one. Features horn buttons and flat chest utility pockets. Perfect for smart-casual summer layering.</p>',
                    'price' => 399000,
                    'compare_at_price' => null,
                    'cost_price' => 150000,
                    'weight' => 350,
                    'stock' => 40,
                    'is_active' => true,
                    'is_featured' => true,
                ],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1598033129183-c4f50c736f10?q=80&w=600', 'is_primary' => true],
                ],
                'variants' => [
                    ['name' => 'Beige M', 'sku' => 'APR-SHRT-01-BEM', 'price' => 399000, 'stock' => 10, 'weight' => 350],
                    ['name' => 'Beige L', 'sku' => 'APR-SHRT-01-BEL', 'price' => 399000, 'stock' => 10, 'weight' => 350],
                    ['name' => 'Black M', 'sku' => 'APR-SHRT-01-BKM', 'price' => 399000, 'stock' => 10, 'weight' => 350],
                    ['name' => 'Black L', 'sku' => 'APR-SHRT-01-BKL', 'price' => 399000, 'stock' => 10, 'weight' => 350],
                ],
            ],
            [
                'category' => 'apparel',
                'product' => [
                    'name' => 'Classic Leather Chelsea Boots',
                    'slug' => 'classic-leather-chelsea-boots',
                    'sku' => 'APR-BOOT-02',
                    'short_description' => 'Artisanal Italian leather Chelsea boots with durable Goodyear welt sole.',
                    'description' => '<p>Crafted by hand using full-grain Italian calfskin leather. Featuring flexible elastic side panels, a pull tab for easy wear, and a resoleable Goodyear welt construction with custom cushioned leather insoles.</p>',
                    'price' => 1800000,
                    'compare_at_price' => 2200000,
                    'cost_price' => 900000,
                    'weight' => 1500,
                    'stock' => 12,
                    'is_active' => true,
                    'is_featured' => false,
                ],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1638247025967-b4e38f787b76?q=80&w=600', 'is_primary' => true],
                ],
                'variants' => [
                    ['name' => 'Tan Brown 41', 'sku' => 'APR-BOOT-02-T41', 'price' => 1800000, 'stock' => 3, 'weight' => 1500],
                    ['name' => 'Tan Brown 42', 'sku' => 'APR-BOOT-02-T42', 'price' => 1800000, 'stock' => 3, 'weight' => 1500],
                    ['name' => 'Black 41', 'sku' => 'APR-BOOT-02-B41', 'price' => 1800000, 'stock' => 3, 'weight' => 1500],
                    ['name' => 'Black 42', 'sku' => 'APR-BOOT-02-B42', 'price' => 1800000, 'stock' => 3, 'weight' => 1500],
                ],
            ],
            [
                'category' => 'home-decor',
                'product' => [
                    'name' => 'Ceramic Flower Vase Set',
                    'slug' => 'ceramic-flower-vase-set',
                    'sku' => 'DEC-VASE-01',
                    'short_description' => 'Set of 3 matte finish textured ceramic flower vases.',
                    'description' => '<p>This beautiful trio of vases features diverse organic silhouettes with a coarse sand texture finish. Display them together as a centerpiece or scatter them across shelves for a minimalist art-gallery feel.</p>',
                    'price' => 280000,
                    'compare_at_price' => null,
                    'cost_price' => 100000,
                    'weight' => 2000,
                    'stock' => 25,
                    'is_active' => true,
                    'is_featured' => false,
                ],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1578500494198-246f612d3b3d?q=80&w=600', 'is_primary' => true],
                ],
                'variants' => [],
            ],
            [
                'category' => 'home-decor',
                'product' => [
                    'name' => 'Scented Soy Wax Candle',
                    'slug' => 'scented-soy-wax-candle',
                    'sku' => 'DEC-CANDL-02',
                    'short_description' => 'Premium slow-burning scented candle in a textured stone vessel.',
                    'description' => '<p>Poured by hand using 100% natural soy wax, custom essential oils, and an organic wood wick that crackles softly when lit. Provides a clean 45-hour burn time inside a reusable concrete jar.</p>',
                    'price' => 150000,
                    'compare_at_price' => null,
                    'cost_price' => 60000,
                    'weight' => 500,
                    'stock' => 100,
                    'is_active' => true,
                    'is_featured' => false,
                ],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1603006905003-be475563bc59?q=80&w=600', 'is_primary' => true],
                ],
                'variants' => [
                    ['name' => 'Lavender & Chamomile', 'sku' => 'DEC-CAND-LAV', 'price' => 150000, 'stock' => 40, 'weight' => 500],
                    ['name' => 'Sandalwood & Leather', 'sku' => 'DEC-CAND-SND', 'price' => 150000, 'stock' => 30, 'weight' => 500],
                    ['name' => 'Vanilla Mint', 'sku' => 'DEC-CAND-VNL', 'price' => 150000, 'stock' => 30, 'weight' => 500],
                ],
            ],
        ];

        foreach ($productsData as $data) {
            $p = \App\Models\Product::create($data['product']);
            
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

            // Insert Variants
            foreach ($data['variants'] as $v) {
                \App\Models\ProductVariant::create(array_merge($v, ['product_id' => $p->id]));
            }
        }

        // 10. Seed Active Flash Sale
        $flashSale = \App\Models\FlashSale::create([
            'name' => 'Midnight Horizon Sale',
            'start_time' => now()->subHours(2),
            'end_time' => now()->addHours(22),
            'is_active' => true,
        ]);

        // Find products for flash sale
        $keyboard = \App\Models\Product::where('slug', 'sleek-mechanical-keyboard')->first();
        $candle = \App\Models\Product::where('slug', 'scented-soy-wax-candle')->first();

        if ($keyboard) {
            \App\Models\FlashSaleItem::create([
                'flash_sale_id' => $flashSale->id,
                'product_id' => $keyboard->id,
                'discounted_price' => 799000,
                'stock_limit' => 10,
                'stock_sold' => 3,
            ]);
        }

        if ($candle) {
            \App\Models\FlashSaleItem::create([
                'flash_sale_id' => $flashSale->id,
                'product_id' => $candle->id,
                'discounted_price' => 89000,
                'stock_limit' => 30,
                'stock_sold' => 12,
            ]);
        }

        // 11. Seed Coupons
        $couponsData = [
            [
                'code' => 'WELCOME10',
                'type' => 'percentage',
                'value' => 10,
                'min_spend' => 200000,
                'max_discount' => 50000,
                'start_time' => now()->subDay(),
                'end_time' => now()->addDays(30),
                'is_active' => true,
            ],
            [
                'code' => 'PREMIUM50',
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
