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
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete()->after('building_id');
        });

        // Migrate existing assignments: copy tenant.room_id → room.tenant_id
        \Illuminate\Support\Facades\DB::statement('
            UPDATE rooms
            SET tenant_id = (
                SELECT id FROM tenants WHERE tenants.room_id = rooms.id LIMIT 1
            )
        ');
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
        });
    }
};
