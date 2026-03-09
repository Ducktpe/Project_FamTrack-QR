<?php

namespace App\Exports;

use App\Models\DistributionEvent;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Collection;

/**
 * EventHouseholdsExport
 *
 * Generates an Excel (.xlsx) or PDF report for a distribution event.
 *
 * ── Data sources ──────────────────────────────────────────────────────────────
 *  'households'  – pulls via $event->households() (with qrCode + distributionLog)
 *  'logs'        – pulls via $event->logs (with household + staff relations)
 *
 * ── Usage ─────────────────────────────────────────────────────────────────────
 *  // Excel
 *  return Excel::download(
 *      new EventHouseholdsExport($event, 'excel', 'households'),
 *      'households.xlsx'
 *  );
 *
 *  // PDF (households source)
 *  return (new EventHouseholdsExport($event, 'pdf', 'households'))->downloadPdf('report.pdf');
 *
 *  // PDF (logs source)
 *  return (new EventHouseholdsExport($event, 'pdf', 'logs'))->downloadPdf('report.pdf');
 */
class EventHouseholdsExport implements WithEvents, ShouldAutoSize
{
    protected DistributionEvent $event;

    /** @var 'excel'|'pdf' */
    protected string $format;

    /** @var 'households'|'logs' */
    protected string $source;

    public function __construct(
        DistributionEvent $event,
        string $format = 'excel',   // 'excel' | 'pdf'
        string $source = 'households' // 'households' | 'logs'
    ) {
        $this->event  = $event;
        $this->format = $format;
        $this->source = $source;
    }

    // ══════════════════════════════════════════════════════════════════════════
    // DATA LAYER — normalise both sources into a unified Collection
    // Each item exposes the same keys regardless of source.
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Returns a Collection of plain objects / arrays with normalised keys:
     *
     *   id, event_name, serial_code, household_head_name, barangay,
     *   distributed_by, distributed_at (Carbon|null), goods_detail, remarks,
     *   has_qr_code (bool, households source only — false for logs source)
     */
    protected function getData(): Collection
    {
        if ($this->source === 'households') {
            return $this->event
                ->households()
                ->with(['qrCode', 'distributionLog.staff'])
                ->get()
                ->map(fn ($h) => (object) [
                    'id'                  => $h->id,
                    'event_name'          => $this->event->name ?? $this->event->relief_type ?? '—',
                    'serial_code'         => $h->serial_code ?? '—',
                    'household_head_name' => $h->household_head_name ?? 'N/A',
                    'barangay'            => $h->barangay ?? 'N/A',
                    'distributed_by'      => $h->distributionLog?->staff?->name ?? '—',
                    'distributed_at'      => $h->distributionLog?->distributed_at,
                    'goods_detail'        => $h->goods_detail ?? '',
                    'remarks'             => $h->remarks ?? '',
                    'has_qr_code'         => (bool) $h->qrCode,
                ]);
        }

        // source === 'logs'
        return $this->event
            ->logs()
            ->with(['household', 'staff'])
            ->get()
            ->map(fn ($log) => (object) [
                'id'                  => $log->id,
                'event_name'          => $this->event->name ?? $this->event->relief_type ?? '—',
                'serial_code'         => $log->serial_code ?? '—',
                'household_head_name' => $log->household?->household_head_name ?? 'N/A',
                'barangay'            => $log->household?->barangay ?? 'N/A',
                'distributed_by'      => $log->staff?->name ?? '—',
                'distributed_at'      => $log->distributed_at,
                'goods_detail'        => $log->goods_detail ?? '',
                'remarks'             => $log->remarks ?? '',
                'has_qr_code'         => false, // logs source doesn't carry QR info
            ]);
    }

    /**
     * Aggregated stats derived from normalised data.
     */
    protected function getStats(Collection $data): array
    {
        return [
            'total'             => $data->count(),
            'unique_households' => $data->pluck('household_head_name')->unique()->count(),
            'barangays'         => $data->pluck('barangay')->filter()->unique()->count(),
            'with_qr'           => $data->filter(fn ($r) => $r->has_qr_code)->count(),
            'missing_qr'        => $data->filter(fn ($r) => !$r->has_qr_code)->count(),
        ];
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PDF EXPORT
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Returns a DomPDF response for download.
     *
     * @param  string $filename  e.g. 'report.pdf'
     */
    public function downloadPdf(string $filename = 'report.pdf')
    {
        $data   = $this->getData();
        $stats  = $this->getStats($data);
        $event  = $this->event;

        $barangayName = $data->first()?->barangay ?? 'All Barangays';

        $pdf = Pdf::loadView('exports.event-households-pdf', compact(
            'event', 'data', 'stats', 'barangayName'
        ))->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }

    /**
     * Returns a DomPDF response for inline streaming.
     */
    public function streamPdf(string $filename = 'report.pdf')
    {
        $data   = $this->getData();
        $stats  = $this->getStats($data);
        $event  = $this->event;

        $barangayName = $data->first()?->barangay ?? 'All Barangays';

        $pdf = Pdf::loadView('exports.event-households-pdf', compact(
            'event', 'data', 'stats', 'barangayName'
        ))->setPaper('a4', 'landscape');

        return $pdf->stream($filename);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // EXCEL EXPORT  (Maatwebsite\Excel — AfterSheet event)
    // ══════════════════════════════════════════════════════════════════════════

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $e) {
                $sheet = $e->sheet->getDelegate();
                $event = $this->event;

                $data  = $this->getData();
                $stats = $this->getStats($data);

                $barangayName = $data->first()?->barangay ?? 'All Barangays';
                $exportedAt   = now()->format('F d, Y  h:i A');
                $totalRows    = $stats['total'];
                $dataStart    = 6;
                $dataEnd      = max($dataStart, $dataStart + $totalRows - 1);

                // ── COLOR PALETTE ─────────────────────────────────────────────
                // Header bar rows 1-2 : #002EC0  (royal blue)
                // Column header row 5 : gradient #FFFF00 → #FFC000
                // Odd data rows       : #FFFFFF
                // Even data rows      : #F2F2F2
                // Stats bar           : blue #D6E4FF · green #DCFCE7 · amber #FFF7ED

                // ── ROWS 1-2: Title header bar ────────────────────────────────
                $sheet->setCellValue('A1', 'MDRRMO RBI System');
                $sheet->setCellValue('I1', 'Exported at: ' . $exportedAt);
                $sheet->mergeCells('A1:D1');
                $sheet->mergeCells('E1:I1');
                $sheet->setCellValue('A2', 'List of Households');
                $sheet->setCellValue('I2', $barangayName);
                $sheet->mergeCells('A2:D2');
                $sheet->mergeCells('E2:I2');

                foreach (['A1:I1', 'A2:I2'] as $range) {
                    $sheet->getStyle($range)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FF002EC0');
                    $sheet->getStyle($range)->getFont()
                        ->setName('Calibri')->setSize(11)->setBold(true)
                        ->getColor()->setARGB('FFFFFFFF');
                    $sheet->getStyle($range)->getAlignment()
                        ->setVertical(Alignment::VERTICAL_CENTER);
                }
                $sheet->getStyle('A2:I2')->getFont()->setBold(false)->getColor()->setARGB('FFD0DCF5');
                $sheet->getStyle('E1:I1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('E2:I2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getRowDimension(1)->setRowHeight(22);
                $sheet->getRowDimension(2)->setRowHeight(18);

                // ── ROW 3: Spacer ─────────────────────────────────────────────
                $sheet->getRowDimension(3)->setRowHeight(6);

                // ── ROW 4: Stats bar ──────────────────────────────────────────
                // For 'households' source: show Total / With QR / Missing QR
                // For 'logs' source: show Total / Unique Households / Barangays
                if ($this->source === 'households') {
                    $sheet->setCellValue('A4', 'Total Distributed: ' . $stats['total']);
                    $sheet->setCellValue('D4', 'With QR Code: '       . $stats['with_qr']);
                    $sheet->setCellValue('G4', 'Missing QR: '         . $stats['missing_qr']);
                } else {
                    $sheet->setCellValue('A4', 'Total Distributions: ' . $stats['total']);
                    $sheet->setCellValue('D4', 'Unique Households: '   . $stats['unique_households']);
                    $sheet->setCellValue('G4', 'Barangays Covered: '   . $stats['barangays']);
                }

                $sheet->mergeCells('A4:C4');
                $sheet->mergeCells('D4:F4');
                $sheet->mergeCells('G4:I4');

                $sheet->getStyle('A4:C4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFD6E4FF');
                $sheet->getStyle('A4:C4')->getFont()->setName('Calibri')->setSize(10)->setBold(true)->getColor()->setARGB('FF002EC0');

                $sheet->getStyle('D4:F4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFDCFCE7');
                $sheet->getStyle('D4:F4')->getFont()->setName('Calibri')->setSize(10)->setBold(true)->getColor()->setARGB('FF16A34A');

                $sheet->getStyle('G4:I4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFF7ED');
                $sheet->getStyle('G4:I4')->getFont()->setName('Calibri')->setSize(10)->setBold(true)->getColor()->setARGB('FFD97706');

                foreach (['A4', 'D4', 'G4'] as $c) {
                    $sheet->getStyle($c)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                }
                $sheet->getRowDimension(4)->setRowHeight(20);

                // ── ROW 5: Column headers ─────────────────────────────────────
                $headers = [
                    'A' => 'ID',
                    'B' => 'Event',
                    'C' => 'Serial',
                    'D' => 'Household Head',
                    'E' => 'Barangay',
                    'F' => 'Distributed By',
                    'G' => 'Distributed At',
                    'H' => 'Goods Detail',
                    'I' => 'Remarks',
                ];

                $medBorder = ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']];

                foreach ($headers as $col => $label) {
                    $cell = $sheet->getCell("{$col}5");
                    $cell->setValue($label);

                    $cell->getStyle()->getFill()
                        ->setFillType(Fill::FILL_GRADIENT_LINEAR)
                        ->setRotation(90)
                        ->getStartColor()->setARGB('FFFFFF00');  // top: yellow
                    $cell->getStyle()->getFill()
                        ->getEndColor()->setARGB('FFFFC000');    // bottom: golden

                    $cell->getStyle()->getFont()
                        ->setName('Calibri')->setSize(11)->setBold(true)
                        ->getColor()->setARGB('FF000000');

                    $cell->getStyle()->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                        ->setVertical(Alignment::VERTICAL_CENTER);

                    $cell->getStyle()->getBorders()->applyFromArray(['allBorders' => $medBorder]);
                }

                $sheet->getRowDimension(5)->setRowHeight(20);
                $sheet->setAutoFilter('A5:I5');

                // ── ROWS 6+: Data rows ────────────────────────────────────────
                foreach ($data as $index => $row) {
                    $rowNum = $dataStart + $index;

                    $values = [
                        'A' => $row->id,
                        'B' => $row->event_name,
                        'C' => $row->serial_code,
                        'D' => $row->household_head_name,
                        'E' => $row->barangay,
                        'F' => $row->distributed_by,
                        'G' => $row->distributed_at?->format('Y-m-d H:i:s') ?? '—',
                        'H' => $row->goods_detail,
                        'I' => $row->remarks,
                    ];

                    $stripeFill = ($index % 2 === 0) ? 'FFFFFFFF' : 'FFF2F2F2';

                    foreach ($values as $col => $value) {
                        $cell = $sheet->getCell("{$col}{$rowNum}");
                        $cell->setValue($value);

                        $cell->getStyle()->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($stripeFill);

                        $cell->getStyle()->getFont()
                            ->setName('Calibri')->setSize(11)
                            ->getColor()->setARGB('FF000000');

                        $cell->getStyle()->getAlignment()
                            ->setVertical(Alignment::VERTICAL_CENTER);

                        $cell->getStyle()->getBorders()->getBottom()
                            ->setBorderStyle(Border::BORDER_THIN)
                            ->getColor()->setARGB('FFDEE2E8');
                    }

                    $sheet->getRowDimension($rowNum)->setRowHeight(18);
                }

                // ── Medium outline border around table (rows 5 → end) ─────────
                if ($totalRows > 0) {
                    $sheet->getStyle("A5:I{$dataEnd}")->applyFromArray([
                        'borders' => [
                            'outline' => [
                                'borderStyle' => Border::BORDER_MEDIUM,
                                'color'       => ['argb' => 'FF002EC0'],
                            ],
                        ],
                    ]);
                }

                // ── Column widths ─────────────────────────────────────────────
                foreach (['A' => 6, 'B' => 18, 'C' => 16, 'D' => 26, 'E' => 20, 'F' => 18, 'G' => 20, 'H' => 18, 'I' => 18] as $col => $w) {
                    $sheet->getColumnDimension($col)->setWidth($w);
                }

                // ── Freeze panes + page setup ─────────────────────────────────
                $sheet->freezePane('A6');
                $sheet->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
                    ->setFitToPage(true)
                    ->setFitToWidth(1)
                    ->setFitToHeight(0);
                $sheet->getHeaderFooter()
                    ->setOddHeader('&C&B MDRRMO RBI System — List of Households')
                    ->setOddFooter('&L' . $barangayName . '&RPage &P of &N');
            },
        ];
    }
}