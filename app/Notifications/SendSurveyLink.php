<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendSurveyLink extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable (Instance dari Model Kunjungan)
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // Generate Link Survey
        // Pastikan parameter 'kunjungan_id' ini ditangkap oleh SurveyController Anda
        $surveyUrl = route('survey.create', [
            'kunjungan_id' => $notifiable->id,
            // 'token' => $notifiable->qr_token // Gunakan ini jika controller Anda butuh token
        ]);

        return (new MailMessage)
            ->subject('Survei Kepuasan Layanan Kunjungan Lapas Jombang')
            ->greeting('Halo ' . $notifiable->nama_pengunjung . ',') // FIX: nama_pengunjung
            ->line('Terima kasih atas kunjungan Anda di Lapas Kelas IIB Jombang. Kunjungan Anda telah selesai.')
            ->line('Kami mohon kesediaan Anda untuk mengisi survei kepuasan layanan kami melalui tautan di bawah ini.')
            ->line('Partisipasi Anda sangat berarti untuk peningkatan kualitas layanan kami.')
            ->action('Isi Survei Sekarang', $surveyUrl)
            ->line('Terima kasih atas waktu dan perhatian Anda.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
