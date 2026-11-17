<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class CpanelDeployController extends Controller
{
    /**
     * Handle cPanel deployment operations (migrations, seeders, storage linking, caching).
     */
    public function __invoke(Request $request): mixed
    {
        $expectedKey = config('app.cpanel_key', env('CPANEL_SETUP_KEY', 'jessi-staging-2026'));
        $providedKey = (string) ($request->query('key') ?? $request->input('key') ?? '');

        $isAdmin = Auth::check() && method_exists(Auth::user(), 'isAdmin') && Auth::user()->isAdmin();

        // Security gate: allow if correct key provided or currently logged in as administrator
        if (! $isAdmin && (! hash_equals($expectedKey, $providedKey) || empty($expectedKey))) {
            if ($request->wantsJson() || $request->query('format') === 'json') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized. Invalid or missing deployment key.',
                ], 403);
            }

            return response()->view('cpanel.unauthorized', [
                'providedKey' => $providedKey,
            ], 403);
        }

        $action = (string) ($request->query('action') ?? $request->input('action') ?? '');
        $results = [];

        if (! empty($action)) {
            $results = $this->runAction($action);

            if ($request->wantsJson() || $request->query('format') === 'json') {
                return response()->json([
                    'status' => 'success',
                    'action' => $action,
                    'results' => $results,
                ]);
            }
        }

        $diagnostics = $this->runDiagnostics();

        return view('cpanel.setup', [
            'diagnostics' => $diagnostics,
            'results' => $results,
            'activeAction' => $action,
            'key' => $providedKey ?: $expectedKey,
        ]);
    }

    /**
     * Execute requested Artisan or filesystem action.
     *
     * @return array<string, mixed>
     */
    protected function runAction(string $action): array
    {
        $output = '';
        $success = true;
        $error = null;

        try {
            switch ($action) {
                case 'migrate':
                    Artisan::call('migrate', ['--force' => true]);
                    $output = Artisan::output();
                    break;

                case 'seed_decency_products':
                    $tenantId = 4;
                    $now = now();
                    $items = [
                        ['name' => 'Copper Electric Hot Comb', 'category' => 'Beauty & Wellness', 'price' => 7500, 'image' => '/storage/tenants/4/products/copper-hot-comb.jpg', 'description' => 'Ceramic-heated copper straightening comb with adjustable temperature settings and 360° rotating cord.'],
                        ['name' => 'Scarf-Handle Leather Tote Bag', 'category' => 'Bags', 'price' => 20000, 'image' => '/storage/tenants/4/products/scarf-handle-tote.jpg', 'description' => 'Structured leather tote handbag finished with a decorative silk scarf handle.'],
                        ['name' => 'Miu Miu Woven Bamboo-Handle Bag', 'category' => 'Bags', 'price' => 12500, 'image' => '/storage/tenants/4/products/miumiu-woven-bag.jpg', 'description' => 'Woven raffia mini tote with bamboo top handles, crossbody strap, and charm detail.'],
                        ['name' => 'Round Quilted Crossbody Bag', 'category' => 'Bags', 'price' => 12500, 'image' => '/storage/tenants/4/products/round-quilted-bag.jpg', 'description' => 'Compact round quilted crossbody bag with gold-tone hardware and detachable strap.'],
                        ['name' => 'Classic Turn-Lock Top-Handle Bag', 'category' => 'Bags', 'price' => 10000, 'image' => '/storage/tenants/4/products/turnlock-top-handle-bag.jpg', 'description' => 'Structured top-handle handbag (28×20×11 cm) with gold turn-lock clasp.'],
                        ['name' => 'Long-Sleeve Ribbed Loungewear Set', 'category' => 'Clothing', 'price' => 8500, 'image' => '/storage/tenants/4/products/ribbed-loungewear-set.jpg', 'description' => 'Soft, breathable two-piece long-sleeve loungewear set available in multiple colors.'],
                        ['name' => 'Drawstring Casual Culotte Shorts', 'category' => 'Clothing', 'price' => 7500, 'image' => '/storage/tenants/4/products/culotte-shorts.jpg', 'description' => 'Comfortable high-waist drawstring culotte shorts for everyday casual wear.'],
                        ['name' => 'Electric Menstrual Heating Pad Belt', 'category' => 'Beauty & Wellness', 'price' => 11000, 'image' => '/storage/tenants/4/products/heating-pad-belt.jpg', 'description' => 'Portable rechargeable heating & massage waist belt with digital temperature display.'],
                        ['name' => 'Quilted Vanity Top-Handle Bag', 'category' => 'Bags', 'price' => 13000, 'image' => '/storage/tenants/4/products/quilted-vanity-bag.jpg', 'description' => 'Quilted mini vanity case handbag with gold top handle and zip closure.'],
                        ['name' => 'Nike Air Force 1 White Sneakers', 'category' => 'Shoes', 'price' => 28000, 'image' => '/storage/tenants/4/products/nike-af1-white.jpg', 'description' => 'Classic crisp all-white Air Force 1 low-top leather sneakers.'],
                        ['name' => 'Quilted Chain Flap Shoulder Bag', 'category' => 'Bags', 'price' => 18000, 'image' => '/storage/tenants/4/products/quilted-chain-flap-bag.jpg', 'description' => 'Quilted flap shoulder bag with interwoven chain strap and gold clasp.'],
                        ['name' => 'Structured Double-Handle Handbag', 'category' => 'Bags', 'price' => 20000, 'image' => '/storage/tenants/4/products/structured-handbag.jpg', 'description' => 'Elegant structured top-handle handbag (27×20 cm) with spacious interior and crossbody strap.'],
                        ['name' => '2-Piece Bath & Face Towel Set', 'category' => 'Home & Essentials', 'price' => 6000, 'image' => '/storage/tenants/4/products/bath-towel-set.jpg', 'description' => 'Ultra-soft absorbent microfiber bath towel and matching face towel gift set.'],
                    ];

                    $created = [];
                    foreach ($items as $item) {
                        $catSlug = Str::slug($item['category']).'-'.$tenantId;
                        $catId = DB::table('categories')->where('tenant_id', $tenantId)->where('slug', $catSlug)->value('id');
                        if (! $catId) {
                            $catId = DB::table('categories')->insertGetId([
                                'tenant_id' => $tenantId,
                                'name' => $item['category'],
                                'slug' => $catSlug,
                                'description' => $item['category'].' collection',
                                'is_active' => true,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]);
                        }

                        $prodSlug = Str::slug($item['name']).'-'.$tenantId;
                        $existing = DB::table('products')->where('tenant_id', $tenantId)->where('slug', $prodSlug)->first();
                        if (! $existing) {
                            $productId = DB::table('products')->insertGetId([
                                'tenant_id' => $tenantId,
                                'category_id' => $catId,
                                'name' => $item['name'],
                                'slug' => $prodSlug,
                                'description' => $item['description'],
                                'base_price' => $item['price'],
                                'featured_image' => $item['image'],
                                'status' => 'active',
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]);

                            DB::table('product_variants')->insert([
                                'tenant_id' => $tenantId,
                                'product_id' => $productId,
                                'sku' => strtoupper(Str::slug($item['name'], '')).'-'.$tenantId,
                                'size' => 'Standard',
                                'color' => 'Assorted',
                                'price_override' => $item['price'],
                                'stock_quantity' => 10,
                                'is_active' => true,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]);

                            $created[] = $item['name'];
                        }
                    }

                    $output = json_encode(['created_products' => $created, 'count' => count($created)], JSON_PRETTY_PRINT);
                    break;

                case 'cleanup_test_stores':
                    $testTenants = DB::table('tenants')
                        ->whereIn('slug', ['tester', 'jessis-classiq-store'])
                        ->get(['id', 'name', 'slug']);
                    $tenantIds = $testTenants->pluck('id')->all();

                    if ($tenantIds !== []) {
                        DB::transaction(function () use ($tenantIds): void {
                            DB::table('users')->whereIn('tenant_id', $tenantIds)->delete();
                            DB::table('site_visits')->whereIn('tenant_id', $tenantIds)->delete();
                            DB::table('tenants')->whereIn('id', $tenantIds)->delete();
                        });
                    }

                    $output = json_encode([
                        'deleted_tenants' => $testTenants,
                    ], JSON_PRETTY_PRINT);
                    break;

                case 'list_users':
                    $users = DB::table('users')->select('id', 'name', 'email', 'role', 'tenant_id', 'created_at')->get();
                    $output = json_encode($users, JSON_PRETTY_PRINT);
                    break;

                case 'testadmin_info':
                    $user = DB::table('users')->where('email', 'testadmin@shopmanager.test')->first();
                    $tenants = DB::table('tenants')->get();
                    $userTenantProducts = $user && $user->tenant_id ? DB::table('products')->where('tenant_id', $user->tenant_id)->get(['id', 'name', 'base_price', 'tenant_id']) : [];
                    $allProducts = DB::table('products')->count();
                    $output = json_encode([
                        'user' => $user,
                        'tenants' => $tenants,
                        'user_tenant_products' => $userTenantProducts,
                        'all_products_count' => $allProducts,
                    ], JSON_PRETTY_PRINT);
                    break;

                case 'seed':
                    Artisan::call('db:seed', ['--force' => true]);
                    $output = Artisan::output();
                    break;

                case 'seed_testadmin_products':
                    Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\TestStoreDataSeeder', '--force' => true]);
                    $output = Artisan::output();
                    break;

                case 'migrate_seed':
                    Artisan::call('migrate', ['--force' => true]);
                    $output .= "=== MIGRATIONS ===\n".Artisan::output()."\n";
                    Artisan::call('db:seed', ['--force' => true]);
                    $output .= "=== SEEDING ===\n".Artisan::output();
                    break;

                case 'storage_link':
                    $publicStorage = public_path('storage');
                    if (file_exists($publicStorage) || is_link($publicStorage)) {
                        @unlink($publicStorage);
                    }
                    Artisan::call('storage:link');
                    $output = Artisan::output();
                    break;

                case 'optimize':
                    Artisan::call('config:cache');
                    Artisan::call('view:cache');
                    $output = "Configuration and Blade view templates cached successfully.\n";
                    break;

                case 'clear_cache':
                    Artisan::call('optimize:clear');
                    $output = Artisan::output();
                    break;

                case 'clear_route_cache':
                    Artisan::call('route:clear');
                    $output = Artisan::output();
                    break;

                case 'read_log':
                    $logFile = storage_path('logs/laravel.log');
                    if (file_exists($logFile)) {
                        $lines = file($logFile);
                        $output = implode('', array_slice($lines, -100));
                    } else {
                        $output = 'No laravel.log file found.';
                    }
                    break;

                case 'all':
                    Artisan::call('migrate', ['--force' => true]);
                    $output .= "=== 1. MIGRATIONS ===\n".Artisan::output()."\n";

                    Artisan::call('db:seed', ['--force' => true]);
                    $output .= "=== 2. SEEDING (CATALOG & MEDIA) ===\n".Artisan::output()."\n";

                    $publicStorage = public_path('storage');
                    if (file_exists($publicStorage) || is_link($publicStorage)) {
                        @unlink($publicStorage);
                    }
                    Artisan::call('storage:link');
                    $output .= "=== 3. STORAGE LINK ===\n".Artisan::output()."\n";

                    Artisan::call('route:clear');
                    Artisan::call('config:cache');
                    Artisan::call('view:cache');
                    $output .= "=== 4. CONFIG & VIEW CACHE ===\nConfiguration and views cached successfully (routes kept dynamic for subfolder compatibility).\n";
                    break;

                default:
                    $success = false;
                    $output = "Unknown action requested: {$action}";
                    break;
            }
        } catch (Throwable $e) {
            $success = false;
            $error = $e->getMessage();
            $output .= "\n[EXCEPTION]: ".$e->getMessage();
        }

        return [
            'action' => $action,
            'success' => $success,
            'output' => trim($output),
            'error' => $error,
            'timestamp' => now()->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Gather system, database, and permissions diagnostics.
     *
     * @return array<string, mixed>
     */
    protected function runDiagnostics(): array
    {
        $dbConnected = false;
        $dbError = null;
        $dbName = null;

        try {
            DB::connection()->getPdo();
            $dbConnected = true;
            $dbName = DB::connection()->getDatabaseName();
        } catch (Throwable $e) {
            $dbError = $e->getMessage();
        }

        $writablePaths = [
            'storage/app' => storage_path('app'),
            'storage/framework' => storage_path('framework'),
            'storage/logs' => storage_path('logs'),
            'bootstrap/cache' => base_path('bootstrap/cache'),
        ];

        $permissions = [];
        foreach ($writablePaths as $label => $path) {
            $permissions[$label] = [
                'exists' => file_exists($path),
                'writable' => is_writable($path),
                'path' => $path,
            ];
        }

        $storageLinkActive = false;
        $publicStorage = public_path('storage');
        if (is_link($publicStorage) || (file_exists($publicStorage) && is_dir($publicStorage))) {
            $storageLinkActive = true;
        }

        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug'),
            'app_url' => config('app.url'),
            'database_connected' => $dbConnected,
            'database_name' => $dbName,
            'database_error' => $dbError,
            'storage_link_active' => $storageLinkActive,
            'permissions' => $permissions,
            'public_build_manifest' => file_exists(public_path('build/manifest.json')),
        ];
    }
}
