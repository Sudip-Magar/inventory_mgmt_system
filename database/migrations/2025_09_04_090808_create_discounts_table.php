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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('code');                  // e.g. DISC10
            $table->string('name');                  // human readable
            $table->enum('sign', ['+', '-']);        // discount/increase
            $table->decimal('rate', 8, 2);           // percentage or flat amount
            $table->boolean('is_item_wise')->default(false); // item-wise or global
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
