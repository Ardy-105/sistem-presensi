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

        $counts = [
            'hadir' => $this->countStatus($today, ['HADIR']),
            'izin' => $this->countStatus($today, ['IZIN']),
        ];

        // Statistik mingguan (ambil total "hadir" per hari).
        $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $days = [];
        $max = 0;
        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i)->toDateString();
            $count = $this->countStatus($date, ['HADIR']);
            $days[] = [
                'label' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'][$i] ?? $i,
                'isoDay' => $weekStart->copy()->addDays($i)->format('Y-m-d'),
                'count' => $count,
            ];
            $max = max($max, $count);
        }

        $latest = Presensi::with(['siswa', 'tutor'])
            ->orderByDesc('tgl_presensi')
            ->orderByDesc('id')
            ->limit(5)
            ->get()
            ->map(function (Presensi $p) {
                // Dashboard hanya menampilkan 2 status: HADIR & IZIN.
                // Status lain (SAKIT/TERLAMBAT/dll) akan ditampilkan sebagai IZIN.
                $class = $this->classifyStatusForDashboard($p->status);
                $p->status_label = $class['label']; // HADIR | IZIN
                $p->status_class = $class['class']; // hadir | izin
                return $p;
            });

        // Ubah label menjadi Bahasa Indonesia supaya mirip contoh gambar.
        $indLabels = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        foreach ($days as $idx => $d) {
            $days[$idx]['label'] = $indLabels[$idx] ?? $d['label'];
        }

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

    private function countStatus(string $date, array $statuses): int
    {
        $statusesUpper = array_map(static fn($s) => strtoupper(trim($s)), $statuses);
        $query = Presensi::whereDate('tgl_presensi', $date);

        // Ambil semua baris status (karena collation DB bisa beda) lalu hitung di PHP.
        $rows = $query->get(['status']);
        $cnt = 0;
        foreach ($rows as $row) {
            $st = strtoupper(trim((string) $row->status));
            if (in_array($st, $statusesUpper, true)) {
                $cnt++;
            }
        }

        return $cnt;
    }

    private function classifyStatus(?string $status): array
    {
        $s = strtoupper(trim((string) $status));

        if (in_array($s, ['HADIR'], true)) {
            return ['label' => 'HADIR', 'class' => 'hadir'];
        }
        if (in_array($s, ['SAKIT'], true)) {
            return ['label' => 'SAKIT', 'class' => 'sakit'];
        }
        if (in_array($s, ['IZIN'], true)) {
            return ['label' => 'IZIN', 'class' => 'izin'];
        }
        if (in_array($s, ['TERLAMBAT', 'TELAT', 'LAMBAT'], true)) {
            return ['label' => 'TERLAMBAT', 'class' => 'terlambat'];
        }

        return ['label' => $s ?: 'PENDING', 'class' => ($s === 'PENDING' ? 'pending' : 'pending')];
    }

    private function classifyStatusForDashboard(?string $status): array
    {
        $s = strtoupper(trim((string) $status));

        if (in_array($s, ['HADIR'], true)) {
            return ['label' => 'HADIR', 'class' => 'hadir'];
        }

        // Semua selain HADIR ditampilkan sebagai IZIN di dashboard.
        return ['label' => 'IZIN', 'class' => 'izin'];
    }
}
