<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Tutor;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->get('tanggal', Carbon::today()->toDateString());
        $selectedDate = Carbon::parse($tanggal);

        // Ambil 7 hari (Senin s.d. Sabtu minggu ini)
        $monday = $selectedDate->copy()->startOfWeek(Carbon::MONDAY);
        $weekDays = collect(range(0, 5))->map(fn($i) => $monday->copy()->addDays($i));

        // Jadwal untuk tanggal yang dipilih
        $jadwals = Jadwal::with(['tutor', 'siswa'])
            ->whereDate('tanggal', $selectedDate)
            ->orderBy('jam_mulai')
            ->get();

        // Hitung total jadwal per hari dalam minggu ini (untuk dot indicator)
        $weekCounts = Jadwal::selectRaw('DATE(tanggal) as tgl, COUNT(*) as total')
            ->whereBetween('tanggal', [$weekDays->first()->toDateString(), $weekDays->last()->toDateString()])
            ->groupBy('tgl')
            ->pluck('total', 'tgl');

        return view('admin.jadwal.index', compact('jadwals', 'selectedDate', 'weekDays', 'weekCounts'));
    }

    public function create()
    {
        $this->syncTutorsFromUsers();

        $tutors = Tutor::with('user')->get()->sortBy(fn($tutor) => $tutor->user?->nama_lengkap ?? $tutor->id);
        $siswas = Siswa::orderBy('nama_siswa')->get();
        return view('admin.jadwal.create', compact('tutors', 'siswas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tutor_id'    => 'required|exists:tutors,id',
            'siswa_id'    => 'required|exists:siswas,id',
            'mata_pelajaran' => 'required|string|max:100',
            'tanggal'     => 'required|date',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'lokasi_tipe' => ['required', Rule::in(['sekolah', 'rumah_siswa'])],
        ], [
            'tutor_id.required'    => 'Tutor wajib dipilih.',
            'siswa_id.required'    => 'Siswa wajib dipilih.',
            'mata_pelajaran.required' => 'Mata pelajaran wajib diisi.',
            'tanggal.required'     => 'Tanggal wajib diisi.',
            'jam_mulai.required'   => 'Jam mulai wajib diisi.',
            'jam_selesai.required' => 'Jam selesai wajib diisi.',
            'jam_selesai.after'    => 'Jam selesai harus setelah jam mulai.',
            'lokasi_tipe.required' => 'Lokasi mengajar wajib dipilih.',
        ]);

        Jadwal::create($request->only(['tutor_id', 'siswa_id', 'mata_pelajaran', 'tanggal', 'jam_mulai', 'jam_selesai', 'lokasi_tipe']));

        return redirect()->route('admin.jadwal.index', ['tanggal' => $request->tanggal])
            ->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(Jadwal $jadwal)
    {
        $this->syncTutorsFromUsers();

        $tutors = Tutor::with('user')->get()->sortBy(fn($tutor) => $tutor->user?->nama_lengkap ?? $tutor->id);
        $siswas = Siswa::orderBy('nama_siswa')->get();
        return view('admin.jadwal.edit', compact('jadwal', 'tutors', 'siswas'));
    }

    public function update(Request $request, Jadwal $jadwal)
    {
        $request->validate([
            'tutor_id'    => 'required|exists:tutors,id',
            'siswa_id'    => 'required|exists:siswas,id',
            'mata_pelajaran' => 'required|string|max:100',
            'tanggal'     => 'required|date',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'lokasi_tipe' => ['required', Rule::in(['sekolah', 'rumah_siswa'])],
        ]);

        $jadwal->update($request->only(['tutor_id', 'siswa_id', 'mata_pelajaran', 'tanggal', 'jam_mulai', 'jam_selesai', 'lokasi_tipe']));

        return redirect()->route('admin.jadwal.index', ['tanggal' => $jadwal->tanggal])
            ->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Jadwal $jadwal)
    {
        $tanggal = $jadwal->tanggal;
        $jadwal->delete();

        return redirect()->route('admin.jadwal.index', ['tanggal' => $tanggal])
            ->with('success', 'Jadwal berhasil dihapus.');
    }

    private function syncTutorsFromUsers(): void
    {
        $tutorUsers = User::where('role', 'tutor')->get();

        foreach ($tutorUsers as $user) {
            Tutor::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nik' => $user->nik,
                    'nama_lengkap' => $user->nama_lengkap,
                    'email' => $user->email ?? 'user'.$user->id.'@local.test',
                    'alamat' => null,
                    'no_hp' => $user->no_hp,
                    'foto' => $user->foto ?? null,
                ]
            );
        }
    }
}
