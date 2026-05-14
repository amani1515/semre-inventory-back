<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sale\StoreSaleRequest;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public const VAT_RATE             = 0.15;
    public const APPROVAL_THRESHOLD   = 50000;

    public function index(): JsonResponse
    {
        $sales = Sale::with(['user:id,name', 'items.product:id,name,sku'])
            ->latest()
            ->paginate(20);

        return response()->json($sales);
    }

    public function store(StoreSaleRequest $request): JsonResponse
    {
        $sale = DB::transaction(function () use ($request) {

            // 1. Load & lock products, validate stock
            $productIds = collect($request->items)->pluck('product_id');
            $products   = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

            foreach ($request->items as $item) {
                $product = $products->get($item['product_id']);

                if ($product->stock_quantity < $item['quantity']) {
                    abort(422, "Insufficient stock for product: {$product->name}. Available: {$product->stock_quantity}");
                }
            }

            // 2. Calculate subtotal and deduct stock
            $subtotal = 0;
            $saleItems = [];

            foreach ($request->items as $item) {
                $product    = $products->get($item['product_id']);
                $lineTotal  = $product->selling_price * $item['quantity'];
                $subtotal  += $lineTotal;

                $product->decrement('stock_quantity', $item['quantity']);

                $saleItems[] = [
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $product->selling_price,
                    'subtotal'   => $lineTotal,
                ];
            }

            // 3. Apply discount and VAT
            $discount       = $request->discount ?? 0;
            $discountAmount = round($subtotal * ($discount / 100), 2);
            $afterDiscount  = $subtotal - $discountAmount;
            $vatAmount      = round($afterDiscount * self::VAT_RATE, 2);
            $total          = round($afterDiscount + $vatAmount, 2);

            // 4. Determine status — requires manager approval if total > 50,000
            $status = $total > self::APPROVAL_THRESHOLD ? 'pending_approval' : 'completed';

            // 5. Create sale record
            $sale = Sale::create([
                'user_id'         => $request->user()->id,
                'subtotal'        => $subtotal,
                'discount'        => $discount,
                'discount_amount' => $discountAmount,
                'vat_amount'      => $vatAmount,
                'total'           => $total,
                'status'          => $status,
                'note'            => $request->note,
            ]);

            // 6. Create sale items
            $sale->items()->createMany($saleItems);

            return $sale;
        });

        return response()->json(
            $sale->load(['items.product:id,name,sku', 'user:id,name']),
            201
        );
    }

    public function show(Sale $sale): JsonResponse
    {
        return response()->json(
            $sale->load(['items.product:id,name,sku', 'user:id,name', 'approvedBy:id,name'])
        );
    }
}
