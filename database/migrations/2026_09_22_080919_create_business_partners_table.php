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
        Schema::create('business_partners', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->unique()->autoIncrement();
            $table->timestamps();
            $table->boolean('is_active')->default(true);
            $table->enum('type', ['person', 'company'])->default('person');
            $table->string('name');
            $table->string('family_name');
            $table->string('company_name')->nullable();
            $table->string('surname')->nullable(true);
            $table->string('vat_number')->nullable()->unique();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_partners');
    }
};
