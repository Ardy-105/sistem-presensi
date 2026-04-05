<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    //buat mass assigment
    protected $fillable =
    [
        'tutor_id',
        'siswa_id',
        'mata_pelajaran',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'lokasi_tipe',
    ];

    /**
     * sekolah = peta tetap (config lokasi.sekolah_maps_url).
     * rumah_siswa = alamat dari data siswa (kolom alamat).
     */
    public function isLokasiSekolah(): bool
    {
        return ($this->lokasi_tipe ?? 'sekolah') === 'sekolah';
    }

    public function lokasiRingkasan(): string
    {
        if (($this->lokasi_tipe ?? 'sekolah') === 'rumah_siswa') {
            $a = trim((string) ($this->siswa?->alamat ?? ''));

            return $a !== '' ? ('Rumah siswa — '.$a) : 'Rumah siswa (lengkapi alamat di data siswa)';
        }

        return (string) config('lokasi.sekolah_nama');
    }

    public function lokasiPetaUrl(): ?string
    {
        if (($this->lokasi_tipe ?? 'sekolah') === 'rumah_siswa') {
            $a = trim((string) ($this->siswa?->alamat ?? ''));
            if ($a === '') {
                return null;
            }

            return 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($a);
        }

        $url = config('lokasi.sekolah_maps_url');

        return $url ? (string) $url : null;
    }

    public function tutor()
    {
        return $this->belongsTo(Tutor::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
