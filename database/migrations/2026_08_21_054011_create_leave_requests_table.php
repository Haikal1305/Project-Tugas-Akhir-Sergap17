<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Yang izin
            $table->date('date'); // Tanggal izin

            $table->text('reason'); // Alasan izin
            $table->string('attachment')->nullable(); // Opsional: foto surat/bukti

            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');

            // Siapa yang input pengajuan ini (bisa user sendiri, bisa admin atas nama user)
            $table->foreignId('submitted_by')->constrained('users')->cascadeOnDelete();

            // Review oleh admin/supervisor
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('admin_note')->nullable(); // Alasan tolak, atau catatan approval

            $table->timestamps();

            $table->index(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};