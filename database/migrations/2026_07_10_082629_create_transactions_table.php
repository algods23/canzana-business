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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('account_type', ['conel', '128', 'rental', 'agriculture', 'tilapia'])->index();
            $table->enum('module_type', ['income', 'expense', 'payment', 'balance_adjustment'])->index();
            $table->decimal('amount', 12, 2);
            $table->string('description');
            $table->date('transaction_date')->index();
            $table->string('reference_type')->nullable(); // e.g., 'tenant', 'expense', 'payment'
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('completed');
            $table->string('notes')->nullable();
            $table->timestamps();
            
            $table->index(['account_type', 'module_type']);
            $table->index(['account_type', 'transaction_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
