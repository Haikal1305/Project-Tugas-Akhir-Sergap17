<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Satpam yang absen
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete(); // Pos tempat dia bertugas/absen

            // Waktu & Status
            $table->date('date'); // Tanggal absensi
            $table->timestamp('clock_in_at')->nullable(); // Waktu masuk
            $table->timestamp('clock_out_at')->nullable(); // Waktu keluar/selesai
            $table->enum('status', ['belum_hadir', 'hadir', 'terlambat', 'pulang_cepat', 'izin', 'alfa'])->default('belum_hadir');
            // Data Lokasi Aktual Satpam saat Masuk (GPS Leaflet HP)
            $table->decimal('clock_in_lat', 10, 7)->nullable();
            $table->decimal('clock_in_lng', 10, 7)->nullable();
            $table->string('clock_in_photo')->nullable();
            $table->text('clock_in_address')->nullable();

            // Data Lokasi Aktual Satpam saat Keluar (GPS Leaflet HP)
            $table->decimal('clock_out_lat', 10, 7)->nullable();
            $table->decimal('clock_out_lng', 10, 7)->nullable();
            $table->string('clock_out_photo')->nullable();
            $table->text('clock_out_address')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
