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
        'household_head_name' => 'Household Head Name',
        'national_id'       => 'Listahan ID',
        'contact_number'      => 'Contact Number',
        'email'               => 'Email',
        'street_purok'        => 'Street / Purok',
        'location'            => 'Location / Address',
        'barangay'            => 'Barangay',
        'municipality'        => 'Municipality',
        'province'            => 'Province',
        'housing_type'        => 'Housing Type',
        'housing_material'    => 'Housing Material',
        'ownership_type'      => 'Ownership Type',
        'electricity_source'  => 'Electricity Source',
        'water_source'        => 'Water Source',
        'toilet_access'       => 'Toilet Access',
        'waste_disposal'      => 'Waste Disposal',
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

    // ── Family head columns (from family_members where is_family_head = 1) ────
    const FH_COLS = [
        'fh_name'                  => 'Family Head Name',
        'fh_sex'                   => 'Sex',
        'fh_birthday'              => 'Birthday',
        'fh_civil_status'          => 'Civil Status',
        'fh_occupation'            => 'Occupation',
        'fh_educational_attainment'=> 'Educational Attainment',
    ];

    // ── Risk profile columns (from household_risk_profiles) ───────────────────
    const RISK_COLS = [
        'income_average'      => 'Monthly Income (Avg)',
        'early_warning'       => 'Early Warning System',
        'hazard_awareness'    => 'Hazard Awareness',
        'financial_assistance'=> 'Financial Assistance',
        'access_info'         => 'Access to Info',
        'relocate_willingness'=> 'Willing to Relocate',
    ];

    protected DistributionEvent $event;
    protected array $selectedLogCols;
    protected array $selectedHhCols;
    protected array $selectedFhCols;
    protected array $selectedRiskCols;
    protected ?string $barangayFilter;
    protected ?string $dateFrom;
    protected ?string $dateTo;

    protected array $headers = [];
    protected array $rows    = [];

    public function __construct(
        DistributionEvent $event,
        ?array $selectedLogCols  = [],
        ?array $selectedHhCols   = [],
        ?array $selectedFhCols   = [],
        ?array $selectedRiskCols = [],
        ?string $barangayFilter = null,
        ?string $dateFrom       = null,
        ?string $dateTo         = null
    ) {
        $selectedLogCols  = $selectedLogCols  ?? [];
        $selectedHhCols   = $selectedHhCols   ?? [];
        $selectedFhCols   = $selectedFhCols   ?? [];
        $selectedRiskCols = $selectedRiskCols ?? [];

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

        $this->selectedFhCols = !empty($selectedFhCols)
            ? array_intersect(array_keys(self::FH_COLS), $selectedFhCols)
            : array_keys(self::FH_COLS);

        $this->selectedRiskCols = !empty($selectedRiskCols)
            ? array_intersect(array_keys(self::RISK_COLS), $selectedRiskCols)
            : array_keys(self::RISK_COLS);

        $event->load([
            'logs.household.encoder',
            'logs.household.approver',
            'logs.household.riskProfile',
            'logs.household.familyMembers' => fn ($q) => $q->where('is_family_head', 1)->limit(1),
            'logs.staff',
        ]);

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
        foreach ($this->selectedFhCols as $col) {
            $this->headers[] = self::FH_COLS[$col];
        }
        foreach ($this->selectedRiskCols as $col) {
            $this->headers[] = self::RISK_COLS[$col];
        }
        // Signature column always last
        $this->headers[] = 'Signature';

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
            $row        = [];
            $hh         = $log->household;
            $familyHead = $hh?->familyMembers?->first();
            $risk       = $hh?->riskProfile;

            foreach ($this->selectedLogCols as $col) {
                $row[] = $this->getLogValue($log, $col);
            }
            foreach ($this->selectedHhCols as $col) {
                $row[] = $this->getHhValue($hh, $col);
            }
            foreach ($this->selectedFhCols as $col) {
                $row[] = $this->getFhValue($familyHead, $col);
            }
            foreach ($this->selectedRiskCols as $col) {
                $row[] = $this->getRiskValue($risk, $col);
            }
            // Blank signature cell
            $row[] = '';

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
            'household_head_name' => $hh->household_head_name ?? '—',
            'national_id'       => $hh->national_id ?? '—',
            'contact_number'      => $hh->contact_number ?? '—',
            'email'               => $hh->email ?? '—',
            'street_purok'        => $hh->street_purok ?? '—',
            'location'            => $hh->location ?? '—',
            'barangay'            => $hh->barangay ?? '—',
            'municipality'        => $hh->municipality ?? '—',
            'province'            => $hh->province ?? '—',
            'housing_type'        => $hh->housing_type ? ucwords(str_replace('_', ' ', $hh->housing_type)) : '—',
            'housing_material'    => $hh->housing_material ? ucwords(str_replace('_', ' ', $hh->housing_material)) : '—',
            'ownership_type'      => $hh->ownership_type ? ucwords(str_replace('_', ' ', $hh->ownership_type)) : '—',
            'electricity_source'  => $hh->electricity_source ? ucwords(str_replace('_', ' ', $hh->electricity_source)) : '—',
            'water_source'        => $hh->water_source ? ucwords(str_replace('_', ' ', $hh->water_source)) : '—',
            'toilet_access'       => $hh->toilet_access ? ucwords(str_replace('_', ' ', $hh->toilet_access)) : '—',
            'waste_disposal'      => $hh->waste_disposal ? ucwords(str_replace('_', ' ', $hh->waste_disposal)) : '—',
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

    private function getFhValue($fh, string $col): string
    {
        if (!$fh) return '—';

        return match($col) {
            'fh_name'                   => $fh->full_name ?? '—',
            'fh_sex'                    => $fh->sex ?? '—',
            'fh_birthday'               => $fh->birthday ? \Carbon\Carbon::parse($fh->birthday)->format('Y-m-d') : '—',
            'fh_civil_status'           => $fh->civil_status ?? '—',
            'fh_occupation'             => $fh->occupation ?? '—',
            'fh_educational_attainment' => $fh->educational_attainment ?? '—',
            default                     => '—',
        };
    }

    private function getRiskValue($risk, string $col): string
    {
        if (!$risk) return '—';

        return match($col) {
            'income_average'       => $risk->income_average !== null
                                        ? '₱' . number_format($risk->income_average, 2)
                                        : '—',
            'early_warning'        => $risk->early_warning ? 'Yes' : 'No',
            'hazard_awareness'     => $risk->hazard_awareness ? 'Yes' : 'No',
            'financial_assistance' => $risk->financial_assistance ? 'Yes' : 'No',
            'access_info'          => $risk->access_info ? 'Yes' : 'No',
            'relocate_willingness' => $risk->relocate_willingness ? 'Yes' : 'No',
            default                => '—',
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
        $this->buildFooter($sheet, $lastCol, $colCount);
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
            // Log cols
            'Serial Code'           => 22,
            'Items Received'        => 30,
            'Goods Detail'          => 28,
            'Date Distributed'      => 22,
            'Distributed By'        => 22,
            'Remarks'               => 24,
            // Household cols
            'Household Head Name'   => 28,
            'Listahan ID'           => 16,
            'Contact Number'        => 18,
            'Email'                 => 26,
            'Street / Purok'        => 22,
            'Location / Address'    => 28,
            'Barangay'              => 20,
            'Municipality'          => 18,
            'Province'              => 16,
            'Housing Type'          => 18,
            'Housing Material'      => 18,
            'Ownership Type'        => 18,
            'Electricity Source'    => 20,
            'Water Source'          => 18,
            'Toilet Access'         => 18,
            'Waste Disposal'        => 18,
            'Is 4Ps Beneficiary'    => 16,
            'Is PWD'                => 12,
            'Is Senior'             => 12,
            'Is Solo Parent'        => 14,
            'Status'                => 14,
            'Encoded By'            => 20,
            'Approved By'           => 20,
            'Created At'            => 20,
            'Updated At'            => 20,
            // Family head cols
            'Family Head Name'      => 28,
            'Sex'                   => 10,
            'Birthday'              => 14,
            'Civil Status'          => 16,
            'Occupation'            => 20,
            'Educational Attainment'=> 24,
            // Risk profile cols
            'Monthly Income (Avg)'  => 20,
            'Early Warning System'  => 20,
            'Hazard Awareness'      => 18,
            'Financial Assistance'  => 20,
            'Access to Info'        => 16,
            'Willing to Relocate'   => 18,
            // Signature
            'Signature'             => 36,
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
        $signatureColIndex = count($this->headers); // last column (1-based)

        foreach ($this->rows as $i => $row) {
            $rowNum = 8 + $i;
            $bg     = ($i % 2 === 1) ? self::BLUE_LIGHT : self::NEUTRAL;
            $sheet->getRowDimension($rowNum)->setRowHeight(22);

            foreach ($row as $ci => $value) {
                $col  = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ci + 1);
                $cell = $sheet->getCell("{$col}{$rowNum}");

                $isSignature = ($ci + 1 === $signatureColIndex);

                $botSide   = ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::BORDER_COL]];
                $rightSide = ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::BORDER_COL]];

                if ($isSignature) {
                    // Blank signature cell — wide, white bg, dashed bottom border as signing line
                    $cell->setValue('');
                    $cell->getStyle()->applyFromArray([
                        'font'      => ['size' => 9, 'name' => 'Calibri', 'color' => ['rgb' => self::MUTED]],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::WHITE]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_BOTTOM],
                        'borders'   => [
                            'bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => self::BLUE_MID]],
                            'right'  => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => self::BLUE_DARK]],
                        ],
                    ]);
                } elseif ($ci === 0) {
                    $cell->setValue($value ?? '—');
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
                    $cell->setValue($value ?? '—');
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

    private function buildFooter($sheet, string $last, int $colCount): void
    {
        $afterDataRow  = 8 + count($this->rows);

        // ── Spacer row ────────────────────────────────────────────────────────
        $spacerRow = $afterDataRow + 1;
        $sheet->getRowDimension($spacerRow)->setRowHeight(10);
        $sheet->mergeCells("A{$spacerRow}:{$last}{$spacerRow}");
        $sheet->getStyle("A{$spacerRow}")->getFill()
            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::WHITE);

        // ── Signature block ───────────────────────────────────────────────────
        // Split columns into 3 equal signature slots
        $third  = (int) floor($colCount / 3);
        $col1S  = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(1);
        $col1E  = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($third);
        $col2S  = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($third + 1);
        $col2E  = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($third * 2);
        $col3S  = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($third * 2 + 1);
        $col3E  = $last;

        $labelRow = $spacerRow + 1;
        $lineRow  = $spacerRow + 4;
        $nameRow  = $spacerRow + 5;
        $titleRow = $spacerRow + 6;

        $sheet->getRowDimension($labelRow)->setRowHeight(16);
        $sheet->getRowDimension($lineRow)->setRowHeight(22);
        $sheet->getRowDimension($nameRow)->setRowHeight(14);
        $sheet->getRowDimension($titleRow)->setRowHeight(14);

        // "Prepared by" label row
        foreach ([[$col1S,$col1E,'Prepared by:'], [$col2S,$col2E,'Noted by:'], [$col3S,$col3E,'Approved by:']] as [$cs, $ce, $lbl]) {
            $sheet->mergeCells("{$cs}{$labelRow}:{$ce}{$labelRow}");
            $sheet->setCellValue("{$cs}{$labelRow}", $lbl);
            $sheet->getStyle("{$cs}{$labelRow}")->applyFromArray([
                'font'      => ['bold' => true, 'size' => 8, 'name' => 'Calibri', 'color' => ['rgb' => self::BLUE_DARK]],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::NEUTRAL]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 2],
            ]);
        }

        // Blank rows between label and signature line (rows labelRow+1 and labelRow+2)
        foreach ([$labelRow + 1, $labelRow + 2, $labelRow + 3] as $blankRow) {
            $sheet->getRowDimension($blankRow)->setRowHeight(14);
            $sheet->mergeCells("A{$blankRow}:{$last}{$blankRow}");
            $sheet->getStyle("A{$blankRow}")->getFill()
                ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::WHITE);
        }

        // Signature line row — thick bottom border as the signing line, 3 columns
        foreach ([[$col1S,$col1E], [$col2S,$col2E], [$col3S,$col3E]] as [$cs, $ce]) {
            $sheet->mergeCells("{$cs}{$lineRow}:{$ce}{$lineRow}");
            $sheet->setCellValue("{$cs}{$lineRow}", '');
            $sheet->getStyle("{$cs}{$lineRow}")->applyFromArray([
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::WHITE]],
                'borders' => [
                    'bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => self::BLUE_DARK]],
                ],
            ]);
        }

        // "Signature over Printed Name" label
        foreach ([[$col1S,$col1E], [$col2S,$col2E], [$col3S,$col3E]] as [$cs, $ce]) {
            $sheet->mergeCells("{$cs}{$nameRow}:{$ce}{$nameRow}");
            $sheet->setCellValue("{$cs}{$nameRow}", 'Signature over Printed Name');
            $sheet->getStyle("{$cs}{$nameRow}")->applyFromArray([
                'font'      => ['italic' => true, 'size' => 8, 'name' => 'Calibri', 'color' => ['rgb' => self::MUTED]],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::WHITE]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_TOP],
            ]);
        }

        // "Designation / Position" label
        foreach ([[$col1S,$col1E], [$col2S,$col2E], [$col3S,$col3E]] as [$cs, $ce]) {
            $sheet->mergeCells("{$cs}{$titleRow}:{$ce}{$titleRow}");
            $sheet->setCellValue("{$cs}{$titleRow}", 'Designation / Position');
            $sheet->getStyle("{$cs}{$titleRow}")->applyFromArray([
                'font'      => ['italic' => true, 'size' => 8, 'name' => 'Calibri', 'color' => ['rgb' => self::MUTED]],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::WHITE]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_TOP],
            ]);
        }

        // ── System note ───────────────────────────────────────────────────────
        $noteRow   = $titleRow + 2;
        $bottomRow = $noteRow + 1;

        $sheet->getRowDimension($noteRow)->setRowHeight(18);
        $sheet->mergeCells("A{$noteRow}:{$last}{$noteRow}");
        $sheet->setCellValue("A{$noteRow}", 'This document is system-generated from the MDRRMO RBI System. For official use only.');
        $sheet->getStyle("A{$noteRow}")->applyFromArray([
            'font'      => ['italic' => true, 'size' => 8, 'name' => 'Calibri', 'color' => ['rgb' => self::MUTED]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::NEUTRAL]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => self::BLUE_MID]]],
        ]);

        $sheet->getRowDimension($bottomRow)->setRowHeight(6);
        $sheet->mergeCells("A{$bottomRow}:{$last}{$bottomRow}");
        $sheet->getStyle("A{$bottomRow}")->getFill()
            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::BLUE_DARKER);
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