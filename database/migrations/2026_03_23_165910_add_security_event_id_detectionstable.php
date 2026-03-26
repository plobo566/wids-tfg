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
        Schema::table('detections', function(Blueprint $table){

            $table->foreignId('security_event_id')
                  ->constrained()
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
          Schema::table('detections', function(Blueprint $table){

            $table->dropForeign(['security_event_id']);
            $table->dropColumn('security_event_id');
        });
    }
};
