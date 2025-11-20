<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use session;
use Throwable;

class NewResultEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function build2()
    {

        // try{
        return $this->view('emails.finance.invoice-email-template')->subject($this->details['subject'])
            ->attach($this->details['invoice_path'], [
                'as'   => $this->details['invoice_no'] . '_' . $this->details['type'] . '.pdf',
                'mime' => 'application/pdf',
            ])->when(isset($this->details['attachment_path']) && $this->details['attachment_path'] != 'N/A', function ($message) {
            $message->attach($this->details['attachment_path']);
        })
            ->with('details', $this->details)
        ;
        // } catch (Throwable $error) {
        //     Log::error('Failed to send referral status email. Error message: '.$error->getMessage());
        // }
    }
    public function build()
    {
        $email = $this->view('emails.finance.invoice-email-template')
            ->subject($this->details['subject'])
            ->with('details', $this->details)
            ->when(isset($this->details['attachment_path']) && $this->details['attachment_path'] != 'N/A', function ($message) {
                $message->attach($this->details['attachment_path']);
            });
        try {
            if (isset($this->details['invoice_path'])) {
                $email->attach($this->details['invoice_path'], [
                    'as'   => $this->details['invoice_no'] . '_' . $this->details['type'] . '.pdf',
                    'mime' => 'application/pdf',
                ]);
            } else {
                session(['error' => 'Invoice path does not exist: ' . $this->details['invoice_path']]);
                Log::error('Invoice path does not exist: ' . $this->details['invoice_path']);
            }
        } catch (Throwable $error) {
            session(['error' => 'Failed to attach invoice. Error message: ' . $error->getMessage()]);
            Log::error('Failed to attach invoice. Error message: ' . $error->getMessage());
        }

        return $email;
    }

}
