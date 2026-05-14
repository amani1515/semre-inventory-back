<?php

namespace App\Http\Controllers\Api;

use App\Exports\SalesExport;
use App\Exports\StockExport;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    // GET /api/reports/sales?period=daily&date=2024-01-15&format=pdf|excel
    public function sales(Request $request)
    {
        $request->validate([
            'period' => ['required', 'in:daily,monthly'],
            'format' => ['required', 'in:pdf,excel'],
            'date'   => ['nullable', 'string'],
        ]);

        $period = $request->period;
        $format = $request->format;
        $date   = $request->date;

        $query = Sale::with(['user', 'items.product'])
            ->whereIn('status', ['completed', 'approved']);

        if ($period === 'daily') {
            $query->whereDate('created_at', $date ?? today());
            $label = 'Daily Sales Report - ' . ($date ?? today()->toDateString());
        } else {
            [$year, $month] = $date ? explode('-', $date) : [now()->year, now()->month];
            $query->whereYear('created_at', $year)->whereMonth('created_at', $month);
            $label = 'Monthly Sales Report - ' . now()->setMonth($month)->format('F') . " $year";
        }

        $sales = $query->get();
        $summary = [
            'total_sales'    => $sales->count(),
            'total_revenue'  => $sales->sum('total'),
            'total_vat'      => $sales->sum('vat_amount'),
            'total_discount' => $sales->sum('discount_amount'),
        ];

        if ($format === 'excel') {
            return Excel::download(new SalesExport($period, $date), 'sales-report.xlsx');
        }

        $pdf = Pdf::loadView('reports.sales', compact('sales', 'summary', 'label'));
        return $pdf->download('sales-report.pdf');
    }

    // GET /api/reports/stock?format=pdf|excel
    public function stock(Request $request)
    {
        $request->validate([
            'format' => ['required', 'in:pdf,excel'],
        ]);

        if ($request->format === 'excel') {
            return Excel::download(new StockExport(), 'stock-report.xlsx');
        }

        $products = Product::orderBy('name')->get();
        $pdf = Pdf::loadView('reports.stock', compact('products'));
        return $pdf->download('stock-report.pdf');
    }
}
