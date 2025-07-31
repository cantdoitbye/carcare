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
        Schema::create('products', function (Blueprint $table) {
          $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->string('sku')->unique();
            $table->string('brand')->nullable();
            
            // Pricing
            $table->decimal('price', 10, 2);
            $table->decimal('compare_price', 10, 2)->nullable();
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->decimal('discount_percentage', 5, 2)->default(0);
            
            // Inventory
            $table->integer('quantity')->default(0);
            $table->integer('min_quantity')->default(0); // Low stock alert
            $table->boolean('track_inventory')->default(true);
            $table->enum('stock_status', ['in_stock', 'out_of_stock', 'low_stock'])->default('in_stock');
            
            // Physical attributes
            $table->decimal('weight', 8, 3)->nullable(); // in kg
            $table->decimal('length', 8, 2)->nullable(); // in cm
            $table->decimal('width', 8, 2)->nullable(); // in cm
            $table->decimal('height', 8, 2)->nullable(); // in cm
            
            // Status and visibility
            $table->enum('status', ['active', 'inactive', 'draft'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_digital')->default(false);
            
            // SEO and meta
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('tags')->nullable(); 
            $table->json('attributes')->nullable(); 
            
            $table->timestamps();

            $table->index(['status', 'is_featured']);
            $table->index('brand');
            $table->index('stock_status');
            $table->fullText(['name', 'description', 'sku']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
