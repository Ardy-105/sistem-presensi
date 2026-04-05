<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tutor\Concerns\ResolvesTutor;
use App\Models\Jadwal;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class PresensiFotoController extends Controller
{
    use ResolvesTutor;

    public function index(Request $request)
    {
        $tutor = $this->resolveTutor();

        if (!$tutor) {
            return redirect()
                ->route('tutor.dashboard')
                ->with('warning', 'Data tutor belum terhubung ke akun ini. Hubungkan tutor ke user terlebih dahulu.');
        }

        $today = Carbon::now('Asia/Jakarta')->toDateString();

        $jadwals = Jadwal::with('siswa')
            ->where('tutor_id', $tutor->id)
            ->whereDate('tanggal', $today)
            ->orderBy('jam_mulai')
            ->get();

        $hasPresensiTutorId = Schema::hasColumn('presensis', 'tutor_id');
        $siswaIdsToday = $jadwals->pluck('siswa_id')->unique()->filter()->values();

        $presensiQuery = Presensi::query()->whereDate('tgl_presensi', $today);
        if ($hasPresensiTutorId) {
            $presensiQuery->where('tutor_id', $tutor->id);
        } elseif ($siswaIdsToday->isNotEmpty()) {
            // fallback: batasi hanya siswa yang ada di jadwal tutor hari ini
            $presensiQuery->whereIn('siswa_id', $siswaIdsToday);
        }

        $presensiToday = $presensiQuery->get()->keyBy('siswa_id');

        return view('tutor.presensi_foto', [
            'tutor' => $tutor,
            'today' => $today,
            'jadwals' => $jadwals,
            'presensiToday' => $presensiToday,
        ]);
    }

    public function store(Request $request)
    {
        $tutor = $this->resolveTutor();
        if (!$tutor) {
            return redirect()
                ->route('tutor.dashboard')
                ->with('warning', 'Data tutor belum terhubung ke akun ini.');
        }

        $validated = $request->validate([
            'siswa_id' => ['required', 'integer'],
            'mode' => ['required', Rule::in(['mulai', 'selesai'])],
            'foto' => ['required', 'image', 'max:5120'], // 5MB
            'lokasi' => ['nullable', 'string', 'max:255'],
        ]);

        $now = Carbon::now('Asia/Jakarta');
        $today = $now->toDateString();

        $jadwal = Jadwal::where('tutor_id', $tutor->id)
            ->where('siswa_id', $validated['siswa_id'])
            ->whereDate('tanggal', $today)
            ->first();

        if (!$jadwal) {
            return back()->with('warning', 'Tidak ada jadwal untuk siswa tersebut hari ini.');
        }

        $tz = 'Asia/Jakarta';
        $jamMulaiJadwal = Carbon::parse($today.' '.$jadwal->jam_mulai, $tz);
        $jamSelesaiJadwal = Carbon::parse($today.' '.$jadwal->jam_selesai, $tz);
        if ($jamSelesaiJadwal->lte($jamMulaiJadwal)) {
            $jamSelesaiJadwal->addDay();
        }

        $bukaAbsenMasuk = $jamMulaiJadwal->copy()->subMinutes(30);
        $batasAkhirSesi = $jamSelesaiJadwal->copy()->addMinutes(60);

        $dir = 'presensi/' . $tutor->id . '/' . $today;

        $hasPresensiTutorId = Schema::hasColumn('presensis', 'tutor_id');
        $presensiLookup = Presensi::query()
            ->where('siswa_id', $validated['siswa_id'])
            ->whereDate('tgl_presensi', $today);
        if ($hasPresensiTutorId) {
            $presensiLookup->where('tutor_id', $tutor->id);
        }
        $presensi = $presensiLookup->first();

        if ($validated['mode'] === 'mulai') {
            if ($presensi && $presensi->foto_mulai) {
                return back()->with('warning', 'Presensi masuk sudah tercatat untuk siswa ini.');
            }

            if ($now->lt($bukaAbsenMasuk)) {
                return back()->with('warning', 'Belum waktunya absen masuk. Absen masuk dibuka 30 menit sebelum jam mulai mengajar ('.$jamMulaiJadwal->format('H:i').' WIB).');
            }

            if ($now->gt($batasAkhirSesi)) {
                return back()->with('warning', 'Waktu presensi masuk untuk jadwal ini sudah lewat.');
            }

            $terlambat = $now->gt($jamMulaiJadwal);
            $path = $request->file('foto')->store($dir, 'public');

            if (!$presensi) {
                $presensi = new Presensi();
                if ($hasPresensiTutorId) {
                    $presensi->tutor_id = $tutor->id;
                }
                $presensi->siswa_id = (int) $validated['siswa_id'];
                $presensi->tgl_presensi = $today;
            }

            $presensi->jam_mulai = $jadwal->jam_mulai;
            $presensi->jam_selesai = $jadwal->jam_selesai;
            $presensi->foto_mulai = $path;
            $presensi->lokasi_mulai = $validated['lokasi'] ?? null;
            $presensi->status = $terlambat ? 'alpha' : 'pending';
            $presensi->save();

            $msg = $terlambat
                ? 'Presensi masuk tercatat. Status Alpha (terlambat — melewati jam mulai '.$jamMulaiJadwal->format('H:i').' WIB).'
                : 'Presensi masuk berhasil disimpan.';

            return redirect()
                ->route('tutor.presensi')
                ->with('success', $msg);
        }

        // mode selesai
        if (!$presensi || !$presensi->foto_mulai) {
            return back()->with('warning', 'Presensi pulang harus setelah presensi masuk.');
        }

        if ($presensi->foto_selesai) {
            return back()->with('warning', 'Presensi pulang sudah tercatat untuk siswa ini.');
        }

        if ($now->lt($jamSelesaiJadwal)) {
            return back()->with('warning', 'Belum waktunya presensi pulang. Pulang hanya bisa setelah jam selesai mengajar pada jadwal ('.$jamSelesaiJadwal->format('H:i').' WIB).');
        }

        if ($now->gt($batasAkhirSesi)) {
            return back()->with('warning', 'Waktu presensi pulang untuk jadwal ini sudah lewat.');
        }

        $path = $request->file('foto')->store($dir, 'public');
        $presensi->foto_selesai = $path;
        $presensi->lokasi_selesai = $validated['lokasi'] ?? null;
        $presensi->status = ($presensi->status === 'alpha') ? 'alpha' : 'hadir';
        $presensi->save();

        $msgSelesai = $presensi->status === 'alpha'
            ? 'Presensi pulang tercatat. Status tetap Alpha karena terlambat saat masuk.'
            : 'Presensi pulang berhasil disimpan.';

        return redirect()
            ->route('tutor.presensi')
            ->with('success', $msgSelesai);
    }
}

