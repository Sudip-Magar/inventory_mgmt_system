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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers');
            $table->string('total_amount');
            $table->string('quantity');
            $table->date('sales_date');
            $table->date('expected_date');
            $table->string('status')->default('draft')->comment('draft,received,cancel');
            $table->string('payment_status')->default('unpaid')->comment('unpaid,partial,paid');
            $table->string('payment_method')->default('cash')->comment('cash,bank');
            $table->string('notes');
            $table->string('total_received_amount')->nullable();
            $table->string('total_due_amount')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
