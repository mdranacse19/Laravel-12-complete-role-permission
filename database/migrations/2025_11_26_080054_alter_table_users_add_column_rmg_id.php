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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('rmg_id')->after('id')->nullable()->comment('RMG for the user')->constrained('rmgs')->cascadeOnUpdate()->nullOnDelete();
            $table->string('bn_name')->after('name')->nullable();
            $table->boolean('is_active')->after('password')->default(true);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_rmg_id_foreign');
            $table->dropIndex('users_rmg_id_foreign');
            $table->dropColumn(['rmg_id', 'is_active']);
        });
    }
};
