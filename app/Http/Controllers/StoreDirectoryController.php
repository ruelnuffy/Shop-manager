<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreDirectoryController extends Controller
{
    /**
     * Display a directory of all registered stores and boutiques on ShopManager.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $storesQuery = Tenant::query()
            ->where('status', 'active')
            ->withCount(['products', 'categories']);

        if ($search !== '') {
            $storesQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('subdomain', 'like', "%{$search}%")
                    ->orWhere('settings->branding->name', 'like', "%{$search}%")
                    ->orWhere('settings->branding->tagline', 'like', "%{$search}%")
                    ->orWhere('settings->storefront->location_badge', 'like', "%{$search}%");
            });
        }

        $stores = $storesQuery->latest()->get();
        $totalStoresCount = Tenant::where('status', 'active')->count();

        return view('platform.stores', compact('stores', 'totalStoresCount', 'search'));
    }
}
