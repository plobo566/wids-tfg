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
        Schema::create('security_events', function (Blueprint $table) {
            $table->id();
            
            $table->string('ip_address');
            $table->string('method');
            $table->text('url');
            $table->text('user_agent')->nullable();
            $table->integer('status_code')->nullable();

            $table->text('payload')->nullable();
            $table->boolean('is_suspicious')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_events');
    }
};
