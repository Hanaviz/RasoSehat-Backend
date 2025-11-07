<?php

namespace App\Mail;

use App\Models\Restoran;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RestoranStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $restoran;
    public $pesanTambahan;

    /**
     * Create a new message instance.
     */
    public function __construct(Restoran $restoran, $pesanTambahan = null)
    {
        $this->restoran = $restoran;
        $this->pesanTambahan = $pesanTambahan;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $status = ucfirst($this->restoran->status_verifikasi);

        return $this->subject("Status Verifikasi Restoran Anda: {$status}")
                    ->view('emails.restoran_status')
                    ->with([
                        'namaRestoran' => $this->restoran->nama_restoran,
                        'status' => $status,
                        'pesanTambahan' => $this->pesanTambahan ?? 'Terima kasih telah menggunakan RasoSehat!',
                    ]);
    }
}
