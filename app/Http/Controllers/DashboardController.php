<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presensi;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::now()->toDateString();

        // Hitung berdasarkan jam_mulai
        $counts = [
            'hadir' => $this->countHadir($today),
            'izin' => 0, //izin via WA
        ];

        // Statistik mingguan (ambil total "hadir" per hari).
        $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $days = [];
        $max = 0;

        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i)->toDateString();

            $count = $this->countHadir($date);

            $days[] = [
                'label' => ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'][$i],
                'isoDay' => $date,
                'count' => $count,
            ];
            $max = max($max, $count);
        }

        // Data Baru
        $latest = Presensi::with(['siswa', 'tutor'])
            ->orderByDesc('tgl_presensi')
            ->orderByDesc('id')
            ->limit(5)
            ->get()
            ->map(function (Presensi $p) {

                if ($p->status === 'alpha') {
                    $p->status_label = 'ALPHA';
                    $p->status_class = 'alpha';
                } elseif ($p->foto_mulai && $p->foto_selesai) {
                    $p->status_label = 'SELESAI';
                    $p->status_class = 'hadir';
                } elseif ($p->foto_mulai) {
                    $p->status_label = 'SEDANG BERJALAN';
                    $p->status_class = 'izin';
                } else {
                    $p->status_label = 'BELUM ABSEN';
                    $p->status_class = 'pending';
                }

                return $p;
            });

        return view('admin.dashboard', [
            'today' => $today,
            'counts' => $counts,
            'weekly' => [
                'days' => $days,
                'max' => $max ?: 1,
            ],
            'latest' => $latest,
        ]);
    }

    // Hitung "hadir" berdasarkan jam_mulai (bukan status).
    private function countHadir(string $date): int
    {
        return Presensi::whereDate('tgl_presensi', $date)
            ->whereNotNull('jam_mulai')
            ->count();
    }
}
