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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('customer_id');
            $table->string('order_date');
            $table->string('order_status');
            $table->boolean('is_confirmed')->default(0);
            $table->integer('total_products');
            $table->integer('sub_total');
            $table->integer('vat');
            $table->integer('total');
            $table->string('invoice_no');
            $table->string('payment_type');
            $table->integer('pay');
            $table->integer('due');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->boolean('is_deleted')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
