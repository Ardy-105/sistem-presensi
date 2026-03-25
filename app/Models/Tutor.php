<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tutor extends Model
{
    //buat mass assigment
    protected $fillable =
    [
        'user_id',
        'nik',
        'nama_lengkap',
        'jabatan',
        'email',
        'alamat',
        'no_hp',
        'foto',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function presensis()
    {
        return $this->hasMany(Presensi::class);
    }
    public function lapor_lapors()
    {
        return $this->hasMany(Lapor_Lapor::class);
    }
}
