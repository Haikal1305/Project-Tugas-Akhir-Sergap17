<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('location_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->time('shift_start')->nullable(); // Jam mulai shift personel di pos ini
            $table->time('shift_end')->nullable();   // Jam selesai shift personel di pos ini
            $table->date('assigned_date')->nullable(); // Tanggal mulai penugasan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('location_user');
    }
};