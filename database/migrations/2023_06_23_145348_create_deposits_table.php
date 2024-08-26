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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->string('method_id');
            $table->string('bank_id');
            $table->string('account_no');
            $table->string('deposit_date');
            $table->string('deposit_code');
            $table->string('transaction_id');
            $table->char('deposit_status', 1)->default(0)->comment('0=Pending, 1=Approved');
            $table->integer('amount');
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
        Schema::dropIfExists('deposits');
    }
};
