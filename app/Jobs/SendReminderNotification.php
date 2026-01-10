<?php

namespace App\Jobs;

use App\Mail\VisitReminderMail;
use App\Models\Kunjungan;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SendReminderNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $kunjungan;

    /**
     * Create a new job instance.
     */
    public function __construct(Kunjungan $kunjungan)
    {
        $this->kunjungan = $kunjungan;
    }

    /**
     * Execute the job.
     */
    public function handle(WhatsAppService $whatsAppService): void
    {
        // Ensure WBP relationship is loaded for WhatsApp message
        $this->kunjungan->load('wbp');

        try {
            switch ($this->kunjungan->preferred_notification_channel) {
                case 'email':
                    if (!empty($this->kunjungan->email_pengunjung)) {
                        Mail::to($this->kunjungan->email_pengunjung)->send(new VisitReminderMail($this->kunjungan));
                        Log::info("Reminder email sent for Kunjungan ID: {$this->kunjungan->id}");
                    } else {
                        Log::warning("Kunjungan ID: {$this->kunjungan->id} has email preferred, but no email address.");
                    }
                    break;

                case 'whatsapp':
                    if (!empty($this->kunjungan->no_wa_pengunjung)) {
                        $whatsAppService->sendReminder($this->kunjungan);
                        Log::info("Reminder WhatsApp sent for Kunjungan ID: {$this->kunjungan->id}");
                    } else {
                        Log::warning("Kunjungan ID: {$this->kunjungan->id} has WhatsApp preferred, but no phone number.");
                    }
                    break;

                default:
                    Log::warning("Kunjungan ID: {$this->kunjungan->id} has no preferred notification channel or an invalid one.");
                    break;
            }
        } catch (\Exception $e) {
            Log::error("Failed to send reminder for Kunjungan ID: {$this->kunjungan->id}. Error: " . $e->getMessage());
            $this->fail($e); // Mark the job as failed
        }
    }
}
