<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InventoryExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        protected ?string $dateFrom = null,
        protected ?string $dateTo = null,
    ) {}

    public function query()
    {
        return Product::query()
            ->when($this->dateFrom, fn ($q) => $q->whereDate('updated_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn ($q) => $q->whereDate('updated_at', '<=', $this->dateTo));
    }

    public function headings(): array
    {
        return ['Name', 'SKU', 'Category', 'Selling Price (ETB)', 'Stock Quantity', 'Last Updated'];
    }

    public function map($row): array
    {
        return [
            $row->name,
            $row->sku,
            $row->category,
            $row->selling_price,
            $row->stock_quantity,
            $row->updated_at->format('Y-m-d H:i'),
        ];
    }
}
