<?php

namespace App\Services;

use App\Models\Payment;
use Barryvdh\DomPDF\PDF as DomPdf;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class ReceiptService
{
    // generate Receipt and return the URL that save in Storage
    //  "receipt": "https://your-app.com/storage/Payment_receipts/SCOPS_Payment_Receipt_5.pdf"
    public function generateReceipt(Payment $payment): string
    {
        try {
            $pdf = $this->buildPdf($payment);
            $fileName = $this->fileName($payment);
            $path = "Payment_receipts/{$fileName}";
            return $this->storeReceipt($pdf, $path);
        } catch (Throwable $exception) {
            report($exception);
            throw new RuntimeException('Could not generate receipt.', 0, $exception);
        }
    }
    // generate Receipt and return it as encoded pdf
    //  "receipt": "JVBERi0xLjQKJ..."
    public function generateBase64(Payment $payment): string
    {
        try {
            return base64_encode($this->buildPdf($payment)->output());
        } catch (Throwable $exception) {
            report($exception);
            throw new RuntimeException('Could not generate receipt PDF.', 0, $exception);
        }
    }
    // save the PDF in the directory and return the url if it
    public function storeReceipt(DomPdf $pdf, string $path): string
    {
        try {
            Storage::disk('public')->put($path, $pdf->output());
            return asset("storage/{$path}");
        } catch (Throwable $exception) {
            report($exception);
            throw new RuntimeException('Could not save receipt file.', 0, $exception);
        }
    }
// show the PDF in the app directly
    public function stream(Payment $payment): Response
    {
        try {
            return $this->buildPdf($payment)
                ->stream($this->fileName($payment));
        } catch (Throwable $exception) {
            report($exception);
            throw new RuntimeException('Could not stream receipt PDF.', 0, $exception);
        }
    }
// download the PDF file directly to the user device
    public function download(Payment $payment): Response
    {
        try {
            return $this->buildPdf($payment)
                ->download($this->fileName($payment));
        } catch (Throwable $exception) {
            report($exception);
            throw new RuntimeException('Could not download receipt PDF.', 0, $exception);
        }
    }
    // build  the PDF by Pdf packages and customs design
    public function buildPdf(Payment $payment): DomPdf
    {
        try {
            $payment->load(['subscription.service', 'subscription.card', 'user']);
            $card = $payment->subscription?->card
                ?? ($payment->user ? $payment->user->cards()->where('is_primary', true)->first() : null);

            return Pdf::loadView('pdf.payment_receipt', [
                'payment' => $payment,
                'user' => $payment->user,
                'subscription' => $payment->subscription,
                'card' => $card,
                'owlLogo' => $this->imageDataUri(public_path('asset/owl_cyan.png')),
                'scopsLogo' => $this->imageDataUri(public_path('asset/scops_white.png')),
                'paymentReceiptImage' => $this->imageDataUri(public_path('asset/payment_receipt.png')),
            ])->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isRemoteEnabled' => false,
                'dpi' => 150,
            ]);
        } catch (Throwable $exception) {
            report($exception);
            throw new RuntimeException('Could not build receipt PDF.', 0, $exception);
        }
    }
    // create the file name
    public function fileName(Payment $payment): string
    {
        return 'SCOPS_Payment_Receipt_' . $payment->payment_id . '.pdf';
    }

    private function imageDataUri(string $path): string
    {
        if (!is_file($path)) {
            return '';
        }

        return 'data:' . mime_content_type($path) . ';base64,' . base64_encode(file_get_contents($path));
    }
}
