<?php

namespace App\Http\Controllers;

use App\Services\BusinessDiscoveryService;
use Illuminate\Http\Request;

class BusinessDiscoveryController extends Controller
{
    public function __construct(
        private BusinessDiscoveryService $discoveryService
    ) {}

    public function index(Request $request)
    {
        $lat = $request->query('lat') ? (float) $request->query('lat') : (auth()->user()?->lat);
        $lng = $request->query('lng') ? (float) $request->query('lng') : (auth()->user()?->lng);
        $category = $request->query('category');

        $businesses = $this->discoveryService->getNearbyBusinesses($lat, $lng, $category);
        $trending = $this->discoveryService->getTrendingBusinesses($lat, $lng, 5);

        return view('businesses.index', compact('businesses', 'trending', 'lat', 'lng', 'category'));
    }
}
