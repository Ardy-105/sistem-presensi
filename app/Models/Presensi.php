<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    //buat mass assigment
    protected $fillable =
    [
        'tutor_id',
        'siswa_id',
        'tgl_presensi',
        'jam_mulai',
        'jam_selesai',
        'foto_mulai',
        'foto_selesai',
        'lokasi_mulai',
        'lokasi_selesai',
        'status'
    ];

        public function tutor()
        {
            return $this->belongsTo(Tutor::class);
        }

        public function siswa()
        {
            return $this->belongsTo(Siswa::class);
        }

    // Method untuk laporan jumlah ngajar per periode
    public function scopeLaporanNgajar($query, $periode = 'hari', $tanggal = null)
    {
        switch ($periode) {
            case 'hari':
                return $query->whereDate('tgl_presensi', $tanggal ?? now()->toDateString());
            case 'minggu':
                return $query->whereBetween('tgl_presensi', [now()->startOfWeek(), now()->endOfWeek()]);
            case 'bulan':
                return $query->whereMonth('tgl_presensi', now()->month)->whereYear('tgl_presensi', now()->year);
            case 'tahun':
                return $query->whereYear('tgl_presensi', now()->year);
            default:
                return $query;
        }
    }

}
