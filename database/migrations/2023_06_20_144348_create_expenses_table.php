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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_id');
            $table->string('expense_date');
            $table->string('expense_no');
            $table->text('comment')->nullable();
            $table->char('expense_status', 1)->default(0)->comment('0=Pending, 1=Approved');
            $table->integer('total_amount');
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->boolean('is_deleted')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
