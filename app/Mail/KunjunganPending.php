<?php

namespace App\Mail;

use App\Models\Kunjungan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class KunjunganPending extends Mailable
{
    use Queueable, SerializesModels;

    public $kunjungan;
    public $qrCodePath;

    public function __construct(Kunjungan $kunjungan, $qrCodePath)
    {
        $this->kunjungan = $kunjungan;
        $this->qrCodePath = $qrCodePath;
    }

    public function build()
    {
        return $this->subject('⏳ Pendaftaran Berhasil - Tiket Kunjungan Anda')
            ->view('emails.kunjungan_pending')
            ->attach($this->qrCodePath, [
                'as' => 'qrcode.png',
                'mime' => 'image/png',
            ]);
    }
}
