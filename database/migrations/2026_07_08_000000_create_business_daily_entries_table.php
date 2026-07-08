<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_daily_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->date('entry_date');
            $table->decimal('sales_amount', 12, 2)->default(0);
            $table->decimal('disbursement_amount', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['business_id', 'entry_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_daily_entries');
    }
};
