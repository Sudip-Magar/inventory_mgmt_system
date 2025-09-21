<?php

namespace App\Livewire\User;

use App\Models\Discount;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\Purchase as ModelPurchase;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use App\Models\PurchaseItems;
use App\Models\PurhchaseReturn;
use App\Models\PurhchaseReturnItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\isEmpty;

class Purchase extends Component
{

    public function getdata()
    {
        $vendor = Vendor::latest()->get();
        $purchase = ModelPurchase::with('purchaseItems.product', 'vendor')->latest()->get();
        $product = Product::latest()->get();
        $discount = Discount::latest()->get();
        $discount = Discount::latest()->get();
        return [
            $vendor,
            $purchase,
            $product,
            $discount,
        ];
    }

    public function save($payload)
    {
        DB::beginTransaction();
        try {
            // Create the purchase record
            $purchase = ModelPurchase::create([
                'vendor_id' => $payload['vendor_id'],
                'total_amount' => $payload['total_amount'],
                'total_quantity' => $payload['total_quantity'],
                'order_date' => $payload['order_date'] ?? now(),
                'expected_date' => $payload['expected_date'],
                'payment_method' => $payload['payment_method'] ?? "cash",
                'notes' => $payload['notes'] ?? '',
                'total_discount_amt' => $payload['totalTermAMount'] ?? 0,
                'total_paid_amount' => 0,
                'total_due_amount' => $payload['total_amount']
                // status and payment_status use default values
            ]);

            // Create purchase items
            foreach ($payload['product_id'] as $i => $productId) {
                PurchaseItems::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $productId,
                    'quantity' => $payload['quantity'][$i],
                    'cost_price' => $payload['cost_price'][$i],
                    'subTotal' => $payload['subTotal'][$i],
                    'disount_amt' => $payload['termAmount'][$i],
                ]);
            }

            DB::commit();
            return true; // Or return a message if you want
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e; // Or handle the error as you wish
        }
    }

    public function updatePurchase($id, $payload)
    {
        DB::beginTransaction();
        try {
            $purchase = ModelPurchase::find($id);
            if (!$purchase) {
                throw new \Exception("Purchase not found");
            }

            // Update the purchase record
            $purchase->update([
                'vendor_id' => $payload['vendor_id'],
                'total_amount' => $payload['total_amount'],
                'total_quantity' => $payload['total_quantity'],
                'order_date' => $payload['order_date'],
                'expected_date' => $payload['expected_date'],
                'payment_method' => $payload['payment_method'],
                'notes' => $payload['notes'] ?? '',
                'total_discount_amt' => $payload['totalTermAMount'] ?? 0,
            ]);

            // Delete existing purchase items
            PurchaseItems::where('purchase_id', $id)->delete();

            // Create new purchase items
            foreach ($payload['product_id'] as $i => $productId) {
                PurchaseItems::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $productId,
                    'quantity' => $payload['quantity'][$i],
                    'cost_price' => $payload['cost_price'][$i],
                    'subTotal' => $payload['subTotal'][$i],
                    'disount_amt' => $payload['termAmount'][$i],
                ]);
            }

            DB::commit();
            return true; // Or return a message if you want
        } catch (\Exception | ValidationException $exception) {
            DB::rollBack();
            throw $exception; // Or handle the error as you wish
        }
    }

    public function cancelOrder($id)
    {

        return DB::transaction(function () use ($id) {
            $purchase = ModelPurchase::lockForUpdate()->find($id);
            if (!$purchase || $purchase->status === 'cancelled') {
                return false;
            }

            if ($purchase->status === 'draft') {
                $purchase->status = 'cancelled';
                $purchase->save();
                return true;
            }

            $purchaseItems = PurchaseItems::where('purchase_id', $id)
                ->lockForUpdate()
                ->get(['product_id', 'quantity']);

            if ($purchaseItems->isEmpty()) {
                return false;
            }

            $quantityByProduct = $purchaseItems
                ->groupBy('product_id')
                ->map(function ($items) {
                    return $items->sum('quantity');
                });

            foreach ($quantityByProduct as $productId => $quantityToSubtract) {
                // Prevent negative stock by clamping at zero
                $currentStock = Product::where('id', $productId)->value('stock');
                $newStock = max(0, (int) $currentStock - (int) $quantityToSubtract);
                Product::where('id', $productId)->update(['stock' => $newStock]);
            }

            $purchase->status = 'cancelled';
            foreach ($purchaseItems as $item) {
                StockMovement::create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'type' => 'purchase_cancel',
                ]);
            }
            $purchase->save();

            return true;
        });
    }

    public function confirmOrder($id)
    {
        return DB::transaction(function () use ($id) {
            $purchase = ModelPurchase::lockForUpdate()->find($id);
            if (!$purchase || $purchase->status === 'confirmed') {
                return false;
            }

            $purchaseItems = PurchaseItems::where('purchase_id', $id)
                ->get(['product_id', 'quantity']);

            if ($purchaseItems->isEmpty()) {
                return false;
            }

            $quantityByProduct = $purchaseItems
                ->groupBy('product_id')
                ->map(function ($items) {
                    return $items->sum('quantity');
                });

            foreach ($quantityByProduct as $productId => $quantityToAdd) {
                Product::where('id', $productId)->increment('stock', $quantityToAdd);
            }

            $purchase->status = 'confirmed';
            $purchase->payment_status = 'paid';
            foreach ($purchaseItems as $item) {
                StockMovement::create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'type' => 'purchase',
                ]);
            }
            $purchase->save();

            return true;
        });
    }

    public function createPurchaseReturn($payload)
    {
        // dd($payload);
        DB::beginTransaction();
        try {
            $purchaseReturn = PurhchaseReturn::create([
                'purchase_id' => $payload['purchase_id'],
                'return_reason' => $payload['reason'],
                'total_amount' => $payload['total_net_amount'],
                'total_quantity' => $payload['total_qty'],
                'total_discount_amt' => $payload['total_term_amt'],
                'status' => 'draft',
                'payment_status' => 'unpaid'
            ]);

            foreach ($payload['product_id'] as $i => $productId) {
                if ($payload['quantity'][$i] != 0) {
                    PurhchaseReturnItem::create([
                        'purhchase_return_id' => $purchaseReturn->id,
                        'product_id' => $productId,
                        'quantity' => $payload['quantity'][$i],
                        'cost_price' => $payload['rate'][$i],
                        'subTotal' => $payload['netAmount'][$i],
                        'disount_amt' => $payload['termAmount'][$i],
                    ]);
                }
            }
            DB::commit();
            return $this->redirectRoute('purchase-return');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e; // Or handle the error as you wish
        }

    }

    public function resetToDraft($id)
    {
        $purchase = ModelPurchase::find($id);
        if ($purchase) {
            $purchase->status = 'draft';
            $purchase->save();
            return true;
        }
        return false;
    }
    public function render()
    {
        return view('livewire.user.purchase');
    }
}
