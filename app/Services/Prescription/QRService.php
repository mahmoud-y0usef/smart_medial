<?php

namespace App\Services\Prescription;

use App\Models\Prescription;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

class QRService
{
    /**
     * Generate QR code for prescription
     */
    public function generateQRCode(Prescription $prescription): string
    {
        $payload = $this->createPayload($prescription);
        $signature = $this->generateSignature($prescription->id);

        $data = [
            'prescription_id' => $prescription->id,
            'prescription_number' => $prescription->prescription_number,
            'patient_id' => $prescription->patient_id,
            'doctor_id' => $prescription->doctor_id,
            'valid_until' => $prescription->valid_until->format('Y-m-d'),
            'signature' => $signature,
        ];

        // Encrypt the data
        $encrypted = encrypt(json_encode($data));

        // Generate QR code image
        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($encrypted)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size(300)
            ->margin(10)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->build();

        // Get base64 data URI
        return $result->getDataUri();
    }

    /**
     * Validate and decode QR code
     */
    public function validateQRCode(string $encryptedData): ?array
    {
        try {
            // Decrypt the data
            $decrypted = decrypt($encryptedData);
            $data = json_decode($decrypted, true);

            if (! $data) {
                return null;
            }

            // Verify signature
            if (! $this->verifySignature($data['prescription_id'], $data['signature'])) {
                return null;
            }

            // Check if prescription exists
            $prescription = Prescription::find($data['prescription_id']);
            
            if (! $prescription) {
                return null;
            }

            // Check if valid
            if ($prescription->isExpired()) {
                return null;
            }

            // Check if already dispensed
            if (config('medical.prescription.allow_multiple_dispense') === false && $prescription->isDispensed()) {
                return null;
            }

            return $data;
        } catch (\Exception $e) {
            report($e);

            return null;
        }
    }

    /**
     * Create payload for QR code
     */
    protected function createPayload(Prescription $prescription): array
    {
        return [
            'id' => $prescription->id,
            'number' => $prescription->prescription_number,
            'patient' => $prescription->patient_id,
            'doctor' => $prescription->doctor_id,
            'expires' => $prescription->valid_until->timestamp,
        ];
    }

    /**
     * Generate signature for prescription
     */
    protected function generateSignature(int $prescriptionId): string
    {
        return hash_hmac(
            config('medical.prescription.signature_algorithm', 'sha256'),
            (string) $prescriptionId,
            config('app.key')
        );
    }

    /**
     * Verify signature
     */
    protected function verifySignature(int $prescriptionId, string $signature): bool
    {
        $expected = $this->generateSignature($prescriptionId);

        return hash_equals($expected, $signature);
    }

    /**
     * Get prescription from QR data
     */
    public function getPrescriptionFromQR(string $encryptedData): ?Prescription
    {
        $data = $this->validateQRCode($encryptedData);

        if (! $data) {
            return null;
        }

        return Prescription::with(['patient', 'doctor', 'medicines.medicine'])
            ->find($data['prescription_id']);
    }
}
