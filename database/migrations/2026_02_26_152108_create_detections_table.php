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
        Schema::create('detections', function (Blueprint $table) {
            $table->id();

            $table->string('type'); //si es regla o anomalía
            $table->string('rule_name'); //nombre de la regla
            $table->string('entity_type'); // si es usuario o ip
            $table->string('entity_value');  //valor de usuario o la ip directamente

            $table->integer('score')->default(0); //score por defecto es 0 (no sospechoso)
            
            $table->timestamp('window_start')->nullable();
            $table->timestamp('window_end')->nullable();

            $table->json('details')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detections');
    }
};
