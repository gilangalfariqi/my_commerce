<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates three tables for true relational multi-level vehicle fitment:
     * bike_brands → bike_models → product_fitments (pivot)
     *
     * This replaces the JSON metadata hack in the products table and enables
     * Meilisearch to index fitment data as structured, searchable attributes.
     */
    public function up(): void
    {
        // 1. Motorcycle Brands (e.g., Honda, Yamaha, Kawasaki, Suzuki)
        Schema::create('bike_brands', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->string('logo_path')->nullable();
            $table->string('country_of_origin', 60)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        // 2. Motorcycle Models (e.g., Honda → Vario 125, Beat, CBR150R)
        Schema::create('bike_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bike_brand_id')
                ->constrained('bike_brands')
                ->onDelete('cascade');
            $table->string('name', 150);
            $table->string('slug', 180)->unique();
            $table->string('engine_cc', 20)->nullable();       // e.g., "125cc"
            $table->string('engine_type', 50)->nullable();     // e.g., "SOHC", "DOHC"
            $table->json('years_available')->nullable();       // [2018, 2019, 2020, 2021, 2022]
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['bike_brand_id', 'is_active']);
            $table->index('slug');
        });

        // 3. Product-Fitment Pivot (which products fit which bike model + year)
        Schema::create('product_fitments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade');
            $table->foreignId('bike_model_id')
                ->constrained('bike_models')
                ->onDelete('cascade');
            $table->unsignedSmallInteger('year')->nullable();  // specific year, null = all years
            $table->string('notes', 255)->nullable();           // e.g., "Fits carb version only"
            $table->timestamps();

            // Unique constraint: one product-model-year combination
            $table->unique(['product_id', 'bike_model_id', 'year'], 'unique_product_fitment');

            $table->index(['product_id']);
            $table->index(['bike_model_id', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_fitments');
        Schema::dropIfExists('bike_models');
        Schema::dropIfExists('bike_brands');
    }
};
