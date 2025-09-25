<?php

namespace App\Livewire\User;

use App\Models\Customer;
use App\Models\Discount;
use App\Models\Product;
use App\Models\PurchaseItems;
use App\Models\SaleItems;
use Livewire\Component;
use App\Models\Sale as ModelSale;
use Illuminate\Support\Facades\DB;

class Sale extends Component
{
    public function getData(){
        $customers = Customer::latest()->get();
        $sales = ModelSale::with('salesItems.product', 'customer')->latest()->get();
        $products = Product::latest()->get();
        $discounts = Discount::latest()->get();
        return [
            $customers,
            $sales,
            $products,
            $discounts,
        ];
    }

    public function saveSale($payload){
        DB::beginTransaction();
        try{
            $Sale = ModelSale::create([
                'customer_id' =>$payload['customer_id'],
                'total_amount' => $payload['total_amount'],
                'quantity' => $payload['total_quantity'],
                'sales_date' => $payload['sales_date'],
                'expected_date' => $payload['expected_date'],
                'notes' => $payload['notes'] ?? '',
                'status' => 'draft',
                'payment_status' => 'unpaid',
                'payment_method' => $payload['payment_method'] ?? 'cash',
                'total_discount_amt' => $payload['totalTermAmount'],
            ]);

            foreach($payload['product_id'] as $i => $productId){
                SaleItems::create([
                    'sale_id'=> $Sale->id,
                    'product_id' => $productId,
                    'quantity' => $payload['quantity'][$i],
                    'selling_price' =>$payload['selling_price'][$i],
                    'subTotal'=> $payload['netAmount'][$i],
                    'dicount_amt' => $payload['termAmount'][$i],
                ]);
            }
            DB::commit();
            return true;
        }
        catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }
    }

    public function updateSale($id, $payload){
        DB::beginTransaction();
        try{
            $sale = ModelSale::find($id);
            if(!$sale){
                throw new \Exception('Sale Not Found');
            }

            $sale ->update([
                'customer_id' =>$payload['customer_id'],
                'total_amount' => $payload['total_amount'],
                'quantity' => $payload['total_quantity'],
                'sales_date' => $payload['sales_date'],
                'expected_date' => $payload['expected_date'],
                'notes' => $payload['notes'] ?? '',
                'payment_method' => $payload['payment_method'] ?? 'cash',
                'total_discount_amt' => $payload['totalTermAmount'],
            ]);

            SaleItems::where('sale_id', $id)->delete();
            foreach($payload['product_id'] as $i => $productId){
                SaleItems::create([
                    'sale_id'=> $sale->id,
                    'product_id' => $productId,
                    'quantity' => $payload['quantity'][$i],
                    'selling_price' =>$payload['selling_price'][$i],
                    'subTotal'=> $payload['netAmount'][$i],
                    'dicount_amt' => $payload['termAmount'][$i],
                ]);
            }
            DB::commit();
            return true;
        }
        catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }
    }
    public function render()
    {
        return view('livewire.user.sale');
    }
}
