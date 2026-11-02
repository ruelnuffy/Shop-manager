<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShopLocation;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Seeder;

class StoreDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        // 1. Shop Location
        ShopLocation::where('is_primary', true)->delete();
        ShopLocation::updateOrCreate(
            ['slug' => 'jessi-boutique-jos'],
            [
                'name' => "Jessi's ClassiQ Store — Flagship Boutique Jos",
                'address' => 'Across Chrisbest Plaza, West of Mines',
                'city' => 'Jos',
                'state' => 'Plateau State',
                'country' => 'Nigeria',
                'latitude' => 9.9211000,
                'longitude' => 8.8825000,
                'phone' => '+234 801 234 5678',
                'email' => 'boutique@jessiclassiq.com',
                'opening_hours' => 'Mon - Sat: 9:00 AM - 8:00 PM, Sun: 12:00 PM - 6:00 PM',
                'is_primary' => true,
            ]
        );

        // 2. Categories
        $bags = Category::updateOrCreate(
            ['slug' => 'bags'],
            [
                'name' => 'Bags',
                'description' => 'Handcrafted designer totes, crocodile sets, quilted shoulder bags, and luxury clutches.',
                'image' => '/images/products/croc-tote-3pc-set.png',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $clothes = Category::updateOrCreate(
            ['slug' => 'clothes'],
            [
                'name' => 'Clothes',
                'description' => 'Sophisticated boutique dresses, silk evening wear, co-ords, and atelier creations.',
                'image' => 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        $shoes = Category::updateOrCreate(
            ['slug' => 'shoes'],
            [
                'name' => 'Shoes',
                'description' => 'Elegant stiletto pumps, chic mules, and stylish footwear for every celebration.',
                'image' => 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
                'sort_order' => 3,
            ]
        );

        $homeLiving = Category::updateOrCreate(
            ['slug' => 'home-living'],
            [
                'name' => 'Luxury Towels & Linens',
                'description' => 'Plush coral fleece and organza-wrapped microfiber bath and hand towel suites.',
                'image' => '/images/products/plush-towel-gift-box-set.png',
                'is_active' => true,
                'sort_order' => 3,
            ]
        );

        $beauty = Category::updateOrCreate(
            ['slug' => 'beauty-wellness'],
            [
                'name' => 'Beauty & Wellness',
                'description' => 'Pearl glow skincare collagen capsules and radiant boutique beauty formulations.',
                'image' => '/images/products/pearl-glow-beauty-capsules.png',
                'is_active' => true,
                'sort_order' => 4,
            ]
        );

        // 3. Authentic Products with Videos and Posters
        $productsData = [
            [
                'category_id' => $bags->id,
                'name' => 'VERSSE Exotic Snake-Accent Handbag & Wallet Set',
                'slug' => 'versse-snakeskin-accent-bag-set',
                'description' => 'Exquisite structured top-handle handbag featuring genuine snakeskin-embossed buckle straps, polished gold turn-lock hardware, and an accompanying full-length zippered wallet.',
                'base_price' => 45000.00,
                'featured_image' => '/images/products/versse-snakeskin-accent-bag.png',
                'video_url' => '/videos/products/versse-snakeskin-accent-bag.mp4',
                'images' => [
                    '/images/products/versse-snakeskin-accent-bag.png',
                ],
                'brand_tags' => ['VERSSE', 'Snake Print', 'Handbag Suite', 'Matching Wallet', 'Gold Hardware'],
                'status' => 'active',
                'is_featured' => true,
                'variants' => [
                    ['size' => 'Medium', 'color' => 'Noir Black', 'sku' => 'JC-VRS-BLK', 'stock' => 12],
                    ['size' => 'Medium', 'color' => 'Khaki Nude', 'sku' => 'JC-VRS-KHK', 'stock' => 10],
                    ['size' => 'Medium', 'color' => 'Saddle Camel', 'sku' => 'JC-VRS-CAM', 'stock' => 8],
                ],
            ],
            [
                'category_id' => $bags->id,
                'name' => 'Birkin-Style Structured Padlock Tote with Silk Twilly',
                'slug' => 'birkin-style-padlock-tote-twilly',
                'description' => 'Iconic structured tote crafted in rich pebbled leather, fitted with working padlock and key clochette, paired with an artisan Parisian silk handle twilly scarf.',
                'base_price' => 58000.00,
                'featured_image' => '/images/products/birkin-style-padlock-tote.png',
                'video_url' => '/videos/products/birkin-style-padlock-tote.mp4',
                'images' => [
                    '/images/products/birkin-style-padlock-tote.png',
                ],
                'brand_tags' => ['Atelier Birkin', 'Gold Padlock', 'Silk Twilly', 'Pebbled Grain', 'Statement'],
                'status' => 'active',
                'is_featured' => true,
                'variants' => [
                    ['size' => '30cm', 'color' => 'Scarlet Red', 'sku' => 'JC-BRK-RED', 'stock' => 7],
                    ['size' => '30cm', 'color' => 'Noir Black', 'sku' => 'JC-BRK-BLK', 'stock' => 9],
                ],
            ],
            [
                'category_id' => $bags->id,
                'name' => 'Kelly Bicolor Belted Mini Satchel',
                'slug' => 'kelly-bicolor-belted-mini-satchel',
                'description' => 'Sophisticated contrast two-tone mini tote with horizontal belted closure, top arch handle, detachable leather shoulder strap, and polished gold clasp.',
                'base_price' => 42000.00,
                'featured_image' => '/images/products/kelly-bicolor-belted-bag.png',
                'video_url' => '/videos/products/kelly-bicolor-belted-bag.mp4',
                'images' => [
                    '/images/products/kelly-bicolor-belted-bag.png',
                ],
                'brand_tags' => ['Kelly Style', 'Bicolor Contrast', 'Turn-Lock', 'Crossbody'],
                'status' => 'active',
                'is_featured' => true,
                'variants' => [
                    ['size' => 'Mini (22cm)', 'color' => 'Ivory & Camel Tan', 'sku' => 'JC-KLY-IVCAM', 'stock' => 14],
                    ['size' => 'Mini (22cm)', 'color' => 'Noir Black', 'sku' => 'JC-KLY-BLK', 'stock' => 11],
                    ['size' => 'Mini (22cm)', 'color' => 'Navy & Chalk', 'sku' => 'JC-KLY-NVY', 'stock' => 8],
                ],
            ],
            [
                'category_id' => $bags->id,
                'name' => 'ClassiQ Diamond-Quilted Flap Shoulder Bag',
                'slug' => 'classiq-diamond-quilted-flap-shoulder-bag',
                'description' => 'Timeless diamond quilting in supple lambskin-feel vegan leather with double-interwoven gold curb chain and iconic interlocking turn-lock closure.',
                'base_price' => 38000.00,
                'featured_image' => '/images/products/quilted-chain-flap-bag.png',
                'video_url' => '/videos/products/quilted-chain-flap-bag.mp4',
                'images' => [
                    '/images/products/quilted-chain-flap-bag.png',
                ],
                'brand_tags' => ['Diamond Quilt', 'Gold Woven Chain', 'Double Flap', 'Parisian Chic'],
                'status' => 'active',
                'is_featured' => true,
                'variants' => [
                    ['size' => 'Classic', 'color' => 'Noir Black', 'sku' => 'JC-QFL-BLK', 'stock' => 15],
                    ['size' => 'Classic', 'color' => 'Nude Beige', 'sku' => 'JC-QFL-BEG', 'stock' => 10],
                ],
            ],
            [
                'category_id' => $bags->id,
                'name' => 'Quilted Vanity Box Top-Handle Trunk Case',
                'slug' => 'quilted-vanity-box-top-handle-case',
                'description' => 'Architectural vanity beauty case with rigid gold metallic arched handle, two-way wraparound zipper, flower blossom hardware, and roomy lined cosmetic compartment.',
                'base_price' => 36000.00,
                'featured_image' => '/images/products/quilted-vanity-box-case.png',
                'video_url' => '/videos/products/quilted-vanity-box-case.mp4',
                'images' => [
                    '/images/products/quilted-vanity-box-case.png',
                ],
                'brand_tags' => ['Vanity Case', 'Metallic Arch Handle', 'Quilted Trunk', 'Pastel Luxe'],
                'status' => 'active',
                'is_featured' => true,
                'variants' => [
                    ['size' => 'Standard', 'color' => 'Blush Pink', 'sku' => 'JC-VNT-PNK', 'stock' => 10],
                    ['size' => 'Standard', 'color' => 'Powder Blue', 'sku' => 'JC-VNT-BLU', 'stock' => 8],
                    ['size' => 'Standard', 'color' => 'Wine Bordeaux', 'sku' => 'JC-VNT-WIN', 'stock' => 6],
                    ['size' => 'Standard', 'color' => 'Noir Black', 'sku' => 'JC-VNT-BLK', 'stock' => 12],
                ],
            ],
            [
                'category_id' => $bags->id,
                'name' => 'LYN Signature Gold-Lettered Mini Handbag Set',
                'slug' => 'lyn-signature-mini-handbag-set',
                'description' => 'Chic structured mini tote emblazoned with gilded LYN letter plaque and dual gold turn-locks. Includes a complimentary portable rechargeable beauty fan.',
                'base_price' => 39500.00,
                'featured_image' => '/images/products/lyn-mini-designer-bag.png',
                'video_url' => '/videos/products/lyn-mini-designer-bag.mp4',
                'images' => [
                    '/images/products/lyn-mini-designer-bag.png',
                ],
                'brand_tags' => ['LYN', 'Gold Letters', 'Mini Bag', 'Includes Beauty Fan', 'Summer Edit'],
                'status' => 'active',
                'is_featured' => true,
                'variants' => [
                    ['size' => 'Mini', 'color' => 'Cherry Red', 'sku' => 'JC-LYN-RED', 'stock' => 9],
                    ['size' => 'Mini', 'color' => 'Caramel Tan', 'sku' => 'JC-LYN-TAN', 'stock' => 11],
                    ['size' => 'Mini', 'color' => 'Sky Blue', 'sku' => 'JC-LYN-SKY', 'stock' => 7],
                    ['size' => 'Mini', 'color' => 'Noir Black', 'sku' => 'JC-LYN-BLK', 'stock' => 10],
                ],
            ],
            [
                'category_id' => $bags->id,
                'name' => 'Crocodile-Embossed 3-Piece Executive Suite',
                'slug' => 'crocodile-embossed-3-piece-tote-suite',
                'description' => 'The ultimate 3-in-1 boutique ensemble: high-capacity croc-embossed tote with reinforced shoulder strap, matching structured envelope pouch, and zip-around currency wallet.',
                'base_price' => 52000.00,
                'featured_image' => '/images/products/croc-tote-3pc-set.png',
                'video_url' => '/videos/products/croc-tote-3pc-set.mp4',
                'images' => [
                    '/images/products/croc-tote-3pc-set.png',
                ],
                'brand_tags' => ['3-in-1 Suite', 'Crocodile Embossed', 'Executive Tote', 'Wallet Set'],
                'status' => 'active',
                'is_featured' => true,
                'variants' => [
                    ['size' => '3-Piece Set', 'color' => 'Camel Cognac', 'sku' => 'JC-CRC-CAM', 'stock' => 14],
                    ['size' => '3-Piece Set', 'color' => 'Midnight Noir', 'sku' => 'JC-CRC-BLK', 'stock' => 12],
                ],
            ],
            [
                'category_id' => $bags->id,
                'name' => 'Floral Monogram Mini Boston Barrel Bag',
                'slug' => 'floral-monogram-mini-boston-barrel-bag',
                'description' => 'Playful barrel duffle silhouette adorned with stamped clover monogram floral emblems, rolled top handles, and dual-direction gold zipper.',
                'base_price' => 34000.00,
                'featured_image' => '/images/products/floral-monogram-barrel-bag.png',
                'video_url' => '/videos/products/floral-monogram-barrel-bag.mp4',
                'images' => [
                    '/images/products/floral-monogram-barrel-bag.png',
                ],
                'brand_tags' => ['Barrel Duffle', 'Floral Monogram', 'Boston Bag', 'Chic'],
                'status' => 'active',
                'is_featured' => false,
                'variants' => [
                    ['size' => 'Compact', 'color' => 'Pastel Rose', 'sku' => 'JC-BRL-PNK', 'stock' => 8],
                    ['size' => 'Compact', 'color' => 'Noir Charcoal', 'sku' => 'JC-BRL-BLK', 'stock' => 10],
                ],
            ],
            [
                'category_id' => $bags->id,
                'name' => 'Crescent Gold-Arch Clasp Structured Crossbody',
                'slug' => 'crescent-arch-clasp-crossbody',
                'description' => 'Curved saddle silhouette with textured snakeskin flap and sleek minimalist gold crescent smile-bar closure. Fitted with rigid rolled handle and crossbody strap.',
                'base_price' => 35000.00,
                'featured_image' => '/images/products/crescent-arch-crossbody-bag.png',
                'video_url' => '/videos/products/crescent-arch-crossbody-bag.mp4',
                'images' => [
                    '/images/products/crescent-arch-crossbody-bag.png',
                ],
                'brand_tags' => ['Crescent Bar', 'Saddle Bag', 'Crossbody', 'Minimalist'],
                'status' => 'active',
                'is_featured' => false,
                'variants' => [
                    ['size' => 'One Size', 'color' => 'Chalk White', 'sku' => 'JC-CRS-WHT', 'stock' => 9],
                    ['size' => 'One Size', 'color' => 'Noir Black', 'sku' => 'JC-CRS-BLK', 'stock' => 11],
                    ['size' => 'One Size', 'color' => 'Nude Sand', 'sku' => 'JC-CRS-SND', 'stock' => 8],
                    ['size' => 'One Size', 'color' => 'Rich Chocolate', 'sku' => 'JC-CRS-BRN', 'stock' => 6],
                ],
            ],
            [
                'category_id' => $bags->id,
                'name' => 'Baguette Croc-Embossed Envelope Clutch',
                'slug' => 'baguette-croc-envelope-clutch',
                'description' => 'Sleek elongated horizontal silhouette in glossy crocodile embossed leather with asymmetrical strap clasp and polished gold turn-lock.',
                'base_price' => 28000.00,
                'featured_image' => '/images/products/croc-baguette-envelope-clutch.png',
                'video_url' => '/videos/products/croc-baguette-envelope-clutch.mp4',
                'images' => [
                    '/images/products/croc-baguette-envelope-clutch.png',
                ],
                'brand_tags' => ['Baguette', 'Evening Clutch', 'Turn-Lock', 'Crocodile Texture'],
                'status' => 'active',
                'is_featured' => false,
                'variants' => [
                    ['size' => 'Slim', 'color' => 'Mocha Tan', 'sku' => 'JC-BGT-TAN', 'stock' => 12],
                    ['size' => 'Slim', 'color' => 'Noir Black', 'sku' => 'JC-BGT-BLK', 'stock' => 15],
                    ['size' => 'Slim', 'color' => 'Royal Burgundy', 'sku' => 'JC-BGT-BUR', 'stock' => 7],
                ],
            ],
            [
                'category_id' => $homeLiving->id,
                'name' => 'Royal Coral-Fleece Plush Towel Gift Box Suite',
                'slug' => 'royal-coral-fleece-towel-gift-suite',
                'description' => 'Ultra-absorbent high-density coral fleece towel presentation set. Includes bath sheets and matching hand towels individually wrapped with gold satin ribbon in a luxury presentation gift box.',
                'base_price' => 24500.00,
                'featured_image' => '/images/products/plush-towel-gift-box-set.png',
                'video_url' => '/videos/products/plush-towel-gift-box-set.mp4',
                'images' => [
                    '/images/products/plush-towel-gift-box-set.png',
                ],
                'brand_tags' => ['Gift Boxed', 'Coral Fleece', 'Gold Ribbon', 'Bath & Hand Towels'],
                'status' => 'active',
                'is_featured' => true,
                'variants' => [
                    ['size' => 'Gift Box Suite', 'color' => 'Lavender Lilac', 'sku' => 'JC-TWL-LIL', 'stock' => 16],
                    ['size' => 'Gift Box Suite', 'color' => 'Ocean Blue', 'sku' => 'JC-TWL-BLU', 'stock' => 14],
                    ['size' => 'Gift Box Suite', 'color' => 'Vanilla Cream', 'sku' => 'JC-TWL-CRM', 'stock' => 20],
                    ['size' => 'Gift Box Suite', 'color' => 'Slate Grey', 'sku' => 'JC-TWL-GRY', 'stock' => 12],
                ],
            ],
            [
                'category_id' => $homeLiving->id,
                'name' => 'Organza-Wrapped Luxury Microfiber Bath & Hand Set',
                'slug' => 'organza-wrapped-microfiber-towel-set',
                'description' => 'Feather-soft cloud microfiber towel bundle packaged inside a sheer golden drawstring organza gift pouch with satin bows. Perfect for bridal gifts, housewarmings, and guest suites.',
                'base_price' => 19500.00,
                'featured_image' => '/images/products/organza-microfiber-towel-set.png',
                'video_url' => '/videos/products/organza-microfiber-towel-set.mp4',
                'images' => [
                    '/images/products/organza-microfiber-towel-set.png',
                ],
                'brand_tags' => ['Organza Gift Bag', 'Microfiber Softness', 'Bridal Gift', 'Luxe Linens'],
                'status' => 'active',
                'is_featured' => false,
                'variants' => [
                    ['size' => '2-Piece Set', 'color' => 'Dusty Rose', 'sku' => 'JC-ORG-PNK', 'stock' => 15],
                    ['size' => '2-Piece Set', 'color' => 'Royal Cobalt', 'sku' => 'JC-ORG-BLU', 'stock' => 12],
                    ['size' => '2-Piece Set', 'color' => 'Golden Honey', 'sku' => 'JC-ORG-HON', 'stock' => 14],
                    ['size' => '2-Piece Set', 'color' => 'Pure Ivory', 'sku' => 'JC-ORG-IVR', 'stock' => 18],
                ],
            ],
            [
                'category_id' => $beauty->id,
                'name' => 'ClassiQ Pearl Glow Collagen Beauty Capsules',
                'slug' => 'classiq-pearl-glow-collagen-capsules',
                'description' => 'Potent antioxidant and hydrolysed collagen pearl glow softgels formulated to support skin radiance, hydration, and cellular rejuvenation from within.',
                'base_price' => 22000.00,
                'featured_image' => '/images/products/pearl-glow-beauty-capsules.png',
                'video_url' => '/videos/products/pearl-glow-beauty-capsules.mp4',
                'images' => [
                    '/images/products/pearl-glow-beauty-capsules.png',
                ],
                'brand_tags' => ['Pearl Glow', 'Collagen', 'Radiance', 'Atelier Wellness'],
                'status' => 'active',
                'is_featured' => false,
                'variants' => [
                    ['size' => '60 Softgels Jar', 'color' => 'Pearl White', 'sku' => 'JC-CAP-60', 'stock' => 25],
                ],
            ],
            [
                'category_id' => $clothes->id,
                'name' => 'Blush Silk Evening Gown',
                'slug' => 'blush-silk-evening-gown',
                'description' => 'A breathtaking floor-length rose silk gown with an elegant drape and flattering silhouette crafted in our Jos atelier.',
                'base_price' => 48500.00,
                'featured_image' => 'https://images.unsplash.com/photo-1566174053879-31528523f8ae?auto=format&fit=crop&w=800&q=80',
                'video_url' => '/videos/shop/boutique-lights-apparel.mp4',
                'images' => [
                    'https://images.unsplash.com/photo-1566174053879-31528523f8ae?auto=format&fit=crop&w=800&q=80',
                ],
                'brand_tags' => ['Evening', 'Silk', 'Rose Pink', 'ClassiQ Exclusive'],
                'status' => 'active',
                'is_featured' => true,
                'variants' => [
                    ['size' => 'S', 'color' => 'Blush Pink', 'sku' => 'JC-DRS-001-S', 'stock' => 12],
                    ['size' => 'M', 'color' => 'Blush Pink', 'sku' => 'JC-DRS-001-M', 'stock' => 18],
                    ['size' => 'L', 'color' => 'Blush Pink', 'sku' => 'JC-DRS-001-L', 'stock' => 8],
                ],
            ],
            [
                'category_id' => $shoes->id,
                'name' => 'Aura Rose Stiletto Pumps',
                'slug' => 'aura-rose-stiletto-pumps',
                'description' => 'Pointed-toe stiletto pumps in patent blush rose with padded cushioned insole for day-to-night glamour.',
                'base_price' => 32000.00,
                'featured_image' => 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?auto=format&fit=crop&w=800&q=80',
                'video_url' => null,
                'images' => [
                    'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?auto=format&fit=crop&w=800&q=80',
                ],
                'brand_tags' => ['Pumps', 'Patent Leather', 'Heels'],
                'status' => 'active',
                'is_featured' => false,
                'variants' => [
                    ['size' => 'EU 38', 'color' => 'Blush Rose', 'sku' => 'JC-SHOE-001-38', 'stock' => 6],
                    ['size' => 'EU 39', 'color' => 'Blush Rose', 'sku' => 'JC-SHOE-001-39', 'stock' => 9],
                    ['size' => 'EU 40', 'color' => 'Blush Rose', 'sku' => 'JC-SHOE-001-40', 'stock' => 5],
                ],
            ],
        ];

        foreach ($productsData as $data) {
            $variants = $data['variants'];
            unset($data['variants']);

            $product = Product::updateOrCreate(['slug' => $data['slug']], $data);

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

                // Initial stock movement record
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
                        'created_by' => $admin?->id,
                    ]
                );
            }
        }
    }
}
