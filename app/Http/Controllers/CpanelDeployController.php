<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
