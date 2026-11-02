<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LeaveRequestController extends Controller
{
    /**
     * Menampilkan daftar pengajuan izin.
     */
    public function index()
    {
        $user = Auth::user();

        // Jika user memiliki permission 'leave-request view all', tampilkan semua data.
        // Jika tidak, hanya tampilkan data milik user tersebut.
        if ($user->can('leave-request view all')) {
            $leaveRequests = LeaveRequest::with(['user', 'reviewer'])->latest()->get();
        } else {
            $leaveRequests = LeaveRequest::where('user_id', $user->id)->latest()->get();
        }

        return view('leave_requests.index', compact('leaveRequests'));
    }

    /**
     * Menyimpan pengajuan izin baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'reason' => 'required|string',
            'attachment' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('leave_attachments', 'public');
        }

        LeaveRequest::create([
            'user_id'      => Auth::id(),
            'date'         => $request->date,
            'reason'       => $request->reason,
            'attachment'   => $attachmentPath,
            'status'       => 'pending',
            'submitted_by' => Auth::id(),
        ]);

        return back()->with('success', 'Pengajuan izin berhasil dikirim.');
    }

    /**
     * Mereview pengajuan izin (Approve/Reject).
     */
    public function review(Request $request, LeaveRequest $leaveRequest)
    {
        // Pengecekan izin manual menggunakan Spatie Permission
        if (!Auth::user()->can('leave-request review')) {
            abort(403, 'Anda tidak memiliki izin untuk melakukan tindakan ini.');
        }

        $request->validate([
            'status'     => 'required|in:disetujui,ditolak',
            'admin_note' => 'nullable|string'
        ]);

        $leaveRequest->update([
            'status'      => $request->status,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'admin_note'  => $request->admin_note,
        ]);

        // Jika disetujui, otomatis buat data absensi dengan status 'izin'
        if ($request->status === 'disetujui') {
            Attendance::updateOrCreate(
                [
                    'user_id' => $leaveRequest->user_id,
                    'date'    => $leaveRequest->date,
                ],
                [
                    'status' => 'izin',
                    'notes'  => 'Izin disetujui: ' . $leaveRequest->reason,
                ]
            );
        }

        return back()->with('success', 'Status pengajuan izin berhasil diupdate.');
    }

    /**
     * Menghapus data izin.
     */
    public function destroy(LeaveRequest $leaveRequest)
    {
        if (!Auth::user()->can('leave-request delete')) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus data ini.');
        }

        // Hapus file lampiran jika ada
        if ($leaveRequest->attachment) {
            Storage::disk('public')->delete($leaveRequest->attachment);
        }

        $leaveRequest->delete();

        return back()->with('success', 'Data izin berhasil dihapus.');
    }
}