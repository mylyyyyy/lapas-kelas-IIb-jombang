<?php

namespace App\Mail;

use App\Models\Kunjungan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class KunjunganStatusMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $kunjungan;
    public $qrCodePath;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Kunjungan $kunjungan, $qrCodePath = null)
    {
        $this->kunjungan = $kunjungan;
        $this->qrCodePath = $qrCodePath;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = '';
        switch ($this->kunjungan->status) {
            case 'approved':
                $subject = '✅ Tiket Kunjungan Anda Telah Disetujui';
                break;
            case 'rejected':
                $subject = '❌ Tiket Kunjungan Anda Ditolak';
                break;
            default:
                $subject = '⏳ Pendaftaran Kunjungan Anda Menunggu Persetujuan';
                break;
        }

        $email = $this->subject($subject)
                       ->view('emails.kunjungan.status');

        if ($this->qrCodePath && file_exists($this->qrCodePath)) {
            $email->attach($this->qrCodePath, [
                'as' => 'qrcode.png',
                'mime' => 'image/png',
            ]);
        }

        return $email;
    }
}