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
        Schema::create('rmgs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type')->nullable()->comment('Type for the association')->constrained('association_types')->cascadeOnUpdate()->nullOnDelete();
            $table->char('reg_no', 60)->unique();
            $table->string('name');
            $table->string('bn_name');
            $table->text('address')->nullable();
            $table->string('telephone')->nullable();
            $table->string('contact_person');
            $table->string('bn_contact_person');
            $table->char('contact_mobile', 11);
            $table->char('contact_phone', 20)->nullable();
            $table->string('contact_email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rmgs');
    }
};
