<?php

namespace App\Services;

use App\Models\Household;
use App\Models\FamilyMember;
use App\Models\QrCode;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;
use Illuminate\Support\Facades\Storage;

class QrCodeService
{

    private function getLogoBase64(): string
    {
        $path = public_path('images/mdrrmo-logo.png');
        if (!file_exists($path)) return '';
        return "data:image/png;base64," . base64_encode(file_get_contents($path));
    }

    private function extractInnerSvgContent(string $svg): string
    {
        $svg = preg_replace('/<\?xml[^?]*\?>/', '', $svg);
        $svg = preg_replace('/<!DOCTYPE[^>]*>/', '', $svg);
        $svg = preg_replace('/<svg[^>]*>/', '', $svg, 1);
        $svg = str_replace('</svg>', '', $svg);
        return trim($svg);
    }

    /**
     * Display version: full QR + logo only, no info strip.
     * Saved as: qrcodes/heads/display/{serial}.svg
     */
    public function generateForFamilyHead(Household $household, FamilyMember $member): string
    {
        if (!$member->is_family_head) {
            throw new \Exception('Member is not family head.');
        }
        if (empty($household->serial_code)) {
            throw new \Exception('Household has no serial code. Ensure the household is approved before generating a QR code.');
        }

        // Each family head gets a unique FH serial derived from the household serial
        // plus the member's own ID so multiple heads in one household never collide.
        // e.g. household NIC-TB-HH-2026-00001 → heads NIC-TB-FH-2026-00001-M1, NIC-TB-FH-2026-00001-M2
        $fhBase = str_replace('-HH-', '-FH-', $household->serial_code);
        $serial = $fhBase . '-M' . $member->id;
        if (empty($serial)) {
            throw new \Exception('Failed to derive family head serial from household serial code.');
        }

        $rawQr = QrCodeGenerator::format('svg')
            ->size(300)->margin(1)->errorCorrection('H')->generate($serial);

        $styledQr = $this->styleQrSvg($rawQr, '#6D28D9', '#FFFFFF');
        $logo     = $this->getLogoBase64();
        $innerQr  = $this->extractInnerSvgContent($styledQr);

        // --- DISPLAY SVG (shown in UI): full QR, logo, header only ---
        $display  = '<svg xmlns="http://www.w3.org/2000/svg" width="360" height="420">' . "\n";
        $display .= '<rect width="360" height="420" rx="16" fill="#FFFFFF"/>' . "\n";
        // Header
        $display .= '<rect width="360" height="52" rx="16" fill="#6D28D9"/>' . "\n";
        $display .= '<rect y="36" width="360" height="16" fill="#6D28D9"/>' . "\n";
        $display .= '<text x="180" y="31" text-anchor="middle" font-family="Arial" font-size="11" font-weight="700" fill="#FFFFFF" letter-spacing="1">MDRRMO — NAIC CAVITE</text>' . "\n";
        // Full QR (300x300, centered)
        $display .= '<rect x="30" y="62" width="300" height="300" rx="12" fill="#FAF5FF"/>' . "\n";
        $display .= '<svg x="30" y="62" width="300" height="300">' . "\n" . $innerQr . "\n" . '</svg>' . "\n";
        // Logo centered over QR (72x72 white box)
        $display .= '<rect x="144" y="177" width="72" height="72" rx="12" fill="#FFFFFF" opacity="0.95"/>' . "\n";
        $display .= '<image x="148" y="181" width="64" height="64" href="' . $logo . '" />' . "\n";
        // Bottom hint
        $display .= '<text x="180" y="398" text-anchor="middle" font-family="Arial" font-size="9" fill="#A78BFA">Scan to identify household</text>' . "\n";
        $display .= '</svg>';

        // --- DOWNLOAD SVG (full card with info strip) ---
        $download = $this->buildFamilyHeadDownloadCard($innerQr, $logo, $serial, $member, $household);

        $displayPath  = 'qrcodes/heads/' . $serial . '.svg';
        $downloadPath = 'qrcodes/heads/download/' . $serial . '.svg';

        Storage::disk('public')->put($displayPath, $display);
        Storage::disk('public')->put($downloadPath, $download);

        return $displayPath;
    }

    private function buildFamilyHeadDownloadCard(
        string $innerQr,
        string $logo,
        string $serial,
        FamilyMember $member,
        Household $household
    ): string {
        $date         = date('M d, Y');
        $name         = htmlspecialchars($member->full_name, ENT_XML1);
        $barangay     = htmlspecialchars($household->barangay, ENT_XML1);
        $municipality = htmlspecialchars($household->municipality ?? 'Naic', ENT_XML1);

        $svg  = '<svg xmlns="http://www.w3.org/2000/svg" width="360" height="520">' . "\n";
        $svg .= '<rect width="360" height="520" rx="16" fill="#FFFFFF"/>' . "\n";
        // Header
        $svg .= '<rect width="360" height="52" rx="16" fill="#6D28D9"/>' . "\n";
        $svg .= '<rect y="36" width="360" height="16" fill="#6D28D9"/>' . "\n";
        $svg .= '<text x="180" y="31" text-anchor="middle" font-family="Arial" font-size="11" font-weight="700" fill="#FFFFFF" letter-spacing="1">MDRRMO — NAIC CAVITE</text>' . "\n";
        // Full QR
        $svg .= '<rect x="30" y="62" width="300" height="300" rx="12" fill="#FAF5FF"/>' . "\n";
        $svg .= '<svg x="30" y="62" width="300" height="300">' . "\n" . $innerQr . "\n" . '</svg>' . "\n";
        // Logo
        $svg .= '<rect x="144" y="177" width="72" height="72" rx="12" fill="#FFFFFF" opacity="0.95"/>' . "\n";
        $svg .= '<image x="148" y="181" width="64" height="64" href="' . $logo . '" />' . "\n";
        // Serial pill
        $svg .= '<rect x="40" y="372" width="280" height="30" rx="15" fill="#F5F3FF"/>' . "\n";
        $svg .= '<text x="180" y="392" text-anchor="middle" font-family="Courier" font-size="13" font-weight="bold" fill="#6D28D9">' . $serial . '</text>' . "\n";
        // Name + location
        $svg .= '<text x="180" y="421" text-anchor="middle" font-family="Arial" font-size="14" font-weight="bold" fill="#1F1235">' . $name . '</text>' . "\n";
        $svg .= '<text x="180" y="440" text-anchor="middle" font-family="Arial" font-size="11" fill="#6B7280">' . $barangay . ', ' . $municipality . '</text>' . "\n";
        // Footer bar
        $svg .= '<rect x="0" y="460" width="360" height="44" rx="16" fill="#6D28D9"/>' . "\n";
        $svg .= '<rect x="0" y="460" width="360" height="16" fill="#6D28D9"/>' . "\n";
        $svg .= '<text x="180" y="486" text-anchor="middle" font-family="Arial" font-size="10" fill="#FFFFFF">Emergency Response QR — Family Head</text>' . "\n";
        // Generated date
        $svg .= '<text x="180" y="452" text-anchor="middle" font-family="Arial" font-size="9" fill="#9CA3AF">Generated ' . $date . '</text>' . "\n";
        // Side watermark
        $svg .= '<text x="10" y="290" transform="rotate(-90 10,290)" font-size="7" fill="#D1D5DB">' . $serial . '</text>' . "\n";
        $svg .= '</svg>';

        return $svg;
    }

    /**
     * Household QR — display version saved normally, download version in /download/
     */
    public function generateForHousehold(Household $household): QrCode
    {
        $household->refresh();

        if (!$household->isApproved() || empty($household->serial_code)) {
            throw new \Exception('Household must be approved and have a serial code before generating a QR.');
        }
        if ($household->qrCode) {
            throw new \Exception('QR already exists.');
        }

        $rawQr = QrCodeGenerator::format('svg')
            ->size(300)->margin(1)->errorCorrection('H')->generate($household->serial_code);

        $styledQr = $this->styleQrSvg($rawQr, '#1B3F7A', '#FFFFFF');
        $logo     = $this->getLogoBase64();
        $innerQr  = $this->extractInnerSvgContent($styledQr);

        // Display SVG
        $display  = '<svg xmlns="http://www.w3.org/2000/svg" width="360" height="420">' . "\n";
        $display .= '<rect width="360" height="420" rx="16" fill="#FFFFFF"/>' . "\n";
        $display .= '<rect width="360" height="52" rx="16" fill="#1B3F7A"/>' . "\n";
        $display .= '<rect y="36" width="360" height="16" fill="#1B3F7A"/>' . "\n";
        $display .= '<text x="180" y="31" text-anchor="middle" font-family="Arial" font-size="11" font-weight="700" fill="#FFFFFF" letter-spacing="1">MDRRMO — NAIC CAVITE</text>' . "\n";
        $display .= '<rect x="30" y="62" width="300" height="300" rx="12" fill="#EAF0FA"/>' . "\n";
        $display .= '<svg x="30" y="62" width="300" height="300">' . "\n" . $innerQr . "\n" . '</svg>' . "\n";
        $display .= '<rect x="144" y="177" width="72" height="72" rx="12" fill="#FFFFFF" opacity="0.95"/>' . "\n";
        $display .= '<image x="148" y="181" width="64" height="64" href="' . $logo . '" />' . "\n";
        $display .= '<text x="180" y="398" text-anchor="middle" font-family="Arial" font-size="9" fill="#6B8DB5">Scan to identify household</text>' . "\n";
        $display .= '</svg>';

        // Download SVG (with full info)
        $download = $this->buildHouseholdDownloadCard(
            $innerQr, $logo,
            $household->serial_code,
            $household->household_head_name,
            $household->barangay,
            $household->municipality ?? 'Naic'
        );

        $displayPath  = 'qrcodes/' . $household->serial_code . '.svg';
        $downloadPath = 'qrcodes/download/' . $household->serial_code . '.svg';

        Storage::disk('public')->put($displayPath, $display);
        Storage::disk('public')->put($downloadPath, $download);

        $qrCode = QrCode::create([
            'household_id'  => $household->id,
            'serial_code'   => $household->serial_code,
            'file_path'     => $displayPath,
            'file_name'     => $household->serial_code . '.svg',
            'is_active'     => true,
            'reprint_count' => 0,
            'generated_by'  => auth()->id(),
            'generated_at'  => now()
        ]);

        return $qrCode;
    }

    private function buildHouseholdDownloadCard(
        string $innerQr,
        string $logo,
        string $serial,
        string $name,
        string $barangay,
        string $municipality
    ): string {
        $date         = date('M d, Y');
        $name         = htmlspecialchars($name, ENT_XML1);
        $barangay     = htmlspecialchars($barangay, ENT_XML1);
        $municipality = htmlspecialchars($municipality, ENT_XML1);

        $svg  = '<svg xmlns="http://www.w3.org/2000/svg" width="360" height="520">' . "\n";
        $svg .= '<rect width="360" height="520" rx="16" fill="#FFFFFF"/>' . "\n";
        // Header gradient effect (two rects)
        $svg .= '<rect width="360" height="52" rx="16" fill="#1B3F7A"/>' . "\n";
        $svg .= '<rect y="36" width="360" height="16" fill="#1B3F7A"/>' . "\n";
        $svg .= '<text x="180" y="31" text-anchor="middle" font-family="Arial" font-size="11" font-weight="700" fill="#FFFFFF" letter-spacing="1">MDRRMO — NAIC CAVITE</text>' . "\n";
        // Full QR
        $svg .= '<rect x="30" y="62" width="300" height="300" rx="12" fill="#EAF0FA"/>' . "\n";
        $svg .= '<svg x="30" y="62" width="300" height="300">' . "\n" . $innerQr . "\n" . '</svg>' . "\n";
        // Logo
        $svg .= '<rect x="144" y="177" width="72" height="72" rx="12" fill="#FFFFFF" opacity="0.95"/>' . "\n";
        $svg .= '<image x="148" y="181" width="64" height="64" href="' . $logo . '" />' . "\n";
        // Serial pill
        $svg .= '<rect x="40" y="372" width="280" height="30" rx="15" fill="#EAF0FA"/>' . "\n";
        $svg .= '<text x="180" y="392" text-anchor="middle" font-family="Courier" font-size="13" font-weight="bold" fill="#1B3F7A">' . $serial . '</text>' . "\n";
        // Name + location
        $svg .= '<text x="180" y="421" text-anchor="middle" font-family="Arial" font-size="14" font-weight="bold" fill="#111827">' . $name . '</text>' . "\n";
        $svg .= '<text x="180" y="440" text-anchor="middle" font-family="Arial" font-size="11" fill="#6B7280">' . $barangay . ', ' . $municipality . '</text>' . "\n";
        // Footer bar
        $svg .= '<rect x="0" y="460" width="360" height="44" rx="16" fill="#E11D48"/>' . "\n";
        $svg .= '<rect x="0" y="460" width="360" height="16" fill="#E11D48"/>' . "\n";
        $svg .= '<text x="180" y="486" text-anchor="middle" font-family="Arial" font-size="10" fill="#FFFFFF">Emergency Response QR Identification</text>' . "\n";
        // Generated date
        $svg .= '<text x="180" y="452" text-anchor="middle" font-family="Arial" font-size="9" fill="#9CA3AF">Generated ' . $date . '</text>' . "\n";
        // Side watermark
        $svg .= '<text x="10" y="290" transform="rotate(-90 10,290)" font-size="7" fill="#D1D5DB">' . $serial . '</text>' . "\n";
        $svg .= '</svg>';

        return $svg;
    }

    private function styleQrSvg(string $rawSvg, string $moduleColor, string $bgColor): string
    {
        $svg = str_replace('#000000', $moduleColor, $rawSvg);
        $svg = str_replace('#ffffff', $bgColor, $svg);
        $svg = str_replace('<rect', '<rect rx="1.5" ry="1.5"', $svg);
        return $svg;
    }
}