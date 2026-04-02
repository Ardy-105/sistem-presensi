<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\Jadwal;
use App\Models\Tutor;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', Carbon::now()->format('Y-m'));
        $startDate = Carbon::parse($bulan . '-01')->startOfMonth();
        $endDate   = $startDate->copy()->endOfMonth();

        $hasStatus = Schema::hasColumn('presensis', 'status');
        $hasJamIn = Schema::hasColumn('presensis', 'jam_in');
        $hasJamMulai = Schema::hasColumn('presensis', 'jam_mulai');
        $baseQuery = Presensi::whereBetween('tgl_presensi', [$startDate, $endDate]);

        // Kompatibel dengan 2 skema DB: pakai status jika ada, fallback ke jam_in/jam_mulai.
        if ($hasStatus) {
            $totalHadir = (clone $baseQuery)->where('status', 'hadir')->count();
            $totalIzin = (clone $baseQuery)->where('status', 'izin')->count();
            $totalAlpha = (clone $baseQuery)->where('status', 'alpha')->count();
        } else {
            $hadirColumn = $hasJamIn ? 'jam_in' : ($hasJamMulai ? 'jam_mulai' : null);
            $totalHadir = $hadirColumn ? (clone $baseQuery)->whereNotNull($hadirColumn)->count() : 0;
            $totalIzin = 0;
            $totalAlpha = max((clone $baseQuery)->count() - $totalHadir, 0);
        }

        $totalSesi = Jadwal::whereBetween('tanggal', [$startDate, $endDate])->count();

        // Kehadiran per tutor
        $perTutor = Tutor::withCount([
            'jadwals as total_jadwal' => fn($q) => $q->whereBetween('tanggal', [$startDate, $endDate]),
        ])->having('total_jadwal', '>', 0)->orderByDesc('total_jadwal')->get();

        // Presensi terbaru
        $recentPresensi = Presensi::with(['siswa'])
            ->whereBetween('tgl_presensi', [$startDate, $endDate])
            ->orderByDesc('tgl_presensi')
            ->limit(20)
            ->get()
            ->map(function (Presensi $p) use ($hasStatus, $hasJamIn, $hasJamMulai) {
                if ($hasStatus) {
                    return $p;
                }

                $hadirValue = $hasJamIn ? $p->jam_in : ($hasJamMulai ? $p->jam_mulai : null);
                $p->status = $hadirValue ? 'hadir' : 'alpha';
                return $p;
            });

        // Data chart harian (kehadiran per hari dalam bulan)
        $dailyQuery = Presensi::selectRaw('DATE(tgl_presensi) as tgl, COUNT(*) as total')
            ->whereBetween('tgl_presensi', [$startDate, $endDate]);

        if ($hasStatus) {
            $dailyQuery->where('status', 'hadir');
        } elseif ($hasJamIn) {
            $dailyQuery->whereNotNull('jam_in');
        } elseif ($hasJamMulai) {
            $dailyQuery->whereNotNull('jam_mulai');
        } else {
            // Tidak ada kolom status/jam; kembalikan kosong agar tidak memicu chart palsu.
            $dailyQuery->whereRaw('1 = 0');
        }

        $dailyData = $dailyQuery
            ->groupBy('tgl')
            ->orderBy('tgl')
            ->pluck('total', 'tgl');

        return view('admin.laporan.index', compact(
            'totalHadir', 'totalIzin', 'totalAlpha', 'totalSesi',
            'perTutor', 'recentPresensi', 'dailyData', 'bulan', 'startDate'
        ));
    }
}
