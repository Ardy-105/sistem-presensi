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
        'jam_selesai'
    ];

    public function tutor()
    {
        return $this->belongsTo(Tutor::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
