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
        Schema::create('patrol_reports', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke satpam yang bertugas membuat laporan
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Relasi ke pos jaga / location tempat patroli dilakukan
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            
            // Tanggal dan waktu laporan dikirim
            $table->date('date');
            $table->timestamp('patrol_at');
            
            // Isi laporan atau catatan kondisi pos
            $table->text('report');
            
            // Status kondisi pos (misal: aman, mencurigakan, ada temuan, dsb)
            $table->string('status')->default('aman'); 
            
            // Bukti foto saat patroli (opsional/disarankan untuk validasi)
            $table->string('photo')->nullable();
            
            // Koordinat GPS saat satpam mengirim laporan (opsional untuk verifikasi lokasi riil)
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patrol_reports');
    }
};