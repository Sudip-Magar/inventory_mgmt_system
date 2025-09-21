<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purhchase_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purhchase_return_id')->constrained('purhchase_returns');
            $table->foreignId('product_id')->constrained('products');
            $table->string('quantity');
            $table->string('cost_price');
            $table->string('subTotal');
            $table->string('disount_amt');
            $table->text('return_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purhchase_return_items');
    }
};
