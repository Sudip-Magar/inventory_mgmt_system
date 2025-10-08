<?php

namespace App\Livewire\User;

use App\Models\Customer;
use App\Models\SaleItems;
use Livewire\Component;
use App\Models\Discount;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleReturn as ModelSaleReturn;
use App\Models\SaleReturnItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class SaleReturn extends Component
{
    public function getData()
    {
        $saleReturn = ModelSaleReturn::with('sale.customer', 'saleReturnItems.product', 'sale.salesItems')
            ->latest()
            ->get();

        // dd($saleReturn);
        $vendor = Customer::latest()->get();
        $discount = Discount::latest()->get();
        $products = Product::latest()->get();
        return [
            $saleReturn,
            $vendor,
            $products,
            $discount,
        ];
    }

    public function updateSaleReturn($payload)
    {
        DB::beginTransaction();
        try {
            $purchaseReturn = ModelSaleReturn::findOrFail($payload['id']);
            if (!$purchaseReturn) {
                throw new \Exception('Sale Return Not Found');
            }

            $purchaseReturn->update([
                'total_amount' => $payload['total_amount'],
                'total_quantity' => $payload['total_quantity'],
                'total_discount_amt' => $payload['totalTermAmount'],
            ]);

            SaleReturnItem::where('sale_return_id', $payload['id'])->delete();
            foreach ($payload['product_id'] as $i => $productId) {
                SaleReturnItem::create([
                    'sale_return_id' => $payload['sale_id'],
                    'product_id' => $productId,
                    'quantity' => $payload['quantity'][$i],
                    'cost_price' => $payload['selling_price'][$i],
                    'subTotal' => $payload['netAmount'][$i],
                    'disount_amt' => $payload['termAmount'][$i],
                ]);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function confirmOrder($id)
    {
        return DB::transaction(function () use ($id) {
            $saleReturn = ModelSaleReturn::lockForUpdate()->find($id);

            if (!$saleReturn || $saleReturn->status == 'confirmed') {
                return false;
            }

            $saleReturnItems = SaleReturnItem::where('sale_return_id', $id)->lockForUpdate()->get(['product_id', 'quantity']);
            if (!$saleReturnItems) {
                return false;
            }

            $quantityByProduct = $saleReturnItems
                ->groupBy('product_id')
                ->map(function ($items) {
                    return $items->sum('quantity');
                });

            foreach ($quantityByProduct as $productId => $quantityToAdd) {
                Product::where('id', $productId)->increment('stock', $quantityToAdd);

                $currentStock = SaleItems::where('product_id', $productId)->value('quantity');
                $newStock = max(0, (int) $currentStock - (int) $quantityToAdd);
                SaleItems::where('product_id', $productId)->update(['quantity' => $newStock]);
            }

            $saleReturn->status = 'confirmed';
            $sale = Sale::find($saleReturn->sale_id);
            $sale->update([
                'total_amount' => $sale->total_amount - $saleReturn->total_amount,
                'quantity' => $sale->quantity - $saleReturn->total_quantity,
                'total_discount_amt' => $sale->total_discount_amt - $saleReturn->total_discount_amt,
            ]);

            foreach ($saleReturnItems as $items) {
                StockMovement::create([
                    'product_id' => $items->product_id,
                    'quantity' => $items->quantity,
                    'type' => 'Sale-return'
                ]);
            }
            $saleReturn->save();
            return true;
        });
    }

    public function cancelOrder($id)
    {
        return DB::transaction(function () use ($id) {
            $saleReturn = ModelSaleReturn::lockForUpdate()->find($id);
            if (!$saleReturn || $saleReturn->status == 'cancelled') {
                return false;
            }

            if ($saleReturn->status === 'draft') {
                $saleReturn->status = 'cancelled';
                $saleReturn->save();
                return true;
            }

            $saleReturnItems = SaleReturnItem::where('sale_return_id', $id)->get(['product_id', 'quantity']);
            if ($saleReturnItems->isEmpty()) {
                return false;
            }

            $quantityByProduct = $saleReturnItems
                ->groupBy('product_id')
                ->map(function ($item) {
                    return $item->sum('quantity');
                });

            foreach ($quantityByProduct as $productId => $quantityToSubtract) {
                $currentStock = Product::where('id', $productId)->value('stock');
                $newStock = max(0, (int) $currentStock - (int) $quantityToSubtract);
                Product::where('id', $productId)->update(['stock' => $newStock]);

                SaleItems::where('product_id', $productId)->increment('quantity', $quantityToSubtract);
            }
            $saleReturn->status = 'cancelled';
            $sale = Sale::find($saleReturn->sale_id);
            $sale->update([
                'total_amount' => $sale->total_amount + $saleReturn->total_amount,
                'quantity' => $sale->quantity + $saleReturn->total_quantity,
                'total_discount_amt' => $sale->total_discount_amt + $saleReturn->total_discount_amt,
            ]);

            foreach ($saleReturnItems as $items) {
                StockMovement::create([
                    'product_id' => $items->product_id,
                    'quantity' => $items->quantity,
                    'type' => 'Sale-return_cancel'
                ]);
            }
            $saleReturn->save();
            return true;
        });
    }

    public function resetToDraft($id)
    {
        $sale = ModelSaleReturn::find($id);
        if ($sale) {
            $sale->status = 'draft';
            $sale->save();
            return true;
        }
        return false;
    }
    public function render()
    {
        return view('livewire.user.sale-return');
    }
}
