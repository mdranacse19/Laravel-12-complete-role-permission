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
        Schema::create('monitoring_items', function (Blueprint $table) {
            $table->foreignId('monitoring_id')->nullable()->constrained('monitorings')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('dynamic_form_input_id')->nullable()->constrained('dynamic_form_inputs')->cascadeOnUpdate()->nullOnDelete();
            $table->json('options');
            $table->text('value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoring_items');
    }
};
