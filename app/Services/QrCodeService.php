<?php

namespace App\Services;

use App\Models\Household;
use App\Models\QrCode;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;
use Illuminate\Support\Facades\Storage;

class QrCodeService
{
    /**
     * Generate QR code for a household
     * Returns the QrCode model instance
     */
    public function generateForHousehold(Household $household): QrCode
    {
        // Household must be approved and have serial code
        if (!$household->isApproved() || !$household->serial_code) {
            throw new \Exception('Household must be approved with serial code before generating QR.');
        }

        // Check if QR already exists
        if ($household->qrCode) {
            throw new \Exception('QR code already exists for this household.');
        }

        // Generate QR code image (SVG format)
$qrSvg = QrCodeGenerator::format('svg')
            ->size(300)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($household->serial_code);

        // File name: NIC-2024-00001.svg
        $fileName = $household->serial_code . '.svg';
        $filePath = 'qrcodes/' . $fileName;

        // Save to storage/app/public/qrcodes/
        Storage::disk('public')->put($filePath, $qrSvg);

        // Create QR code record in database
        $qrCode = QrCode::create([
            'household_id' => $household->id,
            'serial_code' => $household->serial_code,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'is_active' => true,
            'reprint_count' => 0,
            'generated_by' => auth()->id(),
            'generated_at' => now(),
        ]);

        return $qrCode;
    }

    /**
     * Reprint QR code (marks as reprint and increments counter)
     */
    public function reprintQrCode(QrCode $qrCode): void
    {
        $qrCode->increment('reprint_count');
        
        // Audit log could be added here
    }

    /**
     * Get the full URL to the QR code image
     */
    public function getQrCodeUrl(QrCode $qrCode): string
    {
        return Storage::disk('public')->url($qrCode->file_path);
    }

    /**
     * Generate printable QR card HTML (for DomPDF later)
     * Returns HTML string
     */
    public function generatePrintableCard(Household $household): string
    {
        $qrCode = $household->qrCode;
        
        if (!$qrCode) {
            throw new \Exception('QR code not found for this household.');
        }

        $qrImageUrl = $this->getQrCodeUrl($qrCode);

        // 3" x 2" ID card template
        return view('qr-card', [
            'household' => $household,
            'qrImageUrl' => $qrImageUrl,
            'serialCode' => $household->serial_code,
        ])->render();
    }
}