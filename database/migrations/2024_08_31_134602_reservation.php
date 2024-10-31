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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->date('reservation_date');
            $table->enum('type_reservation', ['classes', 'practices', 'events']);
            $table->enum('status',['requested', 'confirmed', 'rejected'])->default('requested');
            $table->foreignId('id_schedules_lab')->constrained(
                table: 'schedules_labs', indexName: 'reservations_id_schedules_lab'
            )->onUpdate('cascade')->onDelete('cascade');
            
            $table->foreignId('id_user')->constrained(
                table: 'users', indexName: 'reservations_id_user'
            )->onUpdate('cascade')->onDelete('cascade');
            
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
