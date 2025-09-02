<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors');
            $table->string('total_amount');
            $table->string('total_quantity');
            $table->date('order_date');
            $table->date('expected_date');
            $table->string('status')->default('pending')->comment('pending,received,cancel');
            $table->string('payment_status')->default('uppaid')->comment('unpaid,partial,paid');
            $table->string('payment_method')->default('cash')->comment('cash,bank');
            $table->string('notes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
