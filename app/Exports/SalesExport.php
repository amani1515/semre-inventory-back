<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        protected ?string $dateFrom = null,
        protected ?string $dateTo = null,
        protected ?string $status = null,
    ) {}

    public function query()
    {
        return Sale::with(['user', 'approvedBy'])
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->when($this->status,   fn ($q) => $q->where('status', $this->status));
    }

    public function headings(): array
    {
        return ['Sale #', 'Sales Officer', 'Subtotal (ETB)', 'Discount (%)', 'VAT (ETB)', 'Total (ETB)', 'Status', 'Approved By', 'Date'];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->user?->name,
            $row->subtotal,
            $row->discount,
            $row->vat_amount,
            $row->total,
            $row->status,
            $row->approvedBy?->name ?? '-',
            $row->created_at->format('Y-m-d H:i'),
        ];
    }
}
