<?php

namespace App\Http\Controllers;

use App\Exports\PatrolReportExport;
use App\Models\Location;
use App\Models\PatrolReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lauthz\Facades\Enforcer;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Controller khusus PELAPORAN (export PDF/Excel) laporan patroli.
 * Otorisasi 'export' memakai relasi ReBAC lokasi yang sama dengan LocationController
 * (guard 'basic'), bukan guard 'patrol' terpisah lagi.
 */
class PatrolReportExportController extends Controller
{
    protected string $casbinAction = 'export';

    /**
     * Halaman pelaporan: pilih pos & rentang tanggal sebelum export.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $locations = $user->can('patrol-report manage-all')
            ? Location::all()
            : Location::all()->filter(fn ($loc) => Enforcer::guard('basic')->enforce(
                strval($user->id), 'location_' . $loc->id, $this->casbinAction
            ))->values();

        return view('patrol-reports.pelaporan', compact('locations'));
    }

    public function exportExcel(Request $request)
    {
        $reports = $this->authorizedReports($request);

        return Excel::download(
            new PatrolReportExport($reports),
            'laporan-patroli-' . now()->format('Ymd-His') . '.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $reports = $this->authorizedReports($request);

        $pdf = Pdf::loadView('patrol-reports.pdf', compact('reports'));

        return $pdf->download('laporan-patroli-' . now()->format('Ymd-His') . '.pdf');
    }

    /**
     * Ambil laporan sesuai izin export user, dengan filter opsional
     * pos & rentang tanggal dari query string.
     */
    private function authorizedReports(Request $request)
    {
        $request->validate([
            'location_id' => 'nullable|exists:locations,id',
            'date_from'   => 'nullable|date',
            'date_to'     => 'nullable|date|after_or_equal:date_from',
        ]);

        $user = Auth::user();
        $hasGlobalAccess = $user->can('patrol-report manage-all');

        $query = PatrolReport::with(['user', 'location']);

        if ($request->filled('location_id')) {
            $locationId = (int) $request->location_id;

            if (!$hasGlobalAccess && !Enforcer::guard('basic')->enforce(
                strval($user->id), 'location_' . $locationId, $this->casbinAction
            )) {
                abort(403, 'Akses Ditolak: Anda tidak memiliki hak export untuk pos ini.');
            }

            $query->where('location_id', $locationId);
        } elseif (!$hasGlobalAccess) {
            $allowedIds = Location::all()->filter(fn ($loc) => Enforcer::guard('basic')->enforce(
                strval($user->id), 'location_' . $loc->id, $this->casbinAction
            ))->pluck('id');

            if ($allowedIds->isEmpty()) {
                abort(403, 'Akses Ditolak: Anda tidak memiliki hak export laporan patroli manapun.');
            }

            $query->whereIn('location_id', $allowedIds);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('patrol_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('patrol_at', '<=', $request->date_to);
        }

        return $query->latest('patrol_at')->get();
    }
}