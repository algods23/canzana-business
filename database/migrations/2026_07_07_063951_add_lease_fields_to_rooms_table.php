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
        Schema::table('rooms', function (Blueprint $table) {
            $table->date('lease_start')->nullable()->after('tenant_id');
            $table->date('lease_end')->nullable()->after('lease_start');
        });

        // Migrate existing lease dates from tenants → their assigned rooms
        \Illuminate\Support\Facades\DB::statement('
            UPDATE rooms
            SET lease_start = tenants.lease_start,
                lease_end   = tenants.lease_end,
                rent        = COALESCE(tenants.rent, rooms.rent)
            FROM tenants
            WHERE tenants.id = rooms.tenant_id
            AND rooms.tenant_id IS NOT NULL
        ');
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['lease_start', 'lease_end']);
        });
    }
};
