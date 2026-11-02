<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class MarkAbsentUsersCommand extends Command
{
    protected $signature = 'attendance:mark-alfa {--date= : Tanggal spesifik (Y-m-d) jika bukan hari ini}';

    protected $description = 'Menandai user sebagai alfa jika tidak absen dan tidak ada izin yang disetujui';

    public function handle()
    {
        $dateString = $this->option('date') ?: Carbon::today()->toDateString();
        $date = Carbon::parse($dateString);

        // Ambil secara dinamis user yang memiliki permission 'attendance create' 
        // (biasanya ini hak akses khusus untuk satpam/petugas lapangan)
        $users = User::permission('attendance create')->get();

        // Jika tidak ada user dengan permission tersebut, fallback ambil semua user yang bukan admin (opsional)
        if ($users->isEmpty()) {
            $users = User::all(); // atau sesuaikan dengan kebutuhan logic aplikasimu
        }
        
        $countAlfa = 0;

        foreach ($users as $user) {
            $hasAttendance = Attendance::where('user_id', $user->id)
                ->whereDate('date', $date->toDateString())
                ->exists();

            $hasApprovedLeave = LeaveRequest::where('user_id', $user->id)
                ->whereDate('date', $date->toDateString())
                ->where('status', 'disetujui')
                ->exists();

            if (!$hasAttendance && !$hasApprovedLeave) {
                Attendance::create([
                    'user_id' => $user->id,
                    'date' => $date->toDateString(),
                    'status' => 'alfa',
                    'notes' => 'Otomatis ditandai sistem karena tidak absen dan tidak ada izin.',
                ]);
                
                $countAlfa++;
            }
        }

        $this->info("Berhasil menandai {$countAlfa} user sebagai alfa untuk tanggal {$date->toDateString()}.");
    }
}