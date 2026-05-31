<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Item;
use App\Models\Staff;
use App\Models\StockIssuance;
use App\Models\StockReturn;

class Dashboard extends Component
{
    public function render()
    {
        // 1. Calculate high-level KPIs
        $totalItems = Item::count();
        $activeWardens = Staff::where('status', 'active')->count();
        
        // Items where aggregate current stock is below reorder threshold
        $lowStockCount = Item::all()->filter(function ($item) {
            return $item->needs_restock;
        })->count();

        // Count of active issuances
        $activeIssuancesCount = StockIssuance::where('status', 'issued')->count();

        // 2. Fetch lists for dashboard views
        $lowStockItems = Item::with('category')->get()->filter(function ($item) {
            return $item->needs_restock;
        })->take(5);

        $recentIssuances = StockIssuance::with(['staff', 'issuer'])
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        $categoryData = \App\Models\Category::withCount('items')->get();
        $chartCategories = $categoryData->pluck('name')->toArray();
        $chartCounts = $categoryData->pluck('items_count')->toArray();

        return view('livewire.dashboard', [
            'totalItems' => $totalItems,
            'activeWardens' => $activeWardens,
            'lowStockCount' => $lowStockCount,
            'activeIssuancesCount' => $activeIssuancesCount,
            'lowStockItems' => $lowStockItems,
            'recentIssuances' => $recentIssuances,
            'chartCategories' => $chartCategories,
            'chartCounts' => $chartCounts
        ])->layout('components.layouts.app');
    }
}
