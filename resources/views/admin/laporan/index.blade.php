@extends('layout.admin')

@section('title', 'Laporan — Admin')

<style>
    /* ── Page Header ── */
    .laporanHeader {
        background: #1a3a5c;
        padding: 16px 16px 20px;
    }

    .laporanHeaderLabel {
        font-size: 11px;
        color: #f1f5f9;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .laporanHeaderTitle {
        font-size: 18px;
        font-weight: 700;
        color: #fff;
    }

    .laporanHeaderSub {
        font-size: 12px;
        color: #cbd5e1;
        margin-top: 2px;
    }

    /* ── Month Filter ── */
    .monthFilter {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 14px 16px 0;
    }

    .monthInput {
        flex: 1;
        padding: 10px 12px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        background: #fff;
        font-size: 13px;
        color: #0f172a;
        font-family: inherit;
    }

    .monthInput:focus { outline: none; border-color: #2563eb; }

    .filterBtn {
        padding: 10px 16px;
        border-radius: 10px;
        border: none;
        background: #1a3a5c;
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
        white-space: nowrap;
    }

    /* ── Summary Cards ── */
    .summaryRow {
        display: flex;
        gap: 10px;
        padding: 14px 16px 0;
    }

    .statCard {
        flex: 1;
        background: #fff;
        border-radius: 14px;
        padding: 14px 10px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .statCardValue {
        font-size: 24px;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 4px;
    }

    .statCardLabel {
        font-size: 10px;
        font-weight: 600;
        color: #94a3b8;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .stat-hadir  .statCardValue { color: #16a34a; }
    .stat-izin   .statCardValue { color: #f59e0b; }

    /* ── Section Title ── */
    .sectionTitle {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1.5px;
        color: var(--muted);
        padding: 16px 16px 8px;
    }

    /* ── Chart Container ── */
    .chartContainer {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
        padding: 0 16px;
    }
    @media (min-width: 768px) {
        .chartContainer { grid-template-columns: 1fr; }
    }
    .chartCard {
        background: #fff;
        border-radius: 14px;
        padding: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .chartTitle {
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 16px;
    }

    /* ── Tutor List ── */
    .tutorList {
        padding: 0 16px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .tutorCard {
        background: #fff;
        border-radius: 12px;
        padding: 14px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }

    .tutorCardTop {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .tutorCardName {
        font-size: 14px;
        font-weight: 600;
        color: #0f172a;
    }

    .tutorCardCount {
        font-size: 13px;
        font-weight: 700;
        color: #2563eb;
    }

    .progressBg {
        height: 6px;
        background: #e2e8f0;
        border-radius: 3px;
    }

    .progressFill {
        height: 6px;
        border-radius: 3px;
        background: #2563eb;
    }

    /* ── Recent Presensi (Table Layout) ── */
    .recentList {
        padding: 0 16px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding-bottom: 110px;
    }

    .tableContainer {
        background: #fff;
        border-radius: 12px;
        overflow-x: auto;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        margin-bottom: 110px;
    }

    .laporanTable {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }

    .laporanTable th {
        background: #f8fafc;
        padding: 12px 10px;
        text-align: left;
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #f1f5f9;
        white-space: nowrap;
    }

    .laporanTable td {
        padding: 12px 10px;
        border-bottom: 1px solid #f1f5f9;
        color: #1e293b;
        vertical-align: middle;
    }

    .fotoStack {
        display: flex;
        gap: 4px;
    }

    .fotoThumbnail {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        object-fit: cover;
        cursor: pointer;
        transition: transform 0.2s;
        border: 1px solid #e2e8f0;
    }

    .fotoThumbnail:hover { transform: scale(1.1); }

    .fotoPlaceholder {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        border: 1px dashed #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        color: #94a3b8;
        background: #f8fafc;
    }

    .mapBtn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        background: #2563eb;
        color: #fff;
        border-radius: 8px;
        text-decoration: none;
        transition: background 0.2s;
    }

    .mapBtn:hover { background: #1d4ed8; }

    .pill {
        font-size: 10px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        letter-spacing: 0.3px;
        flex-shrink: 0;
    }

    .pill.hadir  { background: #dcfce7; color: #15803d; }
    .pill.izin   { background: #fef9c3; color: #92400e; }
    .pill.alpha  { background: #fee2e2; color: #dc2626; }
    .pill.proses { background: #e0e7ff; color: #4f46e5; }

    .emptyState {
        text-align: center;
        padding: 30px;
        color: #94a3b8;
        font-size: 13px;
    }
</style>

@section('content')

    {{-- Header --}}
    <div class="laporanHeader">
        <div class="laporanHeaderLabel">LAPORAN</div>
        <div class="laporanHeaderTitle">Rekap Presensi</div>
        <div class="laporanHeaderSub">
            {{ \Carbon\Carbon::parse($inputStartDate)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($inputEndDate)->translatedFormat('d M Y') }}
        </div>
    </div>

    {{-- Date & Advanced Filter --}}
    <form method="GET" action="{{ route('admin.laporan.index') }}">
        <div class="monthFilter" style="flex-wrap: wrap; margin-bottom: 20px;">
            <input type="date" name="start_date" class="monthInput" value="{{ $inputStartDate }}" style="flex:1; min-width: 140px;">
            <span style="font-size:12px; font-weight:bold; color: var(--muted); padding-top: 10px;">s/d</span>
            <input type="date" name="end_date" class="monthInput" value="{{ $inputEndDate }}" style="flex:1; min-width: 140px;">
            
            <select name="tutor_id" class="monthInput" style="flex:1; min-width: 140px;">
                <option value="">Semua Tutor</option>
                @foreach($tutors as $tutor)
                    <option value="{{ $tutor->id }}" {{ $tutorId == $tutor->id ? 'selected' : '' }}>{{ $tutor->nama_lengkap }}</option>
                @endforeach
            </select>
            
            <select name="siswa_id" class="monthInput" style="flex:1; min-width: 140px;">
                <option value="">Semua Siswa</option>
                @foreach($siswas as $siswa)
                    <option value="{{ $siswa->id }}" {{ $siswaId == $siswa->id ? 'selected' : '' }}>{{ $siswa->nama_siswa }}</option>
                @endforeach
            </select>

            <select name="status" class="monthInput" style="flex:1; min-width: 140px;">
                <option value="">Semua Status</option>
                <option value="hadir" {{ $statusFilter == 'hadir' ? 'selected' : '' }}>Hadir (Selesai)</option>
                <option value="proses" {{ $statusFilter == 'proses' ? 'selected' : '' }}>Sedang Berjalan</option>
                <option value="izin" {{ $statusFilter == 'izin' ? 'selected' : '' }}>Izin/Sakit</option>
            </select>

            <button type="submit" class="filterBtn" style="background: #1a3a5c; width: 100%;">Filter</button>
            <div style="display: flex; gap: 8px; width: 100%;">
                <button type="submit" formtarget="_blank" formaction="{{ route('admin.laporan.exportExcel') }}" class="filterBtn" style="background: #16a34a; flex:1;">Excel</button>
                <button type="submit" formtarget="_blank" formaction="{{ route('admin.laporan.exportPdf') }}" class="filterBtn" style="background: #dc2626; flex:1;">PDF</button>
            </div>
        </div>
    </form>

    {{-- Summary Stats --}}
    <div class="summaryRow">
        <div class="statCard stat-hadir">
            <div class="statCardValue">{{ $totalHadir }}</div>
            <div class="statCardLabel">Total Hadir</div>
        </div>
        <div class="statCard stat-izin">
            <div class="statCardValue">{{ $totalIzin }}</div>
            <div class="statCardLabel">Total Izin</div>
        </div>
    </div>

    {{-- Chart Section --}}
    <div class="sectionTitle">ANALISIS VISUAL</div>
    <div class="chartContainer">
        <div class="chartCard">
            <div class="chartTitle">Tren Kehadiran Harian</div>
            <canvas id="trendChart" style="max-height: 250px;"></canvas>
        </div>
    </div>

    {{-- Jadwal per Tutor --}}
    @if($perTutor->isNotEmpty())
        <div class="sectionTitle">JADWAL PER TUTOR</div>
        @php $maxJadwal = $perTutor->max('total_jadwal') ?: 1; @endphp
        <div class="tutorList">
            @foreach($perTutor as $tutor)
                @php
                    $pct = (int) round(($tutor->total_jadwal / $maxJadwal) * 100);
                @endphp
                <div class="tutorCard">
                    <div class="tutorCardTop">
                        <div class="tutorCardName">{{ $tutor->nama_lengkap }}</div>
                        <div class="tutorCardCount">{{ $tutor->total_jadwal }} kali</div>
                    </div>
                    <div class="progressBg">
                        <div class="progressFill" style="width: {{ $pct }}%;"></div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Recent Presensi (Table View) --}}
    <div class="sectionTitle">MONITORING PRESENSI</div>
    <div style="padding: 0 16px;">
        <div class="tableContainer">
            <table class="laporanTable">
                <thead>
                    <tr>
                        <th>NO.</th>
                        <th>TANGGAL</th>
                        <th>NAMA TUTOR</th>
                        <th>NAMA SISWA</th>
                        <th>MASUK</th>
                        <th>SELESAI</th>
                        <th>FOTO (M/S)</th>
                        <th>LOKASI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPresensi as $index => $presensi)
                        @php
                            $tutor = $presensi->tutor;
                            $tutorName = $tutor->nama_lengkap ?? 'Tutor';
                            $siswaName = $presensi->siswa->nama_siswa ?? '-';
                            $tgl = \Carbon\Carbon::parse($presensi->tgl_presensi)->format('d/m/y');
                            $jamMasuk = $presensi->jam_mulai ? \Carbon\Carbon::parse($presensi->jam_mulai)->format('H:i') : '-';
                            $jamSelesai = $presensi->jam_selesai ? \Carbon\Carbon::parse($presensi->jam_selesai)->format('H:i') : '-';
                            
                            $lokasi = $presensi->lokasi_mulai ?? '-';
                            $urlPeta = $lokasi !== '-' ? "https://www.google.com/maps/search/?api=1&query=" . urlencode($lokasi) : '#';
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $tgl }}</td>
                            <td style="font-weight: 600; color: #1a3a5c;">{{ $tutorName }}</td>
                            <td>{{ $siswaName }}</td>
                            <td style="font-weight: 700; color: #16a34a;">{{ $jamMasuk }}</td>
                            <td style="font-weight: 700; color: #2563eb;">{{ $jamSelesai }}</td>
                            <td>
                                <div class="fotoStack">
                                    @if($presensi->foto_mulai)
                                        <a href="{{ asset($presensi->foto_mulai) }}" target="_blank">
                                            <img src="{{ asset($presensi->foto_mulai) }}" class="fotoThumbnail" title="Foto Mulai">
                                        </a>
                                    @else
                                        <div class="fotoPlaceholder">M -</div>
                                    @endif

                                    @if($presensi->foto_selesai)
                                        <a href="{{ asset($presensi->foto_selesai) }}" target="_blank">
                                            <img src="{{ asset($presensi->foto_selesai) }}" class="fotoThumbnail" title="Foto Selesai">
                                        </a>
                                    @else
                                        <div class="fotoPlaceholder">S -</div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($lokasi !== '-')
                                    <a href="{{ $urlPeta }}" target="_blank" class="mapBtn" title="Lihat Peta">
                                        <ion-icon name="map-outline"></ion-icon>
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="emptyState">Belum ada data presensi periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const trendCtx = document.getElementById('trendChart').getContext('2d');
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [
                        {
                            label: 'Hadir',
                            data: {!! json_encode($chartDataHadir) !!},
                            borderColor: '#16a34a',
                            backgroundColor: 'rgba(22, 163, 74, 0.1)',
                            borderWidth: 2, fill: true, tension: 0.4
                        },
                        {
                            label: 'Izin',
                            data: {!! json_encode($chartDataIzin) !!},
                            borderColor: '#f59e0b',
                            backgroundColor: 'rgba(245, 158, 11, 0.1)',
                            borderWidth: 2, fill: true, tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'top', labels: { boxWidth: 12, font: { size: 10 } } } },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
        });
    </script>
@endsection
