<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landing_id')->constrained()->onDelete('cascade');
            $table->string('ip_address');
            $table->timestamps();
            
            $table->index(['landing_id', 'ip_address']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_views');
    }
};
