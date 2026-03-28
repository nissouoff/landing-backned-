<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landing_id')->constrained()->onDelete('cascade');
            $table->string('product_id');
            $table->string('product_name');
            $table->string('product_price');
            $table->string('product_photo')->nullable();
            $table->string('customer_name');
            $table->string('customer_firstname');
            $table->string('customer_phone');
            $table->string('wilaya');
            $table->string('commune')->nullable();
            $table->text('address')->nullable();
            $table->enum('delivery_type', ['home', 'pickup']);
            $table->string('otp_code', 6)->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->enum('status', ['pending', 'verified', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['landing_id', 'status']);
            $table->index('customer_phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
