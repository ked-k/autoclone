<?php

namespace App\Jobs;

use App\Mail\Finance\NewInvoiceEmail;
use App\Models\Finance\Invoice\FmsInvoice;
use App\Models\User;
use App\Services\Finance\FmsEmailService;
use Barryvdh\DomPDF\PDF;
use Dompdf\Dompdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SendResultJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $invoice;
    protected $client;
    protected $biller;
    protected $billed;
    protected $asst_billed;
    protected $pdf;

    public function generateInvoice($invoice_id, PDF $pdf)
    {

        // return $filePath;
    }
    public function __construct($id)
    {

        $this->invoice = FmsInvoice::where(['id' => $id])->with('billtable', 'requestable', 'items', 'bank', 'approver')->first();
        $this->biller = $this->invoice?->requestable?->unit_head?->email ?? 'kedkays@gmail.com';
        if ($this->invoice->billtable_type == 'App\Models\Finance\Settings\FmsCustomer') {
            $this->billed = $this->invoice?->billtable?->email ?? 'ict.makbrc@gmail.com';
            $this->asst_billed = $this->invoice?->billable?->alt_email ?? null;
        } else {
            $this->billed = $this->invoice?->billtable?->unit_head?->email ?? 'ict.makbrc@gmail.com';
            $this->asst_billed = $this->invoice?->billtable?->unit_asst?->email ?? null;
        }
        $this->client = User::where('id', 1)->first();
    }

    public function handle()
    {

        try {
            // Generate and save the invoice PDF
            $dompdf = new Dompdf();

            $html = view('emails.finance.invoice-email', ['invoice' => $this->invoice, 'organizationInfo' => organizationInfo()])->render();
            $dompdf->loadHtml($html);
            $dompdf->set_option('isPhpEnabled', true);
            $dompdf->render();

            // Save the PDF to a file
            $pdfOutput = $dompdf->output();
            $filename = $this->invoice->invoice_no . '.pdf';
            // Create the directory if it doesn't exist

            // Save the PDF to the storage disk
            Storage::disk('local')->put('invoice/' . $filename, $pdfOutput);
            // Get the path to the saved PDF file
            $filePath = Storage::disk('local')->path('invoice/' . $filename);
            if ($this->invoice->invoice_attachments) {

                $media = $this->invoice->getFirstMedia('invoice_attachments');

                // if ($media) {
                $path = $media->getPath() ?? 'N/A'; // Get the path to the media file

                // Retrieve the original file name
                $fileName = $media->name;
                $disk = $media->disk;

                // Determine the MIME type based on the file extension
                if (Storage::disk($disk)->exists($path)) {
                    $mimeType = Storage::disk($disk)->mimeType($path);
                } else {
                    $path = $path ?? 'N/A';
                    $mimeType = 'n/a';
                }

                // Set the appropriate content type header
                $headers = [
                    'Content-Type' => $mimeType,
                ];
                // } else {
                //     session(['error' => 'Invoice can not be sent without a backup attachment']);
                //     // return redirect()->SignedRoute('finance-invoice_items', $this->invoice->invoice_no);
                // }
                // Prepare email details
            } else {
                $path = 'N/A';
            }
            $body = 'I hope you are well. Please see attached invoice number #' . $this->invoice->invoice_no . ' from ' . $this->invoice->requestable?->name . ' due on ' . $this->invoice->invoice_date;
            $details = [
                'client_name' => $this->invoice->billtable->name ?? 'N/A',
                'from' => $this->invoice->requestable->name ?? 'N/A',
                'invoice_no' => $this->invoice->invoice_no,
                'due_date' => $this->invoice->invoice_date,
                'invoice_path' => $filePath,
                'attachment_path' => $path ?? null,
                'body' => $body,
                'type' => 'INVOICE',
                'subject' => 'NEW MakBRC INVOICE ' . $this->invoice->invoice_no,
            ];

            $email = new NewInvoiceEmail($details);

            Mail::to($this->billed)
                ->cc($this->biller)
                ->when($this->asst_billed, function ($mail) {
                    $mail->cc($this->asst_billed);
                })
                ->bcc('merp@makbrc.org')
                ->send($email);

            // Remove the PDF after sending the email
            Storage::disk('local')->delete($filePath);

        } catch (Throwable $error) {
            $body = 'Failed to send invoice #' . $this->invoice?->invoice_no . ' ' . $error->getMessage();
            FmsEmailService::SendMail(14, $body);
            Log::error('Failed to send invoice email. Error message: ' . $error->getMessage());
            Log::error('Error Code: ' . $error->getCode());
            Log::error('Error Trace: ' . $error->getTraceAsString());

            // Set a session variable to indicate the download
            session()->put('invoice_download', $filePath);
        }
    }
}
