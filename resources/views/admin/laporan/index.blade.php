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
        color: #ccd6e8;
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
        color: #a8b8cc;
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
    .stat-alpha  .statCardValue { color: #ef4444; }
    .stat-sesi   .statCardValue { color: #2563eb; }

    /* ── Section Title ── */
    .sectionTitle {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1.5px;
        color: #64748b;
        padding: 16px 16px 8px;
    }

    /* ── Chart Bars ── */
    .chartWrap {
        margin: 0 16px;
        background: #fff;
        border-radius: 14px;
        padding: 16px 14px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .chartTitle {
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 14px;
    }

    .barChart {
        display: flex;
        align-items: flex-end;
        gap: 5px;
        height: 72px;
    }

    .barCol {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
    }

    .bar {
        width: 100%;
        border-radius: 5px 5px 0 0;
        background: #dbeafe;
        min-height: 6px;
        transition: background 0.2s;
    }

    .bar.active { background: #2563eb; }

    .barDay {
        font-size: 9px;
        color: #94a3b8;
        font-weight: 600;
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

    /* ── Recent Presensi ── */
    .recentList {
        padding: 0 16px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding-bottom: 110px;
    }

    .recentRow {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #fff;
        border-radius: 12px;
        padding: 12px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }

    .recentAvatar {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #dbeafe;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
        color: #1d4ed8;
        flex-shrink: 0;
    }

    .recentInfo { flex: 1; min-width: 0; }

    .recentName {
        font-size: 13px;
        font-weight: 600;
        color: #0f172a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .recentMeta {
        font-size: 11px;
        color: #94a3b8;
        margin-top: 2px;
    }

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
            {{ $startDate->translatedFormat('F Y') }}
        </div>
    </div>

    {{-- Month Filter --}}
    <form method="GET" action="{{ route('admin.laporan.index') }}">
        <div class="monthFilter">
            <input type="month" name="bulan" class="monthInput"
                   value="{{ $bulan }}" max="{{ date('Y-m') }}">
            <button type="submit" class="filterBtn">Tampilkan</button>
        </div>
    </form>

    {{-- Summary Stats --}}
    <div class="summaryRow">
        <div class="statCard stat-hadir">
            <div class="statCardValue">{{ $totalHadir }}</div>
            <div class="statCardLabel">Hadir</div>
        </div>
        <div class="statCard stat-izin">
            <div class="statCardValue">{{ $totalIzin }}</div>
            <div class="statCardLabel">Izin</div>
        </div>
        <div class="statCard stat-alpha">
            <div class="statCardValue">{{ $totalAlpha }}</div>
            <div class="statCardLabel">Alpha</div>
        </div>
        <div class="statCard stat-sesi">
            <div class="statCardValue">{{ $totalSesi }}</div>
            <div class="statCardLabel">Sesi</div>
        </div>
    </div>

    {{-- Bar Chart Kehadiran Harian --}}
    @if($dailyData->isNotEmpty())
        <div class="sectionTitle">GRAFIK KEHADIRAN HARIAN</div>
        <div class="chartWrap">
            <div class="chartTitle">Kehadiran per Hari — {{ $startDate->translatedFormat('F Y') }}</div>
            <div class="barChart">
                @php
                    $maxVal = $dailyData->max() ?: 1;
                    $today  = date('Y-m-d');
                @endphp
                @foreach($dailyData as $tgl => $count)
                    @php
                        $height  = (int) round(($count / $maxVal) * 64);
                        $isToday = $tgl === $today;
                    @endphp
                    <div class="barCol">
                        <div class="bar {{ $isToday ? 'active' : '' }}"
                             style="height: {{ max(6, $height) }}px;"></div>
                        <div class="barDay">{{ \Carbon\Carbon::parse($tgl)->format('d') }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

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
                        <div class="tutorCardCount">{{ $tutor->total_jadwal }} sesi</div>
                    </div>
                    <div class="progressBg">
                        <div class="progressFill" style="width: {{ $pct }}%;"></div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Recent Presensi --}}
    <div class="sectionTitle">PRESENSI TERBARU</div>
    <div class="recentList">
        @forelse($recentPresensi as $presensi)
            @php
                $nama    = $presensi->siswa->nama_siswa ?? '-';
                $initial = strtoupper(substr((string)$nama, 0, 1));
                $status  = strtolower($presensi->status ?? 'alpha');
                $pillStatus = in_array($status, ['hadir','izin','alpha']) ? $status : 'alpha';
                $statusLabel = ucfirst($status);
                $tgl     = \Carbon\Carbon::parse($presensi->tgl_presensi)->translatedFormat('d M Y');
            @endphp
            <div class="recentRow">
                <div class="recentAvatar">{{ $initial }}</div>
                <div class="recentInfo">
                    <div class="recentName">{{ $nama }}</div>
                    <div class="recentMeta">{{ $tgl }}</div>
                </div>
                <div class="pill {{ $pillStatus }}">{{ $statusLabel }}</div>
            </div>
        @empty
            <div class="emptyState">Belum ada data presensi bulan ini.</div>
        @endforelse
    </div>

@endsection
