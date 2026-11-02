<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama Pos, misal: "Pos 1 Utama Gerbang Masuk"
            $table->text('address')->nullable(); // Alamat lengkap lokasi
            $table->decimal('lat', 10, 7); // Latitude titik pos
            $table->decimal('lng', 10, 7); // Longitude titik pos
            $table->integer('radius')->default(50); // Radius validasi GPS dalam meter (misal: 50 meter)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};