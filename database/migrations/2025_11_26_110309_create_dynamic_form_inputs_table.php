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
        Schema::create('dynamic_form_inputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dynamic_form_id')->constrained('dynamic_forms')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('form_input_id')->constrained('form_inputs')->cascadeOnUpdate()->cascadeOnDelete();
            $table->tinyInteger('sort');
            $table->string('label');
            $table->string('placeholder');
            $table->text('options')->nullable();
            $table->boolean('required')->default(false);
            $table->boolean('has_action')->default(false);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dynamic_form_inputs');
    }
};
