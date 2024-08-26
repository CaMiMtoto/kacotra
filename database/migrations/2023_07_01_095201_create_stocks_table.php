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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->nullable();
            $table->integer('product_id');
            $table->float('opening',10,2)->default(0);
            $table->integer('buying_price')->default(0);
            $table->integer('stock_value')->default(0);
            $table->float('sales',10,2)->default(0);
            $table->integer('sale_value')->default(0);
            $table->float('purchases',10,2)->default(0);
            $table->integer('purchase_value')->default(0);
            $table->float('damages',10,2)->default(0);
            $table->integer('damage_value')->default(0);
            $table->float('closing',10,2)->default(0);
            $table->integer('closing_value')->default(0);
            $table->date('stock_date')->nullable();
            $table->boolean('is_deleted')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
