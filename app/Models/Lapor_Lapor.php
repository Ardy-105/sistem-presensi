<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lapor_Lapor extends Model
{
    //buat mass assigment
    protected $fillable = [
        'tutor_id',
        'siswa_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'alasan'
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
