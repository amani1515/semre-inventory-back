<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        private string $period,
        private ?string $date = null
    ) {}

    public function query()
    {
        $query = Sale::with(['user', 'items'])
            ->whereIn('status', ['completed', 'approved']);

        if ($this->period === 'daily') {
            $query->whereDate('created_at', $this->date ?? today());
        } elseif ($this->period === 'monthly') {
            $query->whereYear('created_at', now()->year)
                  ->whereMonth('created_at', $this->date ?? now()->month);
        }

        return $query;
    }

    public function headings(): array
    {
        return ['Sale #', 'Sales Officer', 'Subtotal (ETB)', 'Discount (%)', 'VAT (ETB)', 'Total (ETB)', 'Status', 'Date'];
    }

    public function map($sale): array
    {
        return [
            $sale->id,
            $sale->user->name ?? '-',
            $sale->subtotal,
            $sale->discount,
            $sale->vat_amount,
            $sale->total,
            $sale->status,
            $sale->created_at->format('Y-m-d H:i'),
        ];
    }
}
