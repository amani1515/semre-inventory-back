<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\Sale;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Sales Today', Sale::whereDate('created_at', today())
                ->whereIn('status', ['completed', 'approved'])->count())
                ->description('Completed & approved sales')
                ->color('success')
                ->icon('heroicon-o-shopping-cart'),

            Stat::make('Pending Approvals', Sale::where('status', 'pending_approval')->count())
                ->description('Sales awaiting manager approval')
                ->color('warning')
                ->icon('heroicon-o-clock'),

            Stat::make('Total Revenue Today', 'ETB ' . number_format(
                Sale::whereDate('created_at', today())
                    ->whereIn('status', ['completed', 'approved'])->sum('total'), 2))
                ->description('Today\'s revenue')
                ->color('primary')
                ->icon('heroicon-o-banknotes'),

            Stat::make('Low Stock Products', Product::where('stock_quantity', '<=', 5)->count())
                ->description('Products with stock ≤ 5')
                ->color('danger')
                ->icon('heroicon-o-exclamation-triangle'),
        ];
    }
}
