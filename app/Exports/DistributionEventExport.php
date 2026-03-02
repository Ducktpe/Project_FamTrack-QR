<?php

namespace App\Exports;

use App\Models\DistributionEvent;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class DistributionEventExport
{
    // ── Palette ───────────────────────────────────────────────────────────────
    const BLUE_DARKER = '172554';   // darkest blue — top/bottom accent stripe
    const BLUE_DARK   = '1E40AF';   // deep blue — header bg & col headers
    const BLUE_MID    = '2563EB';   // medium blue — divider, total badge, ID border
    const BLUE_LIGHT  = 'DBEAFE';   // pale blue — alternate row tint
    const NEUTRAL     = 'F0F4F8';   // cool light gray — main bg, sub-header, spacer
    const WHITE       = 'FFFFFF';
    const BODY_TXT    = '1E3A5F';
    const BORDER_COL  = '93C5FD';
    const MUTED       = '6B7280';   // footer text

    protected DistributionEvent $event;
    protected array $rows;

    public function __construct(DistributionEvent $event)
    {
        $this->event = $event;
        $event->load('logs.household', 'logs.staff');

        $this->rows = $event->logs->map(fn($log) => [
            $log->id,
            $event->event_name,
            $log->serial_code,
            $log->household?->household_head_name,
            $log->household?->barangay,
            $log->staff?->name,
            $log->distributed_at?->format('Y-m-d H:i:s'),
        ])->toArray();
    }

    // ── Public API ────────────────────────────────────────────────────────────

    public function build(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Households');
        $sheet->setShowGridlines(false);

        $this->setColumnWidths($sheet);
        $this->buildTopAccentBar($sheet);       // Row 1  — dark blue stripe
        $this->buildReportTitleRow($sheet);     // Row 2  — "Barangay Distribution Report" full width
        $this->buildMainHeader($sheet);         // Row 3  — System name | Event name | Timestamp
        $this->buildSubHeader($sheet);          // Row 4  — List of Households | Total | Barangay
        $this->buildBlueAccentDivider($sheet);  // Row 5  — medium blue divider
        $this->buildGapSpacer($sheet);          // Row 6  — neutral spacer
        $this->buildColumnHeaders($sheet);      // Row 7  — column labels
        $this->buildDataRows($sheet);           // Row 8+
        $this->buildFooter($sheet);
        $this->applyPrintSettings($sheet);

        return $spreadsheet;
    }

    public function download(string $filename): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $spreadsheet = $this->build();
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    // ── Column widths ─────────────────────────────────────────────────────────

    private function setColumnWidths($sheet): void
    {
        foreach ([
            'A' => 6,    // ID
            'B' => 18,   // Serial Code
            'C' => 28,   // Household Head
            'D' => 22,   // Barangay
            'E' => 20,   // Distributed By
            'F' => 22,   // Date & Time
        ] as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }
    }

    // ── Row 1: Top dark blue accent stripe ───────────────────────────────────

    private function buildTopAccentBar($sheet): void
    {
        $sheet->getRowDimension(1)->setRowHeight(6);
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB(self::BLUE_DARKER);
    }

    // ── Row 2: Report title — full width, standalone ──────────────────────────

    private function buildReportTitleRow($sheet): void
    {
        $sheet->getRowDimension(2)->setRowHeight(30);
        $sheet->mergeCells('A2:F2');
        $sheet->setCellValue('A2', 'Barangay Distribution Report');
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 15, 'name' => 'Calibri', 'color' => ['rgb' => self::WHITE]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::BLUE_DARK]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
    }

    // ── Row 3: System name | Event name | Timestamp ───────────────────────────
    // Equal sections: A3:B3 | C3:D3 | E3:F3

    private function buildMainHeader($sheet): void
    {
        $sheet->getRowDimension(3)->setRowHeight(22);

        $eventName = $this->event->event_name ?? '—';

        // System name (A3:B3) — left
        $sheet->mergeCells('A3:B3');
        $sheet->setCellValue('A3', 'MDRRMO RBI System');
        $sheet->getStyle('A3')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'name' => 'Calibri', 'color' => ['rgb' => self::BLUE_DARK]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::NEUTRAL]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
        ]);

        // Event name (C3:D3) — center
        $sheet->mergeCells('C3:D3');
        $sheet->setCellValue('C3', $eventName);
        $sheet->getStyle('C3')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'name' => 'Calibri', 'color' => ['rgb' => self::WHITE]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::BLUE_MID]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Exported timestamp (E3:F3) — right
        $sheet->mergeCells('E3:F3');
        $sheet->setCellValue('E3', 'Exported: ' . now()->format('F j, Y  h:i A'));
        $sheet->getStyle('E3')->applyFromArray([
            'font'      => ['size' => 9, 'name' => 'Calibri', 'color' => ['rgb' => self::BLUE_DARK]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::NEUTRAL]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
        ]);
    }

    // ── Row 4: List of Households | Total Distributed | Barangay ─────────────
    // Equal sections: A4:B4 | C4:D4 | E4:F4

    private function buildSubHeader($sheet): void
    {
        $sheet->getRowDimension(4)->setRowHeight(20);
        $total = count($this->rows);

        $barangay = $this->event->barangay
            ?? $this->event->logs->first()?->household?->barangay
            ?? '—';

        // "List of Households" (A4:B4) — left
        $sheet->mergeCells('A4:B4');
        $sheet->setCellValue('A4', 'List of Households');
        $sheet->getStyle('A4')->applyFromArray([
            'font'      => ['size' => 9, 'name' => 'Calibri', 'color' => ['rgb' => self::BLUE_DARK]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::NEUTRAL]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
        ]);

        // Total distributed (C4:D4) — center
        $sheet->mergeCells('C4:D4');
        $sheet->setCellValue('C4', "Total Distributed: {$total}");
        $sheet->getStyle('C4')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 9, 'name' => 'Calibri', 'color' => ['rgb' => self::WHITE]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::BLUE_MID]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Barangay name (E4:F4) — right
        $sheet->mergeCells('E4:F4');
        $sheet->setCellValue('E4', $barangay);
        $sheet->getStyle('E4')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 9, 'name' => 'Calibri', 'color' => ['rgb' => self::BLUE_DARK]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::NEUTRAL]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
        ]);
    }

    // ── Row 5: Blue accent divider ────────────────────────────────────────────

    private function buildBlueAccentDivider($sheet): void
    {
        $sheet->getRowDimension(5)->setRowHeight(5);
        $sheet->mergeCells('A5:F5');
        $sheet->getStyle('A5')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB(self::BLUE_MID);
    }

    // ── Row 6: Light gap spacer ───────────────────────────────────────────────

    private function buildGapSpacer($sheet): void
    {
        $sheet->getRowDimension(6)->setRowHeight(6);
        $sheet->mergeCells('A6:F6');
        $sheet->getStyle('A6')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB(self::NEUTRAL);
    }

    // ── Row 7: Column headers ─────────────────────────────────────────────────

    private function buildColumnHeaders($sheet): void
    {
        $sheet->getRowDimension(7)->setRowHeight(24);

        $headers = [
            'A' => 'ID',
            'B' => 'Serial Code',
            'C' => 'Household Head',
            'D' => 'Barangay',
            'E' => 'Distributed By',
            'F' => 'Date & Time',
        ];

        foreach ($headers as $col => $label) {
            $sheet->setCellValue("{$col}7", $label);
        }

        $sheet->getStyle('A7:F7')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'name' => 'Calibri', 'color' => ['rgb' => self::WHITE]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::BLUE_DARK]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => [
                'bottom'     => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => self::BLUE_MID]],
                'allBorders' => ['borderStyle' => Border::BORDER_THIN,   'color' => ['rgb' => self::BLUE_MID]],
            ],
        ]);
    }

    // ── Data rows ─────────────────────────────────────────────────────────────

    private function buildDataRows($sheet): void
    {
        $cols = ['A', 'B', 'C', 'D', 'E', 'F'];

        foreach ($this->rows as $i => $row) {
            $rowNum = 8 + $i;
            $bg     = ($i % 2 === 1) ? self::BLUE_LIGHT : self::NEUTRAL;

            $sheet->getRowDimension($rowNum)->setRowHeight(19);

            // Skip index 1 (event_name) — already shown in header row 3
            $filtered = array_values(array_filter(
                array_values($row),
                fn($_, $ci) => $ci !== 1,
                ARRAY_FILTER_USE_BOTH
            ));

            foreach ($filtered as $ci => $value) {
                $col  = $cols[$ci];
                $cell = $sheet->getCell("{$col}{$rowNum}");
                $cell->setValue($value ?? '—');

                $botSide   = ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::BORDER_COL]];
                $rightSide = ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::BORDER_COL]];

                if ($ci === 0) {
                    $cell->getStyle()->applyFromArray([
                        'font'      => ['bold' => true, 'size' => 9, 'name' => 'Calibri', 'color' => ['rgb' => self::WHITE]],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::BLUE_MID]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders'   => [
                            'left'   => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => self::BLUE_DARK]],
                            'bottom' => $botSide,
                            'right'  => $rightSide,
                        ],
                    ]);
                } else {
                    $cell->getStyle()->applyFromArray([
                        'font'      => ['size' => 9, 'name' => 'Calibri', 'color' => ['rgb' => self::BODY_TXT]],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
                        'borders'   => ['bottom' => $botSide, 'right' => $rightSide],
                    ]);
                }
            }
        }
    }

    // ── Footer ────────────────────────────────────────────────────────────────

    private function buildFooter($sheet): void
    {
        $footerRow = 8 + count($this->rows) + 1;
        $bottomRow = $footerRow + 1;

        $sheet->getRowDimension($footerRow)->setRowHeight(18);
        $sheet->mergeCells("A{$footerRow}:F{$footerRow}");
        $sheet->setCellValue("A{$footerRow}", 'This document is system-generated from the MDRRMO RBI System. For official use only.');
        $sheet->getStyle("A{$footerRow}")->applyFromArray([
            'font'      => ['italic' => true, 'size' => 8, 'name' => 'Calibri', 'color' => ['rgb' => self::MUTED]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::NEUTRAL]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => self::BLUE_MID]]],
        ]);

        // Bottom dark blue bar
        $sheet->getRowDimension($bottomRow)->setRowHeight(6);
        $sheet->mergeCells("A{$bottomRow}:F{$bottomRow}");
        $sheet->getStyle("A{$bottomRow}")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB(self::BLUE_DARKER);
    }

    // ── Print settings ────────────────────────────────────────────────────────

    private function applyPrintSettings($sheet): void
    {
        $sheet->freezePane('A8');
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setFitToPage(true);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 7);
    }
}