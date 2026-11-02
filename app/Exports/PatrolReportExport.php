<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PatrolReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected Collection $reports;

    public function __construct(Collection $reports)
    {
        $this->reports = $reports;
    }

    public function collection(): Enumerable
    {
        return $this->reports;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Waktu',
            'Petugas',
            'Pos Jaga',
            'Status',
            'Laporan',
            'Latitude',
            'Longitude',
        ];
    }

    public function map($report): array
    {
        return [
            $report->date?->format('d-m-Y'),
            $report->patrol_at?->format('H:i'),
            $report->user->name ?? '-',
            $report->location->name ?? '-',
            ucfirst(str_replace('_', ' ', $report->status)),
            $report->report,
            $report->lat,
            $report->lng,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}