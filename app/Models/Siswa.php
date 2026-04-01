<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
   //buat mass assigment
protected $table = 'siswas';

    protected $fillable = [
        'nis',
        'nama_siswa',
        'alamat',
        'no_hp',
        'nama_wali',
        'kelas'
    ];

    public function relKelas()
    {
        return $this->belongsTo(kelas::class, 'kelas');
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function presensis()
        {
            return $this->hasMany(Presensi::class);
        }
}
