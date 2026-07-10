<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buildings', function (Blueprint $table): void {
            $table->string('rental_mode')->default('rooms')->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('buildings', function (Blueprint $table): void {
            $table->dropColumn('rental_mode');
        });
    }
};
