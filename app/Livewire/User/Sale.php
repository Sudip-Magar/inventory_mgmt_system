<?php

namespace App\Livewire\User;

use App\Models\Customer;
use App\Models\Discount;
use App\Models\Product;
use App\Models\SaleItems;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use Livewire\Component;
use App\Models\Sale as ModelSale;
use Illuminate\Support\Facades\DB;
use App\Models\StockMovement;


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

    public function confirmOrder($id){
        return DB::transaction((function () use ($id) {
            $sale = ModelSale::lockForUpdate()->find($id);
            if(!$sale || $sale->status == 'confirmed'){
                return false;
            }

            $saleItems = SaleItems::where('sale_id',$id)
            ->get(['product_id','quantity']);

            if($saleItems->isEmpty()){
                return false;
            }

            $quantityByProdut = $saleItems
            ->groupBy('product_id')
            ->map(Function($items){
                return $items->sum('quantity');
            });

            foreach($quantityByProdut as $productId => $quantityToSub){
                $currentStock = Product::where('id', $productId)->value('stock');
                $newStock = max(0,(int)$currentStock - (int)$quantityToSub);
                Product::where('id', $productId)->update(['stock' => $newStock]);
            }
            $sale->status = 'confirmed';
            $sale->payment_status = 'paid';
            foreach($saleItems as $item){
                StockMovement::create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'type' => 'sale',
                ]);
            }

            $sale->save();
            return true;
        }));
    }

    public function cancelOrder($id){
        return DB::transaction(function () use ($id){
            $sale = ModelSale::lockForUpdate()->find($id);
            if(!$sale || $sale->status == 'cancelled'){
                return false;
            }
            if($sale->status === 'draft'){
                $sale->status = 'cancelled';
                $sale->save();
                return true;
            }

            $saleItems = SaleItems::where('sale_id',$id)
            ->lockForUpdate()
            ->get(['product_id','quantity']);

            if($saleItems->isEmpty()){
                throw new \Exception('No Sale Items Found');
            }

            $quantityByProdut = $saleItems
            ->groupBy('product_id')
            ->map(Function($items){
                return $items->sum('quantity');
            });

            foreach($quantityByProdut as $productId => $quantityToAdd){
                Product::where('id', $productId)->increment('stock', $quantityToAdd);
            }

            $sale->status = 'cancelled';
            foreach($saleItems as $item){
                StockMovement::create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'type' => 'sale_cancel',
                ]);
            }
            $sale->save();
        });
    }

    public function resetToDraft($id){
        $sale = ModelSale::find($id);
        if($sale){
            $sale->status = 'draft';
            $sale->save();
            return true;
        }
        return false;
    }

    public function createSaleReturn($payload){
        DB::beginTransaction();
        try{
            $saleReturn = SaleReturn::create([
                'sale_id' => $payload['sale_id'],
                'return_reason' => $payload['reason'],
                'total_amount' => $payload['total_amount'],
                'total_quantity' => $payload['total_quantity'],
                'total_discount_amt'=>$payload['totalTermAmount'],
                'status'=>'draft',
                'payment_method'=>'unpaid',
            ]);

             foreach ($payload['product_id'] as $i => $productId){
                    SaleReturnItem::create([
                        'sale_return_id' => $saleReturn->id,
                        'product_id' => $productId,
                        'quantity' => $payload['quantity'][$i],
                        'cost_price' => $payload['selling_price'][$i],
                        'subTotal' => $payload['netAmount'][$i],
                        'disount_amt' => $payload['termAmount'][$i],
                    ]);
                }   
            DB::commit();
            return $this->redirectRoute('sale-return');
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
