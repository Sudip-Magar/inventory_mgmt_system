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
        Schema::create('sale_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->string('return_reason');
            $table->string('total_amount');
            $table->string('total_quantity');
            $table->string('total_discount_amt');
            $table->string('total_paid_amount')->nullable();
            $table->string('total_due_amount')->nullable();
            $table->string('status')->default('draft')->comment('draft,confirmed,cancelled');
            $table->string('payment_status')->default('unpaid')->comment('unpaid,partial,paid');
            $table->string('payment_method')->default('cash')->comment('cash,bank');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_returns');
    }
};
