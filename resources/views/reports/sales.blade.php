<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
    h1 { color: #1a56db; font-size: 18px; margin-bottom: 4px; }
    .summary { background: #f3f4f6; padding: 10px; margin-bottom: 16px; border-radius: 4px; }
    .summary span { margin-right: 24px; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th { background: #1a56db; color: white; padding: 8px; text-align: left; }
    td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; }
    tr:nth-child(even) { background: #f9fafb; }
    .total { font-weight: bold; }
</style>
</head>
<body>
    <h1>{{ $label }}</h1>
    <p>Generated: {{ now()->format('Y-m-d H:i') }}</p>

    <div class="summary">
        <span><strong>Total Sales:</strong> {{ $summary['total_sales'] }}</span>
        <span><strong>Revenue:</strong> ETB {{ number_format($summary['total_revenue'], 2) }}</span>
        <span><strong>VAT Collected:</strong> ETB {{ number_format($summary['total_vat'], 2) }}</span>
        <span><strong>Discounts Given:</strong> ETB {{ number_format($summary['total_discount'], 2) }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Sale #</th>
                <th>Sales Officer</th>
                <th>Subtotal</th>
                <th>Discount</th>
                <th>VAT</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
            <tr>
                <td>{{ $sale->id }}</td>
                <td>{{ $sale->user->name ?? '-' }}</td>
                <td>ETB {{ number_format($sale->subtotal, 2) }}</td>
                <td>{{ $sale->discount }}%</td>
                <td>ETB {{ number_format($sale->vat_amount, 2) }}</td>
                <td class="total">ETB {{ number_format($sale->total, 2) }}</td>
                <td>{{ ucfirst($sale->status) }}</td>
                <td>{{ $sale->created_at->format('Y-m-d H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
