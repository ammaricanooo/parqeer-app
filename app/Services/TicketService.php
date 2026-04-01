<?php

namespace App\Services;

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * Service untuk generate tiket parkir dalam format PDF menggunakan DOMPDF
 * Versi: Standar Struk Thermal 80mm - SVG QR Code
 */
class TicketService
{
    /**
     * Konfigurasi umum Dompdf
     */
    private function getPdfOptions()
    {
        return [
            'isRemoteEnabled' => true,
            'isPhpEnabled'    => true,    // Mendukung script auto-print di blade
            'dpi'             => 150,     // Optimal untuk teks struk
            'defaultFont'     => 'Courier',
        ];
    }

    /**
     * Ukuran kertas standar thermal 80mm (dalam points)
     */
    private $paperSize = [0, 0, 326.77, 553.54];

    /**
     * TIKET MASUK (ENTRY) - Preview
     */
    public function generateEntryTicketPdf(Transaction $transaction): Response
    {
        $data = [
            'transaction' => $transaction,
            'qrCodeUrl'   => $this->generateQrCodeBase64($transaction),
            'generatedAt' => now(),
        ];

        return Pdf::loadView('pdfs.ticket-entry', $data)
            ->setPaper($this->paperSize, 'portrait')
            ->setOptions($this->getPdfOptions())
            ->stream("tiket-masuk-{$transaction->id}.pdf");
    }

    /**
     * TIKET KELUAR (EXIT) - Preview
     */
    public function generateExitTicketPdf(Transaction $transaction): Response
    {
        $data = [
            'transaction'   => $transaction,
            'generatedAt'   => now(),
            'durationHours' => ceil($transaction->duration_minutes / 60),
        ];

        return Pdf::loadView('pdfs.ticket-exit', $data)
            ->setPaper($this->paperSize, 'portrait')
            ->setOptions($this->getPdfOptions())
            ->stream("tiket-keluar-{$transaction->id}.pdf");
    }

    /**
     * STRUK PEMBAYARAN (RECEIPT) - Preview
     */
    public function generatePaymentReceiptPdf(Transaction $transaction): Response
    {
        $data = [
            'transaction'   => $transaction,
            'generatedAt'   => now(),
            'durationHours' => ceil($transaction->duration_minutes / 60),
        ];

        return Pdf::loadView('pdfs.ticket-payment', $data)
            ->setPaper($this->paperSize, 'portrait')
            ->setOptions($this->getPdfOptions())
            ->stream("struk-pembayaran-{$transaction->id}.pdf");
    }

    /**
     * GENERATE QR CODE (FORMAT SVG BASE64)
     * Tidak butuh Imagick atau GD extension
     * Entry ticket QR selalu mengarah ke halaman pembayaran (fixed, tidak berubah)
     */
    private function generateQrCodeBase64(Transaction $transaction): string
    {
        // QR payload sederhana: ID + status.
        // status adalah 'out' untuk transaksi sudah dibayar/keluar, 'in' untuk belum.
        $payload = [
            'id' => $transaction->id,
            'status' => in_array($transaction->status, ['paid', 'out']) ? 'out' : 'in',
        ];

        $content = json_encode($payload);

        $svg = QrCode::format('svg')
            ->size(200)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($content);

        // Encode ke Base64 agar Dompdf bisa baca sebagai image source
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Method untuk stream langsung (Alias)
     */
    public function generateEntryTicketPdfStream(Transaction $transaction)
    {
        return $this->generateEntryTicketPdf($transaction);
    }

    public function generateExitTicketPdfStream(Transaction $transaction)
    {
        return $this->generateExitTicketPdf($transaction);
    }

    public function generatePaymentReceiptPdfStream(Transaction $transaction)
    {
        return $this->generatePaymentReceiptPdf($transaction);
    }
}
