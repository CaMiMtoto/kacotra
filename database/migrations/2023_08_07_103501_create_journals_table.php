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
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned();
            $table->date('journal_date');
            $table->string('description')->nullable();
            $table->string('reference')->nullable();
            $table->integer('opening')->default(0);
            $table->integer('debit')->default(0);
            $table->integer('credit')->default(0);
            $table->integer('due')->default(0);
            $table->integer('refund')->default(0);
            $table->integer('balance')->default(0);
            $table->string('comment')->nullable();
            $table->boolean('is_deleted')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};
