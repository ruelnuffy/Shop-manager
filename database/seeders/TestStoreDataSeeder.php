<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShopLocation;
use App\Models\StockMovement;
use App\Models\Tenant;
use App\Models\User;
use App\Services\CurrentTenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestStoreDataSeeder extends Seeder
{
    public function run(): void
    {
        $originalTenant = app(CurrentTenant::class)->get();

        // 1. Create or retrieve Tenant: Test Store
        $testTenant = Tenant::firstOrCreate(
            ['slug' => 'tester'],
            [
                'name' => 'Test Store',
                'subdomain' => 'tester',
                'domain' => null,
                'settings' => [
                    'currency' => 'NGN',
                    'currency_symbol' => '₦',
                    'features' => [
                        'storefront' => true,
                        'payroll' => true,
                    ],
                    'branding' => [
                        'name' => 'Test Store',
                        'tagline' => 'Quality Goods & Fast Walk-in POS Checkout',
                        'logo_url' => null,
                        'palette' => [
                            'primary' => '#2563eb',
                            'secondary' => '#0f172a',
                            'accent' => '#38bdf8',
                        ],
                        'invoice_header' => 'Test Store — Retail & POS Hub',
                    ],
                ],
                'paystack_public_key' => 'pk_test_placeholder',
                'paystack_secret_key' => 'sk_test_placeholder',
                'plan' => 'pro',
                'status' => 'active',
            ]
        );

        // 2. Assign testadmin@shopmanager.test to this tenant
        $testAdmin = User::updateOrCreate(
            ['email' => 'testadmin@shopmanager.test'],
            [
                'name' => 'Test Admin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'tenant_id' => $testTenant->id,
                'phone' => '+2348000000004',
                'address' => 'Test Store HQ, Lagos',
                'city' => 'Lagos',
                'email_verified_at' => now(),
            ]
        );
        $testAdmin->tenant_id = $testTenant->id;
        $testAdmin->save();

        if (function_exists('setPermissionsTeamId')) {
            setPermissionsTeamId($testTenant->id);
        }
        $testAdmin->syncRoles(['admin']);

        // 3. Switch CurrentTenant context to Test Store
        app(CurrentTenant::class)->set($testTenant);

        // Primary Shop Location for Test Store
        ShopLocation::updateOrCreate(
            ['slug' => 'test-store-lagos'],
            [
                'name' => 'Test Store — Flagship Lagos',
                'address' => '10 Innovation Way, Victoria Island',
                'city' => 'Lagos',
                'state' => 'Lagos State',
                'country' => 'Nigeria',
                'latitude' => 6.4281,
                'longitude' => 3.4219,
                'phone' => '+234 800 000 0004',
                'email' => 'store@shopmanager.test',
                'opening_hours' => 'Mon - Sat: 8:00 AM - 9:00 PM',
                'is_primary' => true,
            ]
        );

        // Categories for Test Store
        $catHardware = Category::updateOrCreate(
            ['slug' => 'pos-hardware'],
            [
                'name' => 'POS & Hardware',
                'description' => 'Thermal printers, barcode scanners, cash drawers, and terminal docks.',
                'image' => '/images/products/versse-snakeskin-accent-bag.png',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $catBags = Category::updateOrCreate(
            ['slug' => 'bags-accessories'],
            [
                'name' => 'Bags & Accessories',
                'description' => 'Designer leather handbags, structured tote bags, and luxury accessories.',
                'image' => '/images/products/croc-tote-3pc-set.png',
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        $catHome = Category::updateOrCreate(
            ['slug' => 'linens-living'],
            [
                'name' => 'Home & Living',
                'description' => 'Luxury plush bath suites, gift boxes, and organza-wrapped towel sets.',
                'image' => '/images/products/plush-towel-gift-box-set.png',
                'is_active' => true,
                'sort_order' => 3,
            ]
        );

        $catBeauty = Category::updateOrCreate(
            ['slug' => 'beauty-wellness'],
            [
                'name' => 'Beauty & Skincare',
                'description' => 'Glow collagen capsules and premium beauty formulations.',
                'image' => '/images/products/pearl-glow-beauty-capsules.png',
                'is_active' => true,
                'sort_order' => 4,
            ]
        );

        $catApparel = Category::updateOrCreate(
            ['slug' => 'apparel-wear'],
            [
                'name' => 'Fashion & Apparel',
                'description' => 'Everyday luxury boutique wear, cotton staples, and evening outfits.',
                'image' => '/images/products/quilted-chain-flap-bag.png',
                'is_active' => true,
                'sort_order' => 5,
            ]
        );

        // 10 Curated Test Products
        $products = [
            [
                'category_id' => $catHardware->id,
                'name' => 'Wireless 58mm Bluetooth Thermal POS Slip Printer',
                'slug' => 'wireless-58mm-bluetooth-printer',
                'description' => 'High-speed portable Bluetooth 58mm thermal receipt printer with fast tear bar. Compatible with Android RawBT and iOS.',
                'base_price' => 28500.00,
                'featured_image' => '/images/products/versse-snakeskin-accent-bag.png',
                'brand_tags' => ['Hardware', 'POS', 'Wireless'],
                'status' => 'active',
                'is_featured' => true,
                'variants' => [
                    ['size' => '58mm', 'color' => 'Matte Black', 'sku' => 'TEST-PRN-001-BLK', 'stock' => 25],
                    ['size' => '58mm', 'color' => 'Arctic White', 'sku' => 'TEST-PRN-001-WHT', 'stock' => 15],
                ],
            ],
            [
                'category_id' => $catHardware->id,
                'name' => 'Hands-Free 2D Desktop QR & Barcode Scanner',
                'slug' => 'hands-free-desktop-barcode-scanner',
                'description' => 'Omnidirectional USB & wireless barcode scanner. Reads 1D retail UPC barcodes and 2D mobile QR codes off phone screens instantly.',
                'base_price' => 34000.00,
                'featured_image' => '/images/products/versse-snakeskin-accent-bag.png',
                'brand_tags' => ['Hardware', 'Scanner', 'Till'],
                'status' => 'active',
                'is_featured' => true,
                'variants' => [
                    ['size' => 'Standard', 'color' => 'Graphite Black', 'sku' => 'TEST-SCN-001', 'stock' => 20],
                ],
            ],
            [
                'category_id' => $catHardware->id,
                'name' => 'Thermal Receipt Paper Rolls (Pack of 10)',
                'slug' => 'thermal-receipt-paper-rolls-pack-10',
                'description' => 'BPA-free premium 58mm thermal paper rolls. Produces crisp, smudge-proof printouts for walk-in till sales.',
                'base_price' => 5000.00,
                'featured_image' => '/images/products/plush-towel-gift-box-set.png',
                'brand_tags' => ['Supplies', 'Consumables'],
                'status' => 'active',
                'is_featured' => false,
                'variants' => [
                    ['size' => '58mm x 40mm', 'color' => 'White', 'sku' => 'TEST-PPR-001', 'stock' => 100],
                ],
            ],
            [
                'category_id' => $catBags->id,
                'name' => 'VERSSE Exotic Snake-Accent Handbag & Wallet Set',
                'slug' => 'test-versse-snakeskin-accent-bag',
                'description' => 'Exquisite structured top-handle handbag featuring genuine snakeskin-embossed buckle straps, polished gold turn-lock hardware, and matching zippered wallet.',
                'base_price' => 45000.00,
                'featured_image' => '/images/products/versse-snakeskin-accent-bag.png',
                'brand_tags' => ['VERSSE', 'Luxury', 'Bags'],
                'status' => 'active',
                'is_featured' => true,
                'variants' => [
                    ['size' => 'Medium', 'color' => 'Emerald Gold', 'sku' => 'TEST-BAG-001-EMG', 'stock' => 12],
                    ['size' => 'Medium', 'color' => 'Midnight Onyx', 'sku' => 'TEST-BAG-001-BLK', 'stock' => 8],
                ],
            ],
            [
                'category_id' => $catBags->id,
                'name' => 'Crocodile-Embossed 3-Piece Executive Tote Suite',
                'slug' => 'test-croc-tote-3pc-set',
                'description' => 'Stately tri-piece luxury tote ensemble featuring structured work tote, cross-body clutch, and credit card holder.',
                'base_price' => 52000.00,
                'featured_image' => '/images/products/croc-tote-3pc-set.png',
                'brand_tags' => ['Executive', 'Bags'],
                'status' => 'active',
                'is_featured' => true,
                'variants' => [
                    ['size' => 'Large 3-Piece', 'color' => 'Cognac Brown', 'sku' => 'TEST-BAG-002-BRN', 'stock' => 15],
                    ['size' => 'Large 3-Piece', 'color' => 'Jet Black', 'sku' => 'TEST-BAG-002-BLK', 'stock' => 10],
                ],
            ],
            [
                'category_id' => $catBags->id,
                'name' => 'ClassiQ Diamond-Quilted Flap Shoulder Bag',
                'slug' => 'test-quilted-flap-shoulder-bag',
                'description' => 'Timeless diamond quilting with champagne gold woven chain strap and CC turnlock closure.',
                'base_price' => 38000.00,
                'featured_image' => '/images/products/quilted-chain-flap-bag.png',
                'brand_tags' => ['ClassiQ', 'Bags'],
                'status' => 'active',
                'is_featured' => true,
                'variants' => [
                    ['size' => '25cm', 'color' => 'Ivory Cream', 'sku' => 'TEST-BAG-003-CRM', 'stock' => 18],
                    ['size' => '25cm', 'color' => 'Classic Black', 'sku' => 'TEST-BAG-003-BLK', 'stock' => 14],
                ],
            ],
            [
                'category_id' => $catHome->id,
                'name' => 'Royal Coral-Fleece Plush Towel Gift Box Suite',
                'slug' => 'test-plush-towel-gift-suite',
                'description' => 'Heavyweight 600 GSM coral fleece ultra-absorbent bath sheets and hand towels presented in a ribbon gift box.',
                'base_price' => 24500.00,
                'featured_image' => '/images/products/plush-towel-gift-box-set.png',
                'brand_tags' => ['Home', 'Living'],
                'status' => 'active',
                'is_featured' => false,
                'variants' => [
                    ['size' => '2-Piece Set', 'color' => 'Navy Blue', 'sku' => 'TEST-TWL-001-NVY', 'stock' => 30],
                    ['size' => '2-Piece Set', 'color' => 'Pastel Pink', 'sku' => 'TEST-TWL-001-PNK', 'stock' => 25],
                ],
            ],
            [
                'category_id' => $catHome->id,
                'name' => 'Organza-Wrapped Luxury Microfiber Bath & Hand Set',
                'slug' => 'test-organza-microfiber-towel-set',
                'description' => 'Silky soft, rapid dry microfiber towel pairing wrapped with signature sheer organza gift ribbon.',
                'base_price' => 19500.00,
                'featured_image' => '/images/products/organza-microfiber-towel-set.png',
                'brand_tags' => ['Home', 'Towels'],
                'status' => 'active',
                'is_featured' => false,
                'variants' => [
                    ['size' => 'Standard Set', 'color' => 'Pure White', 'sku' => 'TEST-TWL-002-WHT', 'stock' => 40],
                ],
            ],
            [
                'category_id' => $catBeauty->id,
                'name' => 'ClassiQ Pearl Glow Collagen Beauty Capsules',
                'slug' => 'test-pearl-glow-collagen-capsules',
                'description' => 'Advanced hydrolyzed marine collagen and glutathione dietary capsules for radiant skin hydration.',
                'base_price' => 22000.00,
                'featured_image' => '/images/products/pearl-glow-beauty-capsules.png',
                'brand_tags' => ['Beauty', 'Wellness'],
                'status' => 'active',
                'is_featured' => true,
                'variants' => [
                    ['size' => '60 Softgels', 'color' => 'Gold Bottle', 'sku' => 'TEST-BTY-001', 'stock' => 50],
                ],
            ],
            [
                'category_id' => $catApparel->id,
                'name' => 'Test Premium Cotton Crewneck Retail T-Shirt',
                'slug' => 'test-cotton-crewneck-tee',
                'description' => 'Heavyweight 240 GSM breathable combed cotton t-shirt with reinforced double-stitched collar.',
                'base_price' => 12500.00,
                'featured_image' => '/images/products/versse-snakeskin-accent-bag.png',
                'brand_tags' => ['Apparel', 'Basics'],
                'status' => 'active',
                'is_featured' => false,
                'variants' => [
                    ['size' => 'M', 'color' => 'Black', 'sku' => 'TEST-TSH-001-M-BLK', 'stock' => 20],
                    ['size' => 'L', 'color' => 'Black', 'sku' => 'TEST-TSH-001-L-BLK', 'stock' => 25],
                    ['size' => 'XL', 'color' => 'White', 'sku' => 'TEST-TSH-001-XL-WHT', 'stock' => 15],
                ],
            ],
        ];

        foreach ($products as $data) {
            $variants = $data['variants'];
            unset($data['variants']);

            $product = Product::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );

            foreach ($variants as $v) {
                $variant = ProductVariant::updateOrCreate(
                    ['sku' => $v['sku']],
                    [
                        'product_id' => $product->id,
                        'size' => $v['size'],
                        'color' => $v['color'],
                        'stock_quantity' => $v['stock'],
                        'is_active' => true,
                    ]
                );

                StockMovement::firstOrCreate(
                    [
                        'product_variant_id' => $variant->id,
                        'reason' => 'initial_stock_seed',
                    ],
                    [
                        'type' => 'in',
                        'quantity' => $v['stock'],
                        'previous_stock' => 0,
                        'new_stock' => $v['stock'],
                        'reference_type' => 'Manual',
                        'created_by' => $testAdmin->id,
                    ]
                );
            }
        }

        // Restore original tenant context
        if ($originalTenant) {
            app(CurrentTenant::class)->set($originalTenant);
            if (function_exists('setPermissionsTeamId')) {
                setPermissionsTeamId($originalTenant->id);
            }
        }
    }
}
