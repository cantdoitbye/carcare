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
        Schema::create('coupons', function (Blueprint $table) {
          $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            
            // Discount details
            $table->enum('type', ['fixed', 'percentage']); // Fixed amount or percentage
            $table->decimal('value', 10, 2); // Discount value
            $table->decimal('minimum_amount', 10, 2)->nullable(); // Minimum order amount
            $table->decimal('maximum_discount', 10, 2)->nullable(); // Maximum discount for percentage
            
            // Usage limits
            $table->integer('usage_limit')->nullable(); // Total usage limit
            $table->integer('usage_limit_per_customer')->nullable(); // Per customer limit
            $table->integer('used_count')->default(0); // How many times used
            
            // Date constraints
            $table->datetime('starts_at')->nullable();
            $table->datetime('expires_at')->nullable();
            
            // Restrictions
            $table->json('applicable_products')->nullable(); // Product IDs
            $table->json('applicable_categories')->nullable(); // Category IDs
            $table->json('excluded_products')->nullable(); // Excluded product IDs
            $table->json('excluded_categories')->nullable(); // Excluded category IDs
            $table->boolean('free_shipping')->default(false);
            $table->boolean('exclude_sale_items')->default(false);
            
            // Status and visibility
            $table->enum('status', ['active', 'inactive', 'expired'])->default('active');
            $table->boolean('is_public')->default(true); // Public or private coupon
            
            // Admin info
            $table->foreignId('created_by')->nullable()->constrained('admin_users')->onDelete('set null');
            $table->timestamps();

            $table->index(['code', 'status']);
            $table->index(['starts_at', 'expires_at']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
