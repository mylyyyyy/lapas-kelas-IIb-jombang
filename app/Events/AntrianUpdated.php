<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AntrianUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sesi;
    public $nomor_terpanggil;

    /**
     * Create a new event instance.
     *
     * @param string $sesi
     * @param int $nomor_terpanggil
     * @return void
     */
    public function __construct(string $sesi, int $nomor_terpanggil)
    {
        $this->sesi = $sesi;
        $this->nomor_terpanggil = $nomor_terpanggil;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('antrian-channel');
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'antrian.updated';
    }
}
