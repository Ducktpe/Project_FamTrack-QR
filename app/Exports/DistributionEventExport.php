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
    const BLUE_DARKER = '172554';
    const BLUE_DARK   = '1E40AF';
    const BLUE_MID    = '2563EB';
    const BLUE_LIGHT  = 'DBEAFE';
    const NEUTRAL     = 'F0F4F8';
    const WHITE       = 'FFFFFF';
    const BODY_TXT    = '1E3A5F';
    const BORDER_COL  = '93C5FD';
    const MUTED       = '6B7280';

    // ── All available log columns ─────────────────────────────────────────────
    const LOG_COLS = [
        'serial_code'    => 'Serial Code',
        'items_received' => 'Items Received',
        'goods_detail'   => 'Goods Detail',
        'distributed_at' => 'Date Distributed',
        'distributed_by' => 'Distributed By',
        'remarks'        => 'Remarks',
    ];

    // ── All available household columns ───────────────────────────────────────
    const HH_COLS = [
        'id'                  => 'ID',
        'qr_code_path'        => 'QR Code Path',
        'household_head_name' => 'Household Head Name',
        'sex'                 => 'Sex',
        'birthday'            => 'Birthday',
        'civil_status'        => 'Civil Status',
        'contact_number'      => 'Contact Number',
        'house_number'        => 'House Number',
        'street_purok'        => 'Street / Purok',
        'barangay'            => 'Barangay',
        'municipality'        => 'Municipality',
        'province'            => 'Province',
        'listahanan_id'       => 'Listahan ID',
        'is_4ps_beneficiary'  => 'Is 4Ps Beneficiary',
        'is_pwd'              => 'Is PWD',
        'is_senior'           => 'Is Senior',
        'is_solo_parent'      => 'Is Solo Parent',
        'status'              => 'Status',
        'encoded_by'          => 'Encoded By',
        'approved_by'         => 'Approved By',
        'created_at'          => 'Created At',
        'updated_at'          => 'Updated At',
    ];

    protected DistributionEvent $event;
    protected array $selectedLogCols;
    protected array $selectedHhCols;
    protected ?string $barangayFilter;
    protected ?string $dateFrom;
    protected ?string $dateTo;

    protected array $headers = [];
    protected array $rows    = [];

    public function __construct(
        DistributionEvent $event,
        array $selectedLogCols = [],
        array $selectedHhCols  = [],
        ?string $barangayFilter = null,
        ?string $dateFrom       = null,
        ?string $dateTo         = null
    ) {
        $this->event          = $event;
        $this->barangayFilter = $barangayFilter;
        $this->dateFrom       = $dateFrom;
        $this->dateTo         = $dateTo;

        // Default: all columns if none specified
        $this->selectedLogCols = !empty($selectedLogCols)
            ? array_intersect(array_keys(self::LOG_COLS), $selectedLogCols)
            : array_keys(self::LOG_COLS);

        $this->selectedHhCols = !empty($selectedHhCols)
            ? array_intersect(array_keys(self::HH_COLS), $selectedHhCols)
            : array_keys(self::HH_COLS);

        $event->load('logs.household.encoder', 'logs.household.approver', 'logs.staff');

        $this->buildHeadersAndRows();
    }

    // ── Build headers + data rows ─────────────────────────────────────────────

    private function buildHeadersAndRows(): void
    {
        // Build header labels
        foreach ($this->selectedLogCols as $col) {
            $this->headers[] = self::LOG_COLS[$col];
        }
        foreach ($this->selectedHhCols as $col) {
            $this->headers[] = self::HH_COLS[$col];
        }

        // Filter logs
        $logs = $this->event->logs->filter(function ($log) {
            if ($this->barangayFilter && $log->household?->barangay !== $this->barangayFilter) {
                return false;
            }
            if ($this->dateFrom && $log->distributed_at?->lt(\Carbon\Carbon::parse($this->dateFrom)->startOfDay())) {
                return false;
            }
            if ($this->dateTo && $log->distributed_at?->gt(\Carbon\Carbon::parse($this->dateTo)->endOfDay())) {
                return false;
            }
            return true;
        });

        // Build data rows
        foreach ($logs as $log) {
            $row = [];

            foreach ($this->selectedLogCols as $col) {
                $row[] = $this->getLogValue($log, $col);
            }
            foreach ($this->selectedHhCols as $col) {
                $row[] = $this->getHhValue($log->household, $col);
            }

            $this->rows[] = $row;
        }
    }

    private function getLogValue($log, string $col): string
    {
        return match($col) {
            'serial_code'    => $log->serial_code ?? '—',
            'items_received' => $this->formatItems($log->items_received, $log->goods_detail),
            'goods_detail'   => $log->goods_detail ?? '—',
            'distributed_at' => $log->distributed_at
                ? $log->distributed_at->setTimezone('Asia/Manila')->format('Y-m-d h:i A')
                : '—',
            'distributed_by' => $log->staff?->name ?? ('User #' . ($log->distributed_by ?? '—')),
            'remarks'        => $log->remarks ?? '—',
            default          => '—',
        };
    }

    private function getHhValue($hh, string $col): string
    {
        if (!$hh) return '—';

        return match($col) {
            'id'                  => (string) ($hh->id ?? '—'),
            'qr_code_path'        => $hh->qr_code_path ?? '—',
            'household_head_name' => $hh->household_head_name ?? '—',
            'sex'                 => $hh->sex ?? '—',
            'birthday'            => $hh->birthday ? $hh->birthday->format('Y-m-d') : '—',
            'civil_status'        => $hh->civil_status ?? '—',
            'contact_number'      => $hh->contact_number ?? '—',
            'house_number'        => $hh->house_number ?? '—',
            'street_purok'        => $hh->street_purok ?? '—',
            'barangay'            => $hh->barangay ?? '—',
            'municipality'        => $hh->municipality ?? '—',
            'province'            => $hh->province ?? '—',
            'listahanan_id'       => $hh->listahanan_id ?? '—',
            'is_4ps_beneficiary'  => $hh->is_4ps_beneficiary ? 'Yes' : 'No',
            'is_pwd'              => $hh->is_pwd ? 'Yes' : 'No',
            'is_senior'           => $hh->is_senior ? 'Yes' : 'No',
            'is_solo_parent'      => $hh->is_solo_parent ? 'Yes' : 'No',
            'status'              => ucfirst($hh->status ?? '—'),
            'encoded_by'          => $hh->encoder?->name ?? ($hh->encoded_by ? 'User #'.$hh->encoded_by : '—'),
            'approved_by'         => $hh->approver?->name ?? ($hh->approved_by ? 'User #'.$hh->approved_by : '—'),
            'created_at'          => $hh->created_at ? $hh->created_at->format('Y-m-d H:i:s') : '—',
            'updated_at'          => $hh->updated_at ? $hh->updated_at->format('Y-m-d H:i:s') : '—',
            default               => '—',
        };
    }

    private function formatItems(?array $items, ?string $goodsDetail): string
    {
        if (!empty($items) && is_array($items)) {
            return implode(', ', array_map(
                fn($k) => ucwords(str_replace('_', ' ', $k)),
                array_keys($items)
            ));
        }
        return $goodsDetail ?? '—';
    }

    // ── Public API ────────────────────────────────────────────────────────────

    public function build(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Distribution Data');
        $sheet->setShowGridlines(false);

        $colCount = count($this->headers);
        $lastCol  = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);

        $this->setColumnWidths($sheet, $colCount);
        $this->buildTopAccentBar($sheet, $lastCol);
        $this->buildReportTitleRow($sheet, $lastCol);
        $this->buildMainHeader($sheet, $lastCol, $colCount);
        $this->buildSubHeader($sheet, $lastCol, $colCount);
        $this->buildBlueAccentDivider($sheet, $lastCol);
        $this->buildGapSpacer($sheet, $lastCol);
        $this->buildColumnHeaders($sheet, $lastCol);
        $this->buildDataRows($sheet, $colCount);
        $this->buildFooter($sheet, $lastCol);
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

    // ── Column widths (auto-size based on header label length) ────────────────

    private function setColumnWidths($sheet, int $colCount): void
    {
        $widthMap = [
            'ID' => 6, 'Serial Code' => 18, 'Household Head Name' => 28,
            'Barangay' => 20, 'Municipality' => 18, 'Province' => 16,
            'Date Distributed' => 22, 'Distributed By' => 22,
            'Items Received' => 30, 'Goods Detail' => 28,
            'Birthday' => 14, 'Contact Number' => 18,
            'House Number' => 14, 'Street / Purok' => 18,
            'Listahan ID' => 16, 'Status' => 14, 'QR Code Path' => 28,
            'Encoded By' => 20, 'Approved By' => 20,
            'Created At' => 20, 'Updated At' => 20,
            'Remarks' => 24, 'Civil Status' => 16, 'Sex' => 10,
        ];

        for ($i = 1; $i <= $colCount; $i++) {
            $col   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $label = $this->headers[$i - 1] ?? '';
            $width = $widthMap[$label] ?? max(14, strlen($label) + 4);
            $sheet->getColumnDimension($col)->setWidth($width);
        }
    }

    // ── Row builders (same branded style as original) ─────────────────────────

    private function buildTopAccentBar($sheet, string $last): void
    {
        $sheet->getRowDimension(1)->setRowHeight(6);
        $sheet->mergeCells("A1:{$last}1");
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::BLUE_DARKER);
    }

    private function buildReportTitleRow($sheet, string $last): void
    {
        $sheet->getRowDimension(2)->setRowHeight(30);
        $sheet->mergeCells("A2:{$last}2");
        $sheet->setCellValue('A2', 'Barangay Distribution Report');
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 15, 'name' => 'Calibri', 'color' => ['rgb' => self::WHITE]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::BLUE_DARK]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
    }

    private function buildMainHeader($sheet, string $last, int $colCount): void
    {
        $sheet->getRowDimension(3)->setRowHeight(22);
        $mid  = (int) ceil($colCount / 3);
        $mid2 = $mid * 2;
        $midColA = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($mid + 1);
        $midColB = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($mid2);

        // Left: system name
        $leftEnd = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($mid);
        $sheet->mergeCells("A3:{$leftEnd}3");
        $sheet->setCellValue('A3', 'MDRRMO RBI System');
        $sheet->getStyle('A3')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'name' => 'Calibri', 'color' => ['rgb' => self::BLUE_DARK]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::NEUTRAL]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
        ]);

        // Centre: event name
        $sheet->mergeCells("{$midColA}3:{$midColB}3");
        $sheet->setCellValue("{$midColA}3", $this->event->event_name);
        $sheet->getStyle("{$midColA}3")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'name' => 'Calibri', 'color' => ['rgb' => self::WHITE]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::BLUE_MID]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Right: timestamp
        $rightStart = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($mid2 + 1);
        $sheet->mergeCells("{$rightStart}3:{$last}3");
        $sheet->setCellValue("{$rightStart}3", 'Exported: ' . now()->setTimezone('Asia/Manila')->format('F j, Y  h:i A'));
        $sheet->getStyle("{$rightStart}3")->applyFromArray([
            'font'      => ['size' => 9, 'name' => 'Calibri', 'color' => ['rgb' => self::BLUE_DARK]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::NEUTRAL]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
        ]);
    }

    private function buildSubHeader($sheet, string $last, int $colCount): void
    {
        $sheet->getRowDimension(4)->setRowHeight(20);
        $mid  = (int) ceil($colCount / 3);
        $mid2 = $mid * 2;
        $midColA    = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($mid + 1);
        $midColB    = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($mid2);
        $rightStart = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($mid2 + 1);
        $leftEnd    = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($mid);

        $total    = count($this->rows);
        $barangay = $this->barangayFilter ?? ($this->event->logs->first()?->household?->barangay ?? 'All Barangays');

        $sheet->mergeCells("A4:{$leftEnd}4");
        $sheet->setCellValue('A4', 'List of Households');
        $sheet->getStyle('A4')->applyFromArray([
            'font'      => ['size' => 9, 'name' => 'Calibri', 'color' => ['rgb' => self::BLUE_DARK]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::NEUTRAL]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
        ]);

        $sheet->mergeCells("{$midColA}4:{$midColB}4");
        $sheet->setCellValue("{$midColA}4", "Total Distributed: {$total}");
        $sheet->getStyle("{$midColA}4")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 9, 'name' => 'Calibri', 'color' => ['rgb' => self::WHITE]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::BLUE_MID]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->mergeCells("{$rightStart}4:{$last}4");
        $sheet->setCellValue("{$rightStart}4", $barangay);
        $sheet->getStyle("{$rightStart}4")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 9, 'name' => 'Calibri', 'color' => ['rgb' => self::BLUE_DARK]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::NEUTRAL]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
        ]);
    }

    private function buildBlueAccentDivider($sheet, string $last): void
    {
        $sheet->getRowDimension(5)->setRowHeight(5);
        $sheet->mergeCells("A5:{$last}5");
        $sheet->getStyle('A5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::BLUE_MID);
    }

    private function buildGapSpacer($sheet, string $last): void
    {
        $sheet->getRowDimension(6)->setRowHeight(6);
        $sheet->mergeCells("A6:{$last}6");
        $sheet->getStyle('A6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::NEUTRAL);
    }

    private function buildColumnHeaders($sheet, string $last): void
    {
        $sheet->getRowDimension(7)->setRowHeight(24);

        foreach ($this->headers as $i => $label) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue("{$col}7", $label);
        }

        $sheet->getStyle("A7:{$last}7")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'name' => 'Calibri', 'color' => ['rgb' => self::WHITE]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::BLUE_DARK]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => [
                'bottom'     => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => self::BLUE_MID]],
                'allBorders' => ['borderStyle' => Border::BORDER_THIN,   'color' => ['rgb' => self::BLUE_MID]],
            ],
        ]);
    }

    private function buildDataRows($sheet, int $colCount): void
    {
        foreach ($this->rows as $i => $row) {
            $rowNum = 8 + $i;
            $bg     = ($i % 2 === 1) ? self::BLUE_LIGHT : self::NEUTRAL;
            $sheet->getRowDimension($rowNum)->setRowHeight(19);

            foreach ($row as $ci => $value) {
                $col  = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ci + 1);
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
                            'bottom' => $botSide, 'right' => $rightSide,
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

    private function buildFooter($sheet, string $last): void
    {
        $footerRow = 8 + count($this->rows) + 1;
        $bottomRow = $footerRow + 1;

        $sheet->getRowDimension($footerRow)->setRowHeight(18);
        $sheet->mergeCells("A{$footerRow}:{$last}{$footerRow}");
        $sheet->setCellValue("A{$footerRow}", 'This document is system-generated from the MDRRMO RBI System. For official use only.');
        $sheet->getStyle("A{$footerRow}")->applyFromArray([
            'font'      => ['italic' => true, 'size' => 8, 'name' => 'Calibri', 'color' => ['rgb' => self::MUTED]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::NEUTRAL]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => self::BLUE_MID]]],
        ]);

        $sheet->getRowDimension($bottomRow)->setRowHeight(6);
        $sheet->mergeCells("A{$bottomRow}:{$last}{$bottomRow}");
        $sheet->getStyle("A{$bottomRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::BLUE_DARKER);
    }

    private function applyPrintSettings($sheet): void
    {
        $sheet->freezePane('A8');
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setFitToPage(true);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 7);
    }
}