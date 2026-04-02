<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Tutor;
use App\Models\Siswa;
use Illuminate\Http\Request;
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
        $tutors = Tutor::orderBy('nama_lengkap')->get();
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
        ], [
            'tutor_id.required'    => 'Tutor wajib dipilih.',
            'siswa_id.required'    => 'Siswa wajib dipilih.',
            'mata_pelajaran.required' => 'Mata pelajaran wajib diisi.',
            'tanggal.required'     => 'Tanggal wajib diisi.',
            'jam_mulai.required'   => 'Jam mulai wajib diisi.',
            'jam_selesai.required' => 'Jam selesai wajib diisi.',
            'jam_selesai.after'    => 'Jam selesai harus setelah jam mulai.',
        ]);

        Jadwal::create($request->only(['tutor_id', 'siswa_id', 'mata_pelajaran', 'tanggal', 'jam_mulai', 'jam_selesai']));

        return redirect()->route('admin.jadwal.index', ['tanggal' => $request->tanggal])
            ->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(Jadwal $jadwal)
    {
        $tutors = Tutor::orderBy('nama_lengkap')->get();
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
        ]);

        $jadwal->update($request->only(['tutor_id', 'siswa_id', 'mata_pelajaran', 'tanggal', 'jam_mulai', 'jam_selesai']));

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
}
