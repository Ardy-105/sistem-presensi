<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Presensi;
use App\Models\Tutor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PresensiFotoController extends Controller
{
    private function resolveTutor(): ?Tutor
    {
        $user = auth()->user();
        if (!$user) {
            return null;
        }

        $tutor = Tutor::where('user_id', $user->id)->first();
        if ($tutor) {
            return $tutor;
        }

        // Coba hubungkan data tutor berdasarkan nik / email
        $nik = (string) ($user->nik ?? '');
        $email = (string) ($user->email ?? '');

        $hasTutorNik = Schema::hasColumn('tutors', 'nik');
        $hasTutorEmail = Schema::hasColumn('tutors', 'email');

        if (($hasTutorNik && $nik !== '') || ($hasTutorEmail && $email !== '')) {
            $tutor = Tutor::query()
                ->where(function ($q) use ($nik, $email, $hasTutorNik, $hasTutorEmail) {
                    if ($hasTutorNik && $nik !== '') {
                        $q->where('nik', $nik);
                    }
                    if ($hasTutorEmail && $email !== '') {
                        $q->orWhere('email', $email);
                    }
                })
                ->first();
        } else {
            $tutor = null;
        }

        if ($tutor) {
            $tutor->user_id = $user->id;
            $tutor->save();
            return $tutor;
        }

        // Jika belum ada, buat data tutor dari user (agar tutor bisa langsung presensi)
        if ($nik === '' && $email === '') {
            return null;
        }

        $name = (string) (($user->nama_lengkap ?? $user->name) ?? 'Tutor');
        if ($email === '') {
            $email = strtolower(preg_replace('/\s+/', '', $nik)) . '@local.test';
        }

        $payload = [
            'user_id' => $user->id,
            'nama_lengkap' => $name,
            'email' => $email,
            'no_hp' => $user->no_hp ?? null,
            'foto' => $user->foto ?? null,
        ];

        // Hanya isi kolom yang ada di tabel tutors
        if ($hasTutorNik) {
            $payload['nik'] = $nik !== '' ? $nik : ('TUTOR-' . $user->id);
        }

        if (!$hasTutorEmail) {
            unset($payload['email']);
        }

        return Tutor::create($payload);
    }

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

        $start = Carbon::parse($today . ' ' . $jadwal->jam_mulai, 'Asia/Jakarta')->subMinutes(30);
        $end = Carbon::parse($today . ' ' . $jadwal->jam_selesai, 'Asia/Jakarta')->addMinutes(60);
        if ($now->lt($start) || $now->gt($end)) {
            return back()->with('warning', 'Presensi hanya bisa dilakukan sesuai rentang jadwal (dengan toleransi).');
        }

        $dir = 'presensi/' . $tutor->id . '/' . $today;
        $path = $request->file('foto')->store($dir, 'public');

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
            $presensi->status = 'pending';
            $presensi->save();

            return redirect()
                ->route('tutor.presensi')
                ->with('success', 'Presensi masuk berhasil disimpan.');
        }

        // mode selesai
        if (!$presensi || !$presensi->foto_mulai) {
            // bersihkan upload yang sudah terlanjur
            Storage::disk('public')->delete($path);

            return back()->with('warning', 'Presensi pulang harus setelah presensi masuk.');
        }

        if ($presensi->foto_selesai) {
            Storage::disk('public')->delete($path);
            return back()->with('warning', 'Presensi pulang sudah tercatat untuk siswa ini.');
        }

        $presensi->foto_selesai = $path;
        $presensi->lokasi_selesai = $validated['lokasi'] ?? null;
        $presensi->status = 'hadir';
        $presensi->save();

        return redirect()
            ->route('tutor.presensi')
            ->with('success', 'Presensi pulang berhasil disimpan.');
    }
}

