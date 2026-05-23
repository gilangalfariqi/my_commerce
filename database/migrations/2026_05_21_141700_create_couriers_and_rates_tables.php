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
        Schema::create('couriers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g., "jne", "pos", "tiki"
            $table->string('name'); // e.g., "Jalur Nugraha Ekakurir (JNE)"
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('courier_id')->constrained()->onDelete('cascade');
            $table->string('service_name'); // e.g., "REG", "OKE", "YES"
            $table->decimal('cost', 12, 2);
            $table->integer('destination_city_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_rates');
        Schema::dropIfExists('couriers');
    }
};
