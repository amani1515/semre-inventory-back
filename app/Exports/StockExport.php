<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StockExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        return Product::query()->orderBy('name');
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Category', 'SKU', 'Cost Price (ETB)', 'Selling Price (ETB)', 'Stock Quantity'];
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->name,
            $product->category,
            $product->sku,
            $product->cost_price,
            $product->selling_price,
            $product->stock_quantity,
        ];
    }
}
