<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\ShopLocation;
use Illuminate\View\View;

class ShopLocationController extends Controller
{
    public function index(): View
    {
        $primaryLocation = ShopLocation::where('is_primary', true)->first()
            ?? ShopLocation::first();

        $allLocations = ShopLocation::all();

        return view('store.location.index', compact('primaryLocation', 'allLocations'));
    }
}
