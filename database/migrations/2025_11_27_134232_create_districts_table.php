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
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_id')->nullable()->comment('Division for the district')->constrained('divisions')->cascadeOnUpdate()->nullOnDelete();
            $table->string('name')->comment('Name of the district');
            $table->string('bn_name')->comment('District name in bengali');
            $table->string('url')->nullable()->comment('Official website address');
            $table->decimal('lat', 8, 6)->nullable()->comment('Latitudes of the district');
            $table->decimal('lon', 9, 6)->nullable()->comment('Longitudes of the district');
            $table->string('coordinate')->nullable();
            $table->char('code', 16)->nullable();
            $table->boolean('status')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};
