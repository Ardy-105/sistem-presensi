<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tutor\Concerns\ResolvesTutor;
use App\Models\Jadwal;
use App\Models\Presensi;
use App\Models\Tutor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class TutorDashboardController extends Controller
{
    use ResolvesTutor;

    protected function presensiQueryForTutor(Tutor $tutor)
    {
        $hasPresensiTutorId = Schema::hasColumn('presensis', 'tutor_id');
        $q = Presensi::query();

        if ($hasPresensiTutorId) {
            $q->where('tutor_id', $tutor->id);
        } else {
            $siswaIds = Jadwal::where('tutor_id', $tutor->id)->pluck('siswa_id')->unique()->filter()->values();
            if ($siswaIds->isEmpty()) {
                $q->whereRaw('1 = 0');
            } else {
                $q->whereIn('siswa_id', $siswaIds);
            }
        }

        return $q;
    }

    public function index()
    {
        $tutor = $this->resolveTutor();
        $recentPresensi = collect();
        $upcomingJadwal = collect();

        if ($tutor) {
            $tz = 'Asia/Jakarta';
            $today = Carbon::now($tz)->toDateString();

            $recentPresensi = $this->presensiQueryForTutor($tutor)
                ->with('siswa')
                ->orderByDesc('tgl_presensi')
                ->orderByDesc('id')
                ->limit(5)
                ->get();

            $upcomingJadwal = Jadwal::with('siswa')
                ->where('tutor_id', $tutor->id)
                ->whereDate('tanggal', '>=', $today)
                ->orderBy('tanggal')
                ->orderBy('jam_mulai')
                ->limit(8)
                ->get();
        }

        return view('tutor.dashboard', [
            'tutor' => $tutor,
            'recentPresensi' => $recentPresensi,
            'upcomingJadwal' => $upcomingJadwal,
        ]);
    }

    public function riwayat()
    {
        $tutor = $this->resolveTutor();
        if (!$tutor) {
            return redirect()
                ->route('tutor.dashboard')
                ->with('warning', 'Data tutor belum terhubung ke akun ini.');
        }

        $items = $this->presensiQueryForTutor($tutor)
            ->with('siswa')
            ->orderByDesc('tgl_presensi')
            ->orderByDesc('id')
            ->simplePaginate(20)
            ->withQueryString();

        return view('tutor.riwayat', [
            'tutor' => $tutor,
            'items' => $items,
        ]);
    }

    public function jadwal()
    {
        $tutor = $this->resolveTutor();
        if (!$tutor) {
            return redirect()
                ->route('tutor.dashboard')
                ->with('warning', 'Data tutor belum terhubung ke akun ini.');
        }

        $tz = 'Asia/Jakarta';
        $today = Carbon::now($tz)->toDateString();

        $items = Jadwal::with('siswa')
            ->where('tutor_id', $tutor->id)
            ->whereDate('tanggal', '>=', $today)
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->simplePaginate(25)
            ->withQueryString();

        return view('tutor.jadwal', [
            'tutor' => $tutor,
            'items' => $items,
            'today' => $today,
        ]);
    }
}
