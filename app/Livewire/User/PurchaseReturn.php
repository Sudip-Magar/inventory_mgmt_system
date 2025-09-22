<?php

namespace App\Livewire\User;

use App\Models\Discount;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItems;
use App\Models\PurhchaseReturnItem;
use App\Models\PurhchaseReturn as modelPurchaseReturn;
use App\Models\StockMovement;
use App\Models\Vendor;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class PurchaseReturn extends Component
{
    public function getData()
    {
        $purchaseReturn = ModelPurchaseReturn::with('purchase.vendor', 'purchaseReturnItems.product', 'purchase.purchaseItems')->latest()->get();
        $vendor = Vendor::latest()->get();
        $discount = Discount::latest()->get();
        $products = Product::latest()->get();
        return [
            $purchaseReturn,
            $vendor,
            $products,
            $discount,
        ];
    }

    public function updateData($payload)
    {
        DB::beginTransaction();
        try {
            $purchaseReturn = ModelPurchaseReturn::findOrFail($payload['id']);
            if (!$purchaseReturn) {
                throw new \Exception('Purchase Return Not Found');
            }

            $purchaseReturn->update([
                'purchase_id' => $payload['purchase_id'],
                // 'return_reason' =>,
                'total_amount' => $payload['total_amount'],
                'total_quantity' => $payload['total_qty'],
                'total_discount_amt' => $payload['total_term_amt'],
                // 'payment_method' 
            ]);

            PurhchaseReturnItem::where('purhchase_return_id', $payload['id'])->delete();
            foreach ($payload['product_id'] as $i => $productId) {
                PurhchaseReturnItem::create([
                    'purhchase_return_id' => $payload['purchase_id'],
                    'product_id' => $productId,
                    'quantity' => $payload['quantity'][$i],
                    'cost_price' => $payload['cost_price'][$i],
                    'subTotal' => $payload['subTotal'][$i],
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
            $purchaseReturn = ModelPurchaseReturn::lockForUpdate()->find($id);
            if (!$purchaseReturn || $purchaseReturn->status == 'confirmed') {
                return false;
            }
            $purchaseReturnItems = PurhchaseReturnItem::where('purhchase_return_id', $id)->lockForUpdate()->get(['product_id', 'quantity']);
            // dd($purchaseReturnItems->quantity);
            if ($purchaseReturnItems->isEmpty()) {
                return false;
            }

            $quantityByProduct = $purchaseReturnItems
                ->groupBy('product_id')
                ->map(function ($items) {
                    return $items->sum('quantity');
                });

            foreach ($quantityByProduct as $productId => $quantityToSubtract) {
                $currentStock = Product::where('id', $productId)->value('stock');
                $newStock = max(0, (int) $currentStock - (int) $quantityToSubtract);
                Product::where('id', $productId)->update(['stock' => $newStock]);

                $currentPurchaseStock = PurchaseItems::where('product_id', $productId)->value('quantity');
                $newPurchaseStock = max(0, (int) $currentPurchaseStock - (int) $quantityToSubtract);
                PurchaseItems::where('product_id', $productId)->update(['quantity' => $newPurchaseStock]);
            }
            $purchaseReturn->status = 'confirmed';
            $purchase = Purchase::find($purchaseReturn->purchase_id);
            $purchase->update([
                'total_amount' => $purchase->total_amount - $purchaseReturn->total_amount,
                'total_quantity' => $purchase->total_quantity - $purchaseReturn->total_quantity,
                'total_discount_amt' => $purchase->total_discount_amt - $purchaseReturn->total_discount_amt,
                // 'return_qty' => $purchaseReturn-> total_quantity,
            ]);

            foreach($purchaseReturnItems as $item){
                StockMovement::create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'type' => 'purchase-return',
                ]);
            };
            $purchaseReturn->save();
            return true;


        });
    }

    public function cancelPurchaseReturn($id)
    {
        return DB::transaction(function () use ($id) {
            $purchaseReturn = modelPurchaseReturn::lockForUpdate()->find($id);
            if (!$purchaseReturn || $purchaseReturn->status == 'cancelled') {
                return false;
            }
            if ($purchaseReturn->status === 'draft') {
                $purchaseReturn->status = 'cancelled';
                $purchaseReturn->save();
                return true;
            }

            $purchaseReturnItems = PurhchaseReturnItem::where('purhchase_return_id', $id)->get(['product_id', 'quantity']);
            if ($purchaseReturnItems->isEmpty()) {
                return false;
            }

            $quantityByProduct = $purchaseReturnItems
                ->groupBy('product_id')
                ->map(function ($items) {
                    return $items->sum('quantity');
                });
            foreach ($quantityByProduct as $productId => $quantityToAdd) {
                Product::where('id', $productId)->increment('stock', $quantityToAdd);
                PurchaseItems::where('product_id', $productId)->increment('quantity', $quantityToAdd);
            }
            $purchaseReturn->status = 'cancelled';
            $purchase = Purchase::find($purchaseReturn->purchase_id);
            $purchase->update([
                'total_amount' => $purchase->total_amount + $purchaseReturn->total_amount,
                'total_quantity' => $purchase->total_quantity + $purchaseReturn->total_quantity,
                'total_discount_amt' => $purchase->total_discount_amt + $purchaseReturn->total_discount_amt,
                // 'return_qty' => $purchaseReturn-> total_quantity,
            ]);

            foreach($purchaseReturnItems as $item){
                StockMovement::create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'type' => 'purchase-return_cancel',
                ]);
            }

            $purchaseReturn->save();
            return true;
        });
    }

    public function resetToDraft($id)
    {
        $purchase = modelPurchaseReturn::find($id);
        // dd($purchase);
        if ($purchase) {
            $purchase->status = 'draft';
            $purchase->save();
            return true;
        }
        return false;
    }

    public function render()
    {
        return view('livewire.user.purchase-return');
    }
}
