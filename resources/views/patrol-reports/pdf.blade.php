<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Patroli</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #1e293b; }
        h1 { font-size: 16px; margin-bottom: 2px; }
        p.subtitle { margin-top: 0; color: #64748b; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; vertical-align: top; }
        th { background-color: #f1f5f9; font-weight: bold; text-transform: uppercase; font-size: 9px; }
        .status-aman { color: #059669; font-weight: bold; }
        .status-mencurigakan { color: #d97706; font-weight: bold; }
        .status-temuan_darurat { color: #e11d48; font-weight: bold; }
        .footer { margin-top: 16px; font-size: 9px; color: #94a3b8; text-align: right; }
    </style>
</head>
<body>
    <h1>Laporan Patroli — PT SERGAP 17</h1>
    <p class="subtitle">Dicetak pada {{ now()->format('d M Y, H:i') }} · Total {{ $reports->count() }} laporan</p>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>Petugas</th>
                <th>Pos Jaga</th>
                <th>Status</th>
                <th>Laporan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $report)
                <tr>
                    <td>{{ $report->date?->format('d-m-Y') }}</td>
                    <td>{{ $report->patrol_at?->format('H:i') }}</td>
                    <td>{{ $report->user->name ?? '-' }}</td>
                    <td>{{ $report->location->name ?? '-' }}</td>
                    <td class="status-{{ $report->status }}">
                        {{ ucfirst(str_replace('_', ' ', $report->status)) }}
                    </td>
                    <td>{{ $report->report }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center; color:#94a3b8;">Tidak ada laporan pada rentang ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">SERGAP17 &copy; {{ date('Y') }} — Dokumen ini digenerate otomatis oleh sistem.</div>
</body>
</html>