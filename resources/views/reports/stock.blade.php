<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
    h1 { color: #1a56db; font-size: 18px; margin-bottom: 4px; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th { background: #1a56db; color: white; padding: 8px; text-align: left; }
    td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; }
    tr:nth-child(even) { background: #f9fafb; }
    .low { color: #dc2626; font-weight: bold; }
</style>
</head>
<body>
    <h1>Stock Report</h1>
    <p>Generated: {{ now()->format('Y-m-d H:i') }} &nbsp;|&nbsp; Total Products: {{ $products->count() }}</p>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Category</th>
                <th>SKU</th>
                <th>Cost Price</th>
                <th>Selling Price</th>
                <th>Stock Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category }}</td>
                <td>{{ $product->sku }}</td>
                <td>ETB {{ number_format($product->cost_price, 2) }}</td>
                <td>ETB {{ number_format($product->selling_price, 2) }}</td>
                <td class="{{ $product->stock_quantity <= 5 ? 'low' : '' }}">
                    {{ $product->stock_quantity }}{{ $product->stock_quantity <= 5 ? ' ⚠' : '' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
