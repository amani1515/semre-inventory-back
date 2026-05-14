<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        protected ?string $dateFrom = null,
        protected ?string $dateTo = null,
    ) {}

    public function query()
    {
        return Product::query()
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo));
    }

    public function headings(): array
    {
        return ['Name', 'Category', 'SKU', 'Cost Price (ETB)', 'Selling Price (ETB)', 'Stock Quantity', 'Last Updated'];
    }

    public function map($row): array
    {
        return [
            $row->name,
            $row->category,
            $row->sku,
            $row->cost_price,
            $row->selling_price,
            $row->stock_quantity,
            $row->updated_at->format('Y-m-d H:i'),
        ];
    }
}
