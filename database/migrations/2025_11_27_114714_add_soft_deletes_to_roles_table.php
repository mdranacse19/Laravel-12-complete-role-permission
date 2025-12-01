<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->boolean('for_rmg')->default(0)->after('guard_name');
            $table->boolean('deleteable')->default(1)->after('for_rmg');
            $table->boolean('hideable')->default(0)->after('deleteable');
            $table->softDeletes();
        });

        // Check if the unique index exists and drop it
        $indexes = DB::select("SHOW INDEX FROM roles WHERE Key_name = 'roles_name_guard_name_unique'");

        if (!empty($indexes)) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropUnique('roles_name_guard_name_unique');
            });
        }

        // Add a composite unique index that includes deleted_at
        // This allows the same name/guard_name for soft-deleted records
        Schema::table('roles', function (Blueprint $table) {
            $table->unique(['name', 'guard_name', 'deleted_at'], 'roles_name_guard_name_deleted_at_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['for_rmg', 'deleteable', 'hideable']);
            $table->dropSoftDeletes();
        });

        Schema::table('roles', function (Blueprint $table) {
            // Drop the new unique constraint
            $table->dropUnique('roles_name_guard_name_deleted_at_unique');
        });

        // Restore the original unique constraint if it doesn't exist
        $indexes = DB::select("SHOW INDEX FROM roles WHERE Key_name = 'roles_name_guard_name_unique'");

        if (empty($indexes)) {
            Schema::table('roles', function (Blueprint $table) {
                $table->unique(['name', 'guard_name'], 'roles_name_guard_name_unique');
            });
        }
    }
};
