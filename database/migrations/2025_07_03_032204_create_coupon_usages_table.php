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
        Schema::create('coupon_usages', function (Blueprint $table) {
           $table->id();
            $table->foreignId('coupon_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // Customer
            $table->unsignedBigInteger('order_id')->nullable(); 
            $table->string('email')->nullable(); // For guest users
            $table->decimal('discount_amount', 10, 2);
            $table->decimal('order_total', 10, 2);
            $table->json('order_details')->nullable(); // Store order snapshot
            $table->timestamp('used_at');
            $table->timestamps();

            $table->index(['coupon_id', 'user_id']);
            $table->index(['coupon_id', 'email']);
            $table->index('used_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon_usages');
    }
};
