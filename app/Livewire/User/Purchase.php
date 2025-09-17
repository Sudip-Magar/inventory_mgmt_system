<?php

namespace App\Livewire\User;

use App\Models\Discount;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\Purchase as ModelPurchase;
use Livewire\Component;
use App\Models\PurchaseItems;
use Illuminate\Support\Facades\DB;

class Purchase extends Component
{

    public function getdata()
    {
        $vendor = Vendor::latest()->get();
        $purchase = ModelPurchase::with('purchaseItems.product','vendor')->latest()->get();
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
                'order_date' => $payload['order_date'],
                'expected_date' => $payload['expected_date'],
                'payment_method' => $payload['payment_method'],
                'notes' => $payload['notes'] ?? '',
                'total_discount_amt'=>$payload['totalTermAMount'] ?? 0,
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
                    'disount_amt'=>$payload['termAmount'][$i],
                ]);
            }

            DB::commit();
            return true; // Or return a message if you want
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e; // Or handle the error as you wish
        }
    }

    public function updatePurchase($id, $payload){
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
                'total_discount_amt'=>$payload['totalTermAMount'] ?? 0,
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
                    'disount_amt'=>$payload['termAmount'][$i],
                ]);
            }

            DB::commit();
            return true; // Or return a message if you want
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e; // Or handle the error as you wish
        }
    }

    public function cancelOrder($id)
    {
        $purchase = ModelPurchase::find($id);
        if ($purchase) {
            $purchase->status = 'cancelled';
            $purchase->save();
            return true;
        }
        return false;
    }
    
    public function confirmOrder($id)
    {
        $purchase = ModelPurchase::find($id);
        if ($purchase) {
            $purchase->status = 'confirmed';
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
